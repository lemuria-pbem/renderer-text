<?php
declare (strict_types = 1);
namespace Lemuria\Renderer\Text;

use function Lemuria\getClass;
use function Lemuria\number as formatNumber;
use Lemuria\Engine\Fantasya\Census;
use Lemuria\Engine\Fantasya\Combat\BattleLog;
use Lemuria\Engine\Fantasya\Context;
use Lemuria\Engine\Fantasya\Effect\Hunger;
use Lemuria\Engine\Fantasya\Effect\ShipbuildingEffect;
use Lemuria\Engine\Fantasya\Effect\SpyEffect;
use Lemuria\Engine\Fantasya\Effect\Unmaintained;
use Lemuria\Engine\Fantasya\Factory\GrammarTrait;
use Lemuria\Engine\Fantasya\Factory\Model\Trades;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Engine\Fantasya\Factory\Model\Visibility;
use Lemuria\Engine\Fantasya\Factory\Model\Wage;
use Lemuria\Engine\Fantasya\Factory\RealmTrait;
use Lemuria\Engine\Fantasya\Message\Casus;
use Lemuria\Engine\Fantasya\Message\Filter\NoAnnouncementFilter;
use Lemuria\Engine\Fantasya\Outlook;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Engine\Message;
use Lemuria\Engine\Message\Filter;
use Lemuria\Identifiable;
use Lemuria\ItemSet;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Building\Canal;
use Lemuria\Model\Fantasya\Building\Port;
use Lemuria\Model\Fantasya\Commodity;
use Lemuria\Model\Fantasya\Composition;
use Lemuria\Model\Fantasya\Composition\Carcass;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Estate;
use Lemuria\Model\Fantasya\Fleet;
use Lemuria\Model\Fantasya\Landscape\Lake;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Herb;
use Lemuria\Model\Fantasya\Loot;
use Lemuria\Model\Fantasya\Market\Deal;
use Lemuria\Model\Fantasya\Market\Trade;
use Lemuria\Model\Fantasya\Potion;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\Fantasya\World\PartyMap;
use Lemuria\Renderer\Text\Engine\Announcement;
use Lemuria\Renderer\Text\Model\TravelLog;
use Lemuria\Singleton;
use Lemuria\Statistics;
use Lemuria\Statistics\Data;
use Lemuria\Statistics\Fantasya\PartyEntityRecord;
use Lemuria\Statistics\Record;
use Lemuria\Version\Module;

function dateTimeString(int $timestamp): string {
	return date('d.m.Y H:i:s', $timestamp);
}

function dateTimeIso8601(int $timestamp): string {
	return gmdate('Y-m-d', $timestamp) . 'T' . gmdate('H:i:s', $timestamp) . '.000Z';
}

/**
 * A view object that contains variables and helper functions for view scripts.
 */
abstract class View
{
	use GrammarTrait;
	use RealmTrait;

	protected final const BATTLE_ROW = ['Fliehen', 'Nicht', 'Defensiv', 'Hinten', 'Vorsichtig', 'Vorne', 'Aggressiv'];

	protected final const QUANTITY_FACTOR = [
		'Silver' => 100
	];

	public readonly Party $party;

	public readonly Census $census;

	public readonly Outlook $outlook;

	public readonly TravelAtlas $atlas;

	public readonly TravelLog $travelLog;

	public readonly PartyMap $map;

	protected readonly Statistics $statistics;

	protected array $spyEffect;

	protected ?array $variables = null;

	protected array $statisticsCache = [];

	private readonly Context $context;

	private readonly Filter $messageFilter;

	private int $created = 0;

	public static function sortedEstate(Region $region): Estate {
		$estate = clone $region->Estate();
		return $estate->sort();
	}

	public static function sortedFleet(Region $region): Fleet {
		$fleet = clone $region->Fleet();
		return $fleet->sort();
	}

	public function __construct(FileWriter $writer) {
		$this->context       = new Context(State::getInstance());
		$this->party         = $writer->getParty();
		$this->messageFilter = $writer->getFilter();
		$this->census        = new Census($this->party);
		$this->outlook       = new Outlook($this->census);
		$this->atlas         = new TravelAtlas($this->party);
		$this->atlas->forRound(Lemuria::Calendar()->Round() - 1);
		$this->travelLog  = new TravelLog($this->party);
		$this->map        = new PartyMap(Lemuria::World(), $this->party);
		$this->spyEffect  = $this->getSpyEffect();
		$this->statistics = Lemuria::Statistics();
		$this->initDictionary();
	}

	public function isDevelopment(): bool {
		return Lemuria::FeatureFlag()->IsDevelopment();
	}

	public function get(string $keyPath, $index = null): string {
		return $this->dictionary->get($keyPath, $index);
	}

	public function translate(Singleton|string $singleton, int $index = 0, Casus $casus = Casus::Adjective): string {
		return $this->translateSingleton($singleton, $index, $casus);
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
	public function number(int|float $number, Singleton|string|null $singleton = null, Casus $casus = Casus::Adjective, string $delimiter = ' '): string {
		if ($singleton) {
			$index = $number == 1 ? 0 : 1;
			return formatNumber($number) . $delimiter . $this->translateSingleton($singleton, $index, $casus);
		}
		return formatNumber($number);
	}

	public function things(Commodity $commodity): string {
		return $this->translateSingleton($commodity, 1);
	}

	/**
	 * Format a Quantity.
	 */
	public function resource(Quantity $item): string {
		return $this->number($item->Count(), $item->getObject());
	}

	/**
	 * Format a Deal.
	 */
	public function deal(Trade $trade, Deal $deal, bool $forceBoth = false): string {
		$isOffer   = $trade->Trade() === Trade::OFFER;
		$isMaximum = $deal->IsVariable() && $forceBoth ? null : $isOffer;
		$maximum   = $deal->Maximum();
		if ($isMaximum === null) {
			if ($deal->IsAdapting()) {
				$inventory = $trade->Unit()->Inventory();
				if ($isOffer) {
					$commodity = $deal->Commodity();
					$maximum   = $inventory[$commodity]->Count();
				} else {
					$price     = $trade->Price();
					$commodity = $price->Commodity();
					$reserve   = $inventory[$commodity]->Count();
					$maximum   = (int)floor($reserve / $price->Maximum());
				}
			}
		}
		return match ($isMaximum) {
			false   => $this->number($deal->Minimum(), $deal->Commodity()),
			true    => $this->number($deal->Maximum(), $deal->Commodity()),
			default => formatNumber($deal->Minimum()) . '–' . $this->number($maximum, $deal->Commodity())
		};
	}

	/**
	 * Format a Deal.
	 */
	public function ownDeal(Trade $trade, Deal $deal): string {
		$isOffer   = $trade->Trade() === Trade::OFFER;
		$isMaximum = $deal->IsVariable() ? null : $isOffer;
		$maximum   = $deal->Maximum();
		if ($isMaximum === null) {
			if ($deal->IsAdapting()) {
				$inventory = $trade->Unit()->Inventory();
				if ($isOffer) {
					$commodity = $deal->Commodity();
					$maximum   = $inventory[$commodity]->Count();
				} else {
					$price     = $trade->Price();
					$commodity = $price->Commodity();
					$reserve   = $inventory[$commodity]->Count();
					$maximum   = (int)floor($reserve / $price->Maximum());
				}
				if ($maximum <= 0) {
					$isMaximum = 0;
				}
			}
		}
		return match ($isMaximum) {
			false   => $this->number($deal->Minimum(), $deal->Commodity()),
			true    => $this->number($deal->Maximum(), $deal->Commodity()),
			0       => '* ' . $this->translateSingleton($deal->Commodity()),
			default => formatNumber($deal->Minimum()) . '–' . $this->number($maximum, $deal->Commodity())
		};
	}

	/**
	 * Get an Item from a set.
	 */
	public function item(string $class, ItemSet $set): string {
		$item = $set[$class];
		return $this->number($item->Count(), $item->getObject());
	}

	/**
	 * Get a list of items from a set.
	 */
	public function items(array $classes, ItemSet $set): string {
		$items = [];
		foreach ($classes as $class) {
			if (isset($set[$class])) {
				$items[] = $this->item($class, $set);
			}
		}
		return $this->toAndString($items);
	}

	/**
	 * Get all neighbours of a region.
	 *
	 * @return array<string>
	 */
	public function neighbours(Region $region): array {
		$neighbours = [];
		$roads      = $region->Roads();
		foreach ($this->map->getNeighbours($region) as $direction => $neighbour) {
			$visibility = $this->atlas->getVisibility($neighbour)->value;
			if ($visibility > Visibility::Unknown->value) {
				if ($region->hasRoad($direction)) {
					$predicate = ' führt eine Straße ';
					$neighbour = $this->neighbour($neighbour, true);
				} elseif ($roads && $roads[$direction] > 0.0) {
					$percent   = (int)floor(100.0 * $roads[$direction]);
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
	public function neighbour(Region $region = null, bool $hasRoad = false): string {
		$landscape = $region->Landscape();
		if ($hasRoad) {
			$text = $this->combineGrammar($landscape, 'zum', Casus::Dative);
		} else {
			$article = $landscape instanceof Lake ? 'ein' : 'das';
			$text    = $this->combineGrammar($landscape, $article, Casus::Nominative);
		}

		$id          = (string)$region->Id();
		$defaultName = $this->translateSingleton($landscape) . ' ' . $id;
		$name        = $region->Name();
		if ($name === $defaultName) {
			$text .= ' ' . $id;
		} elseif ($name) {
			$isOcean = $landscape instanceof Ocean && $name === 'Ozean';
			$isLake  = $landscape instanceof Lake && $name === 'See';
			if (!$isOcean && !$isLake) {
				$text .= ' ' . $name;
			}
		}
		return $text;
	}

	public function people(Construction|Vessel $entity): int {
		$count  = 0;
		$people = $entity instanceof Construction ? $entity->Inhabitants() : $entity->Passengers();
		foreach ($people as $unit) {
			$count += $unit->Size();
		}
		return $count;
	}

	public function races(Party $party): array {
		$races = [];
		foreach ($party->People() as $unit) {
			$race = $unit->Race();
			$key  = $this->translateSingleton($race, casus: Casus::Accusative);
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
	 * @return array<Message>
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

	/**
	 * @return array<Announcement>
	 */
	public function announcements(): array {
		$announcements = [];
		$atlas         = $this->census->getAtlas();
		$filter        = new NoAnnouncementFilter();
		foreach (Lemuria::Report()->getAll($this->party) as $message) {
			if (!$filter->retains($message)) {
				$announcements[] = new Announcement($message, $this->dictionary);
			}
		}
		foreach ($this->atlas as $region) {
			$visibility = $this->atlas->getVisibility($region);
			if (in_array($visibility, [Visibility::WithUnit, Visibility::Travelled])) {
				foreach (Lemuria::Report()->getAll($region) as $message) {
					if (!$filter->retains($message)) {
						$announcements[] = new Announcement($message, $this->dictionary);
					}
				}
				if ($visibility === Visibility::WithUnit) {
					foreach (self::sortedEstate($region) as $construction) {
						foreach (Lemuria::Report()->getAll($construction) as $message) {
							if (!$filter->retains($message)) {
								$announcements[] = new Announcement($message, $this->dictionary);
							}
						}
					}
					foreach (self::sortedFleet($region) as $vessel) {
						foreach (Lemuria::Report()->getAll($vessel) as $message) {
							if (!$filter->retains($message)) {
								$announcements[] = new Announcement($message, $this->dictionary);
							}
						}
					}
				}
			}
			if ($atlas->has($region->Id())) {
				foreach ($this->census->getPeople($region) as $unit) {
					foreach (Lemuria::Report()->getAll($unit) as $message) {
						if (!$filter->retains($message)) {
							$announcements[] = new Announcement($message, $this->dictionary);
						}
					}
				}
			}
		}
		return $announcements;
	}

	/**
	 * @return array<BattleLog>
	 */
	public function hostilities(): array {
		return Lemuria::Hostilities()->findFor($this->party);
	}

	public function relation(Relation $relation): string {
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

	public function spyLevel(Unit $unit): int {
		return $this->spyEffect[$unit->Id()->Id()] ?? 0;
	}

	public function battleRow(Unit $unit): string {
		return $this->dictionary->get('battleRow.' . $unit->BattleRow()->value, $unit->Size() > 1 ? 1 : 0);
	}

	public function health(Unit $unit): string {
		$effect = new Hunger(State::getInstance());
		if (Lemuria::Score()->find($effect->setUnit($unit))) {
			$key = 'hunger';
		} else {
			$key = 'default';
		}
		return $this->dictionary->get('health.' . $key, $this->healthStage($unit));
	}

	public function healthMark(Unit $unit): string {
		return match ($this->healthStage($unit)) {
			1       => '⚔',
			2       => '✝',
			3       => '†',
			4       => '🕇',
			default => ''
		};
	}

	public function healthStage(Unit $unit): int {
		$health = $unit->Health();
		return match (true) {
			$health < 0.25 => 4,
			$health < 0.5  => 3,
			$health < 0.75 => 2,
			$health < 1.0  => 1,
			default        => 0
		};
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
			$items[] = $this->translateSingleton($commodity, 1, Casus::Dative);
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

	/**
	 * @noinspection PhpSwitchStatementWitSingleBranchInspection
	 */
	public function composition(Unicum|Composition $unicum): string {
		$composition = $unicum instanceof Unicum ? $unicum->Composition() : $unicum;
		if ($this->party->Type() !== Type::Player) {
			switch ($composition::class) {
				case Carcass::class :
					return $unicum->Name();
			}
		}
		return $this->translateSingleton($composition);
	}

	public function quantity(Quantity $quantity, Unit $unit): string {
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
			return $this->dictionary->get($key, $index) . ' ' . $this->translateSingleton($commodity, 1);
		}
		return $this->number($count, $commodity);
	}

	public function gameVersions(): array {
		$version  = Lemuria::Version();
		$versions = [];
		foreach ($version[Module::Base] as $versionTag) {
			$versions[] = $versionTag->name . ': ' . $versionTag->version;
		}
		foreach ($version[Module::Model] as $versionTag) {
			$versions[] = $versionTag->name . ': ' . $versionTag->version;
		}
		foreach ($version[Module::Engine] as $versionTag) {
			$versions[] = $versionTag->name . ': ' . $versionTag->version;
		}
		foreach ($version[Module::Renderers] as $versionTag) {
			$versions[] = $versionTag->name . ': ' . $versionTag->version;
		}
		foreach ($version[Module::Statistics] as $versionTag) {
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
		$record = match ($subject) {
			Subject::Experts, Subject::Joblessness, Subject::Prosperity,
			Subject::Talents, Subject::Workplaces                        => new Record($subject->name, $entity),
			default                                                      => new PartyEntityRecord($subject->name, $entity),
		};
		$key = $record->Key();
		if (isset($this->statisticsCache[$key])) {
			return $this->statisticsCache[$key];
		}

		$data                        = $this->statistics->request($record)->Data();
		$this->statisticsCache[$key] = $data;
		return $data;
	}

	/**
	 * @return array<int>
	 */
	public function talentStatistics(Subject $subject, Unit $unit): array {
		$statistics = [];
		$talents    = $this->statistics($subject, $unit);
		if ($talents) {
			foreach ($talents as $class => $number /** @var Number $number */) {
				$statistics[$class] = $number->change;
			}
		}
		return $statistics;
	}

	public function building(Trades $trades, Construction $construction): ?string {
		if ($trades->HasMarket()) {
			return 'market';
		}
		return match ($construction->Building()::class) {
			Canal::class => 'canal',
			Port::class  => 'port',
			default      => null
		};
	}

	public function isMaintained(Construction $construction): bool {
		$effect = new Unmaintained(State::getInstance());
		return !Lemuria::Score()->find($effect->setConstruction($construction));
	}

	public function isShipbuilder(Unit $unit): bool {
		if ($unit->Construction()) {
			$effect = new ShipbuildingEffect(State::getInstance());
			return (bool)Lemuria::Score()->find($effect->setUnit($unit));
		}
		return false;
	}

	public function wage(Region $region): int {
		$infrastructure = $this->calculateInfrastructure($region);
		$wage           = new Wage($infrastructure);
		return $wage->getWage();
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

	public function getCreatedTimestamp(): int {
		if (!$this->created) {
			$this->created = time();
		}
		return $this->created;
	}

	abstract protected function generateContent(string $template): string;

	private function getSpyEffect(): array {
		$effect = new SpyEffect(State::getInstance());
		/** @var SpyEffect $effect */
		$effect = Lemuria::Score()->find($effect->setParty($this->party));
		return $effect?->Targets() ?? [];
	}
}
