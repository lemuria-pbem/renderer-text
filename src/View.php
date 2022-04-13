<?php
declare (strict_types = 1);
namespace Lemuria\Renderer\Text;

use JetBrains\PhpStorm\Pure;

use function Lemuria\getClass;
use function Lemuria\number as formatNumber;
use Lemuria\Engine\Fantasya\Census;
use Lemuria\Engine\Fantasya\Effect\Hunger;
use Lemuria\Engine\Fantasya\Effect\SpyEffect;
use Lemuria\Engine\Fantasya\Effect\TravelEffect;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Engine\Fantasya\Outlook;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Engine\Message;
use Lemuria\Engine\Message\Filter;
use Lemuria\Identifiable;
use Lemuria\ItemSet;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Commodity;
use Lemuria\Model\Fantasya\Composition;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Herb;
use Lemuria\Model\Fantasya\Loot;
use Lemuria\Model\Fantasya\Potion;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\Fantasya\World\PartyMap;
use Lemuria\Model\World\Direction;
use Lemuria\Singleton;
use Lemuria\Statistics;
use Lemuria\Statistics\Data;
use Lemuria\Statistics\Record;
use Lemuria\Version;

/**
 * A view object that contains variables and helper functions for view scripts.
 */
abstract class View
{
	protected final const BATTLE_ROW = ['Fliehen', 'Nicht', 'Defensiv', 'Hinten', 'Vorsichtig', 'Vorne', 'Aggressiv'];

	protected final const QUANTITY_FACTOR = [
		'Silver' => 100
	];

	public readonly Census $census;

	public readonly Outlook $outlook;

	public readonly TravelAtlas $atlas;

	public readonly PartyMap $map;

	protected readonly Dictionary $dictionary;

	protected readonly Statistics $statistics;

	protected array $spyEffect;

	protected ?array $variables = null;

	protected array $statisticsCache = [];

	public function __construct(public Party $party, private Filter $messageFilter) {
		$this->census  = new Census($this->party);
		$this->outlook = new Outlook($this->census);
		$this->atlas   = new TravelAtlas($this->party);
		$this->atlas->forRound(Lemuria::Calendar()->Round() - 1);
		$this->map        = new PartyMap(Lemuria::World(), $this->party);
		$this->dictionary = new Dictionary();
		$this->spyEffect  = $this->getSpyEffect();
		$this->statistics = Lemuria::Statistics();
	}

	public function isDevelopment(): bool {
		return Lemuria::FeatureFlag()->IsDevelopment();
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
				$direction = Direction::from($direction);
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

	public function races(Party $party): array {
		$races = [];
		foreach ($party->People() as $unit /* @var Unit $unit */) {
			$race = $unit->Race();
			$key  = $this->get('race', $race);
			if (!isset($races[$key])) {
				$races[$key] = ['race' => $race, 'persons' => 0, 'units' => 0];
			}
			$races[$key]['persons'] += $unit->Size();
			$races[$key]['units']++;
		}
		ksort($races);
		return $races;
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
		return $this->dictionary->get('battleRow.' . $unit->BattleRow()->value, $unit->Size() > 1 ? 1 : 0);
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

	public function hasTravelled(Unit $unit): bool {
		$effect = new TravelEffect(State::getInstance());
		return Lemuria::Score()->find($effect->setUnit($unit)) instanceof TravelEffect;
	}

	public function loot(): string {
		$loot  = $this->party->Loot();
		$group = Loot::ALL;
		$line  = $this->dictionary->get('loot.group_' . ($loot->has($group) ? $group : Loot::NOTHING));
		$items = [];
		while ($group < Loot::TROPHY) {
			$group *= 2;
			if ($loot->has($group)) {
				$items[] = $this->dictionary->get('loot.group_' . $group);
			}
		}
		foreach ($loot->Classes() as $commodity) {
			$items[] = $this->dictionary->get('resource.' . $commodity, 2);
		}
		$n = count($items);
		if ($n > 0) {
			if ($n > 1) {
				$items[$n - 2] .= ' und ' . $items[$n - 1];
				unset($items[$n - 1]);
			}
			$line .= ' außer ' . implode(', ', $items);
		}
		return $line;
	}

	#[Pure] public function composition(Composition $composition): string {
		return $this->dictionary->get('composition.' . $composition);
	}

	#[Pure] public function quantity(Quantity $quantity, Unit $unit): string {
		$commodity = $quantity->Commodity();
		$class     = match (true) {
			$commodity instanceof Herb   => 'herb',
			$commodity instanceof Potion => 'potion',
			default                      => getClass($commodity)
		};
		$key   = 'quantity.' . $class;
		$count = $quantity->Count();
		if ($this->dictionary->has($key)) {
			$factor = self::QUANTITY_FACTOR[$class] ?? 1;
			$size   = $unit->Size();
			$amount = $size > 0 ? $count / $factor / $size : 0;
			$index  = match (true) {
				$amount >= 10.0 => 2,
				$amount >= 2.0  => 1,
				default         => 0
			};
			return $this->dictionary->get($key, $index) . ' ' . $this->get('resource.' . $commodity, 1);
		}
		return $this->number($count, 'resource', $commodity);
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

	public function presettings(): array {
		$presettings   = [];
		$settings      = $this->party->Presettings();
		$presettings[] = 'KÄMPFEN ' . self::BATTLE_ROW[$settings->BattleRow()->value];
		$presettings[] = $settings->IsLooting() ? 'SAMMELN' : 'SAMMELN Nicht';
		$presettings[] = $settings->IsHiding() ? 'TARNEN' : 'TARNEN Nicht';
		$disguise      = $settings->Disguise();
		if ($disguise === null) {
			$presettings[] = 'TARNEN Partei';
		} elseif ($disguise instanceof Party) {
			$presettings[] = 'TARNEN Partei ' . $disguise->Id();
		}
		return $presettings;
	}

	public function statistics(Subject $subject, Identifiable $entity): ?Data {
		$record = new Record($subject->name, $entity);
		$key    = $record->Key();
		if (isset($this->statisticsCache[$key])) {
			return $this->statisticsCache[$key];
		}

		$data                        = $this->statistics->request($record)->Data();
		$this->statisticsCache[$key] = $data;
		return $data;
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
