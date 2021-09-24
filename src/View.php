<?php
declare (strict_types = 1);
namespace Lemuria\Renderer\Text;

use JetBrains\PhpStorm\Pure;

use function Lemuria\getClass;
use function Lemuria\number as formatNumber;
use Lemuria\Engine\Fantasya\Census;
use Lemuria\Engine\Fantasya\Effect\Hunger;
use Lemuria\Engine\Fantasya\Effect\SpyEffect;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Engine\Fantasya\Outlook;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Engine\Message;
use Lemuria\Engine\Message\Filter;
use Lemuria\Identifiable;
use Lemuria\ItemSet;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Commodity;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\Fantasya\World\PartyMap;
use Lemuria\Singleton;
use Lemuria\Version;

/**
 * A view object that contains variables and helper functions for view scripts.
 */
abstract class View
{
	public Census $census;

	public Outlook $outlook;

	public TravelAtlas $atlas;

	public PartyMap $map;

	protected Dictionary $dictionary;

	protected array $spyEffect;

	protected ?array $variables = null;

	public function __construct(public Party $party, private Filter $messageFilter) {
		$this->census  = new Census($this->party);
		$this->outlook = new Outlook($this->census);
		$this->atlas   = new TravelAtlas($this->party);
		$this->atlas->forRound(Lemuria::Calendar()->Round() - 1);
		$this->map        = new PartyMap(Lemuria::World(), $this->party);
		$this->dictionary = new Dictionary();
		$this->spyEffect  = $this->getSpyEffect();
	}

	/**
	 * Get a string.
	 */
	#[Pure] public function get(string $keyPath, $index = null): string {
		return $this->dictionary->get($keyPath, $index);
	}

	public function toAndString(array $list): string {
		if (empty($list)) {
			return '';
		}
		if (count($list) === 1) {
			return (string)$list[0];
		}
		$last = array_pop($list);
		return implode(', ', $list) . ' und ' . $last;
	}

	/**
	 * Format a number with optional string.
	 */
	#[Pure] public function number(int|float $number, ?string $keyPath = null, ?Singleton $singleton = null, string $delimiter = ' '): string {
		if ($keyPath) {
			if ($singleton) {
				$keyPath .= '.' . getClass($singleton);
			}
			$index = $number == 1 ? 0 : 1;
			return formatNumber($number) . $delimiter . $this->get($keyPath, $index);
		}
		return formatNumber($number);
	}

	#[Pure] public function things(Commodity $commodity): string {
		$keyPath = 'resource.' . getClass($commodity);
		return $this->get($keyPath, 1);
	}

	/**
	 * Format a Quantity.
	 */
	#[Pure] public function resource(Quantity $item, string $keyPath = 'resource'): string {
		return $this->number($item->Count(), $keyPath, $item->getObject());
	}

	/**
	 * Get an Item from a set.
	 *
	 * @noinspection PhpPureFunctionMayProduceSideEffectsInspection
	 */
	#[Pure] public function item(string $class, ItemSet $set, string $keyPath = 'resource'): string {
		$item = $set[$class];
		return $this->number($item->Count(), $keyPath, $item->getObject());
	}

	/**
	 * Get a list of items from a set.
	 */
	public function items(array $classes, ItemSet $set, string $keyPath = 'resource'): string {
		$items = [];
		foreach ($classes as $class) {
			if (isset($set[$class])) {
				$items[] = $this->item($class, $set, $keyPath);
			}
		}
		return $this->toAndString($items);
	}

	/**
	 * Get all neighbours of a region.
	 *
	 * @return string[]
	 */
	public function neighbours(?Region $region): array {
		$neighbours = [];
		$roads      = $region->Roads();
		foreach ($this->map->getNeighbours($region)->getAll() as $direction => $neighbour) {
			if ($neighbour) {
				if ($region->hasRoad($direction)) {
					$predicate = ' führt eine Straße ';
					$neighbour = $this->neighbour($neighbour, true);
				} elseif ($roads && $roads[$direction] > 0.0) {
					$percent   = (int)round(100.0 * $roads[$direction]);
					$predicate = ' führt eine Straße (' . $percent . '% fertig) ';
					$neighbour = $this->neighbour($neighbour, true);
				} else {
					$predicate = ' liegt ';
					$neighbour = $this->neighbour($neighbour);
				}
				$neighbours[] = 'im ' . $this->get('world', $direction) . $predicate . $neighbour;
			}
		}
		$n = count($neighbours);
		if ($n > 1) {
			$neighbours[$n - 2] .= ' und ' . $neighbours[$n - 1];
			unset($neighbours[$n - 1]);
		}
		return $neighbours;
	}

	/**
	 * Get a neighbour description.
	 */
	#[Pure] public function neighbour(Region $region = null, bool $hasRoad = false): string {
		$landscape = $region->Landscape();
		$article   = $this->get('article', $landscape);
		if ($hasRoad) {
			$preposition = $this->get('preposition.zu', $article);
			$text        = $preposition . ' ' . $this->get('landscape', $landscape);
		} else {
			$text = $article . ' ' . $this->get('landscape', $landscape);
		}
		if ($region->Name() && !($landscape instanceof Ocean && $region->Name() === 'Ozean')) {
			$text .= ' ' . $region->Name();
		}
		return $text;
	}

	#[Pure] public function people(Construction|Vessel $entity): int {
		$count  = 0;
		$people = $entity instanceof Construction ? $entity->Inhabitants() : $entity->Passengers();
		foreach ($people as $unit /* @var Unit $unit */) {
			$count += $unit->Size();
		}
		return $count;
	}

	/**
	 * @return Message[]
	 */
	public function messages(Identifiable $entity): array {
		$messages = [];
		foreach (Lemuria::Report()->getAll($entity) as $message) {
			if (!$this->messageFilter->retains($message)) {
				$messages[] = $message;
			}
		}
		return $messages;
	}

	public function hostilities(): array {
		return Lemuria::Hostilities()->findFor($this->party);
	}

	#[Pure] public function relation(Relation $relation): string {
		$agreement = $relation->Agreement();
		if ($agreement === Relation::NONE || $agreement === Relation::ALL) {
			$agreement = 'agreement_' . $agreement;
			return $this->dictionary->get('diplomacy.relation', $agreement);
		}

		$agreements = [];
		$i = 1;
		while ($i < Relation::ALL) {
			if ($relation->has($i)) {
				$agreement   = 'agreement_' . $i;
				$agreements[] = $this->dictionary->get('diplomacy.relation', $agreement);
			}
			$i *= 2;
		}
		return implode(', ', $agreements);
	}

	#[Pure] public function spyLevel(Unit $unit): int {
		return $this->spyEffect[$unit->Id()->Id()] ?? 0;
	}

	#[Pure] public function battleRow(Unit $unit): string {
		return $this->dictionary->get('battleRow', $unit->BattleRow());
	}

	public function health(Unit $unit): string {
		$effect = new Hunger(new State());
		if (Lemuria::Score()->find($effect->setUnit($unit))) {
			$key = 'hunger';
		} else {
			$key = 'default';
		}
		return $this->dictionary->get('health.' . $key, $this->healthStage($unit));
	}

	#[Pure] public function healthMark(Unit $unit): string {
		return match ($this->healthStage($unit)) {
			1       => '⚔',
			2       => '✝',
			3       => '†',
			4       => '🕇',
			default => ''
		};
	}

	#[Pure] public function healthStage(Unit $unit): int {
		$health = $unit->Health();
		return match (true) {
			$health < 0.25 => 4,
			$health < 0.5  => 3,
			$health < 0.75 => 2,
			$health < 1.0  => 1,
			default        => 0
		};
	}

	public function gameVersions(): array {
		$version  = Lemuria::Version();
		$versions = [];
		foreach ($version[Version::BASE] as $versionTag) {
			$versions[] = $versionTag->name . ': ' . $versionTag->version;
		}
		foreach ($version[Version::MODEL] as $versionTag) {
			$versions[] = $versionTag->name . ': ' . $versionTag->version;
		}
		foreach ($version[Version::ENGINE] as $versionTag) {
			$versions[] = $versionTag->name . ': ' . $versionTag->version;
		}
		foreach ($version[Version::RENDERERS] as $versionTag) {
			$versions[] = $versionTag->name . ': ' . $versionTag->version;
		}
		return $versions;
	}

	/**
	 * Render a template.
	 */
	abstract public function template(string $name, mixed ...$variables): string;

	/**
	 * Render a report message.
	 */
	abstract public function message(Message $message): string;

	/**
	 * Generate the template output.
	 */
	public function generate(): string {
		if (!ob_start()) {
			throw new \RuntimeException('Could not start output buffering.');
		}
		return $this->generateContent('main');
	}

	abstract protected function generateContent(string $template): string;

	private function getSpyEffect(): array {
		$effect = new SpyEffect(State::getInstance());
		/** @var SpyEffect $effect */
		$effect = Lemuria::Score()->find($effect->setParty($this->party));
		return $effect?->Targets() ?? [];
	}
}
