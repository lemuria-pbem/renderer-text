<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\footer;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Engine\Fantasya\Availability;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Building\Site;
use Lemuria\Model\Fantasya\Commodity\Camel;
use Lemuria\Model\Fantasya\Commodity\Elephant;
use Lemuria\Model\Fantasya\Commodity\Griffin;
use Lemuria\Model\Fantasya\Commodity\Griffinegg;
use Lemuria\Model\Fantasya\Commodity\Horse;
use Lemuria\Model\Fantasya\Commodity\Iron;
use Lemuria\Model\Fantasya\Commodity\Peasant;
use Lemuria\Model\Fantasya\Commodity\Silver;
use Lemuria\Model\Fantasya\Commodity\Stone;
use Lemuria\Model\Fantasya\Commodity\Wood;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Offer;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

$party            = $this->party;
$banner           = $this->party->Banner() ? 'Unser Banner: ' . $this->party->Banner() : '(kein Banner gesetzt)';
$report           = $this->messages($party);
$diplomacy        = $party->Diplomacy();
$acquaintances    = $diplomacy->Acquaintances();
$generalRelations = $diplomacy->search($party);
$census           = $this->census;
$outlook          = $this->outlook;
$atlas            = $this->atlas;
$map              = $this->map;
$race             = getClass($party->Race());
$calendar         = Lemuria::Calendar();
$season           = $this->get('calendar.season', $calendar->Season() - 1);
$month            = $this->get('calendar.month', $calendar->Month() - 1);
$week             = $calendar->Week();

?>
<?= center('Lemuria-Auswertung') ?>
<?= center('~~~~~~~~~~~~~~~~~~~~~~~~') ?>

<?= center('für die ' . $week . '. Woche des Monats ' . $month . ' im ' . $season . ' des Jahres ' . $calendar->Year()) ?>
<?= center('(Runde ' . $calendar->Round() . ')') ?>


Dein Volk: <?= $party->Name() ?> [<?= $party->Id() ?>]

<?= line($party->Description()) ?>

<?= line($banner) ?>

Dein Volk zählt <?= $this->number($census->count(), 'race', $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.

<?= hr() ?>

<?= center('Ereignisse') ?>

<?= $this->template('report', $party) ?>

<?= hr() ?>

<?= center('Alle bekannten Völker') ?>

<?= $this->wrappedTemplate('acquaintances', $party) ?>

<?= hr() ?>

<?= center('Kontinent Lemuria [' . $party->Id() . ']') ?>

Dies ist der Hauptkontinent Lemuria.
<?php
foreach ($atlas as $region /* @var Region $region */):
	$landscape  = $region->Landscape();
	$isOcean    = $landscape instanceof Ocean;
	$hasUnits   = $atlas->getVisibility($region) === TravelAtlas::WITH_UNIT;
	$resources  = $region->Resources();
	$neighbours = [];
	foreach ($map->getNeighbours($region)->getAll() as $direction => $neighbour):
		$neighbours[] = 'im ' . $this->get('world', $direction) . ' liegt ' . $this->neighbour($neighbour);
	endforeach;
	$n = count($neighbours);
	if ($n > 1):
		$neighbours[$n - 2] .= ' und ' . $neighbours[$n - 1];
		unset($neighbours[$n - 1]);
	endif;

	if ($hasUnits):
		$report  = $this->messages($region);
		$t       = $resources[Wood::class]->Count();
		$g       = $resources[Stone::class]->Count();
		$o       = $resources[Iron::class]->Count();
		$m       = $g && $o;
		$trees   = $this->item(Wood::class, $resources);
		$granite = $this->item(Stone::class, $resources);
		$ore     = $this->item(Iron::class, $resources);
		$mining  = null;
		if ($m):
			$mining = $granite . ' und ' . $ore;
		elseif ($g):
			$mining = $granite;
		elseif ($o):
			$mining = $ore;
		endif;

		$a       = $resources[Horse::class]->Count() + $resources[Camel::class]->Count() + $resources[Elephant::class]->Count();
		$animals = $this->items([Horse::class, Camel::class, Elephant::class], $resources);
		$gr      = $resources[Griffin::class]->Count();
		$egg     = $resources[Griffinegg::class]->Count();
		$griffin = null;
		if ($gr):
			$griffin = $this->item(Griffin::class, $resources);
			if ($egg):
				$griffin .= ' mit ' . $this->item(Griffinegg::class, $resources);
			endif;
		endif;

		$availability = new Availability($region);
		$peasants     = $availability->getResource(Peasant::class);
		$recruits     = $this->resource($peasants);
		$r            = $peasants->Count();

		$intelligence = new Intelligence($region);
		$guards       = $intelligence->getGuards();
		$g            = count($guards);
		if ($g > 0):
			$guardNames = [];
			foreach ($guards as $unit /* @var Unit $unit */):
				$guardNames[] = $unit->Name();
			endforeach;
			if ($g > 1):
				$guardNames[$g - 2] .= ' und ' . $guardNames[$g - 1];
				unset ($guardNames[$g - 1]);
			endif;
		endif;
		$materialPool = [];
		foreach ($intelligence->getMaterialPool($party) as $quantity /* @var Quantity $quantity */):
			$materialPool[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
		endforeach;

		$luxuries = null;
		$castle   = $intelligence->getGovernment();
		if ($castle?->Size() > Site::MAX_SIZE):
			$luxuries = $region->Luxuries();
			if ($luxuries):
				$offer  = $luxuries->Offer();
				$demand = [];
				foreach ($luxuries as $luxury /* @var Offer $luxury */):
					$demand[] = $this->get('resource', $luxury->Commodity()) . ' $' . $this->number($luxury->Price());
				endforeach;
			endif;
		endif;
	endif;
?>

<?php if ($hasUnits): ?>
<?php if ($isOcean): ?>
<?php if ($region->Name() !== 'Ozean'): ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>.
<?php endif ?>
<?php else: ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>, <?= $this->get('landscape', $region->Landscape()) ?>, <?= $this->item(Peasant::class, $resources) ?>, <?= $this->item(Silver::class, $resources) ?>
.<?php if ($r > 0): ?> <?= $recruits ?> <?= $r === 1 ? 'kann' : 'können' ?> rekrutiert werden.<?php endif ?>
<?php if ($t && $m): ?>
 Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> geerntet sowie <?= $mining ?> abgebaut werden.<?php
elseif ($t): ?>
 Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> geerntet werden.<?php
elseif ($g || $o): ?>
 Hier <?= $g + $o === 1 ? 'kann' : 'können' ?> <?= $mining ?> abgebaut werden.<?php
endif ?><?php if ($a): ?> <?= $animals ?> <?= $a === 1 ? 'streift' : 'streifen' ?> durch die Wildnis.<?php
endif ?><?php if ($gr): ?> <?= $griffin ?> <?= $gr === 1 ? ' nistet ' : 'nisten' ?> in den Bergen.<?php
endif ?><?php if ($g > 0): ?> Die Region wird bewacht von <?= ucfirst(implode(', ', $guardNames)) ?>.<?php endif ?>
<?php endif ?>

<?= ucfirst(implode(', ', $neighbours)) ?>
.<?= line(description($region)) ?>
<?php if ($luxuries): ?>
Die Bauern produzieren <?= $this->things($offer->Commodity()) ?> und verlangen pro Stück $<?= $this->number($offer->Price()) ?>
. Marktpreise für andere Waren: <?= implode(', ', $demand) ?>.
<?php endif ?>
<?php foreach ($report as $message): ?>
<?= $message ?>

<?php endforeach ?>

Materialpool: <?= implode(', ', $materialPool) ?>.
<?php else: ?>
<?php if ($isOcean && $region->Name() === 'Ozean'): ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>.
<?php else: ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>, <?= $this->get('landscape', $region->Landscape()) ?>.
<?php endif ?>

<?= ucfirst(implode(', ', $neighbours)) ?>
.<?= line(description($region)) ?>
<?php endif ?>
<?php if ($hasUnits): ?>
<?php foreach ($region->Estate() as $construction /* @var Construction $construction */): ?>

  >> <?= $construction ?>, <?= $this->get('building', $construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?> mit <?= $this->number($this->people($construction)) ?>
 Bewohnern. Besitzer ist <?= count($construction->Inhabitants()) ? $construction->Inhabitants()->Owner() : 'niemand' ?>
.<?= line(description($construction)) ?>
<?= $this->template('report', $construction) ?>
<?php foreach ($construction->Inhabitants() as $unit /* @var Unit $unit */): ?>
<?php
$isOwn = $unit->Party() === $party;
if ($isOwn):
	$disguised = $unit->Disguise();
	$calculus  = new Calculus($unit);
	$talents   = [];
	foreach ($unit->Knowledge() as $ability /* @var Ability $ability */):
		$experience = $ability->Experience();
		$ability    = $calculus->knowledge($ability->Talent());
		$talents[]  = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($experience) . ')';
	endforeach;
	$inventory = [];
	$payload   = 0;
	foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */):
		$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
		$payload += $quantity->Weight();
	endforeach;
	$n = count($inventory);
	if ($n > 1):
		$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
		unset($inventory[$n - 1]);
	endif;
	$weight = (int)ceil($payload / 100);
	$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);
else:
	$foreign = $census->getParty($unit);
	if (!$foreign):
		$foreign = 'unbekannte Partei';
	endif;
endif;
?>

<?php if (!$isOwn): ?>
   * <?= $unit ?> von <?= $foreign ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

<?php else: ?>
   * <?= $unit ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?>
<?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?>
<?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?>
<?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>
. Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>
, Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
<?= $this->template('report', $unit) ?>
<?php endif ?>
<?php endforeach ?>
<?php endforeach ?>
<?php foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */): ?>
<?php $passengers = $this->people($vessel) ?>

  >> <?= $vessel ?>, <?= $this->get('ship', $vessel->Ship()) ?> mit <?= $this->number($passengers) ?> <?php if ($passengers === 1): ?>Passagier<?php else: ?>Passagieren<?php endif ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>%, <?php if ($vessel->Space() < 0): ?>überladen mit<?php else: ?>freier Platz<?php endif ?> <?= $this->number((int)ceil(abs($vessel->Space()) / 100)) ?>
 GE. Kapitän ist <?= count($vessel->Passengers()) ? $vessel->Passengers()->Owner() : 'niemand' ?>
<?php if (!($vessel->Region()->Landscape() instanceof Ocean)): ?>
<?php if ($vessel->Anchor() === Vessel::IN_DOCK): ?>
. Das Schiff liegt im Dock<?php else: ?>
. Das Schiff ankert im <?= $this->get('world', $vessel->Anchor()) ?>
<?php endif ?>
<?php endif ?>
.<?= line(description($vessel)) ?>
<?= $this->template('report', $vessel) ?>
<?php foreach ($vessel->Passengers() as $unit /* @var Unit $unit */): ?>
<?php
$isOwn = $unit->Party() === $party;
if ($isOwn):
	$disguised = $unit->Disguise();
	$calculus  = new Calculus($unit);
	$talents   = [];
	foreach ($unit->Knowledge() as $ability /* @var Ability $ability */) {
		$experience = $ability->Experience();
		$ability    = $calculus->knowledge($ability->Talent());
		$talents[]  = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($experience) . ')';
	}
	$inventory = [];
	$payload   = 0;
	foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */) {
		$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
		$payload += $quantity->Weight();
	}
	$n = count($inventory);
	if ($n > 1) {
		$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
		unset($inventory[$n - 1]);
	}
	$weight = (int)ceil($payload / 100);
	$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);
else:
	$foreign = $census->getParty($unit);
	if (!$foreign):
		$foreign = 'unbekannte Partei';
	endif;
endif;
?>

<?php if (!$isOwn): ?>
   * <?= $unit ?> von <?= $foreign ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

<?php else: ?>
   * <?= $unit ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?>
<?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?>
<?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?>
<?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>
. Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>
, Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
<?= $this->template('report', $unit) ?>
<?php endif ?>
<?php endforeach ?>
<?php endforeach ?>
<?php foreach ($outlook->Apparitions($region) as $unit /* @var Unit $unit */): ?>
<?php
$isOwn = $unit->Party() === $party;
if ($isOwn):
	$disguised = $unit->Disguise();
	$calculus  = new Calculus($unit);
	$talents   = [];
	foreach ($unit->Knowledge() as $ability /* @var Ability $ability */):
		$experience = $ability->Experience();
		$ability    = $calculus->knowledge($ability->Talent());
		$talents[]  = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($experience) . ')';
	endforeach;
	$inventory = [];
	$payload   = 0;
	foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */):
		$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
		$payload += $quantity->Weight();
	endforeach;
	$n = count($inventory);
	if ($n > 1):
		$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
		unset($inventory[$n - 1]);
	endif;
	$weight = (int)ceil($payload / 100);
	$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);
else:
	$foreign = $census->getParty($unit);
	if (!$foreign):
		$foreign = 'unbekannte Partei';
	endif;
endif;
?>

<?php if (!$isOwn): ?>
  -- <?= $unit ?> von <?= $foreign ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>
<?php else: ?>
  -- <?= $unit ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?>
<?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?>
<?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?>
<?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>
. Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>
, Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?>
 GE.<?= $this->template('report', $unit) ?>
<?php endif ?>

<?php endforeach ?>
<?php endif ?>
<?php endforeach ?>

<?= footer() ?>
