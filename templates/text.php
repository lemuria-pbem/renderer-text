<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\center;
use function Lemuria\Renderer\Text\description;
use function Lemuria\Renderer\Text\footer;
use function Lemuria\Renderer\Text\hr;
use function Lemuria\Renderer\Text\line;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Ability;
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
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\Intelligence;
use Lemuria\Renderer\Text\View;

/**
 * A party's report in plain text.
 */

/* @var View $this */

$party         = $this->party;
$report        = $this->messages($party);
$acquaintances = $party->Diplomacy()->Acquaintances();
$census        = $this->census;
$outlook       = $this->outlook;
$atlas         = $this->atlas;
$map	       = $this->map;
$race	       = getClass($party->Race());
$calendar      = Lemuria::Calendar();
$season        = $this->get('calendar.season', $calendar->Season() - 1);
$month	       = $this->get('calendar.month', $calendar->Month() - 1);
$week	       = $calendar->Week();

?>
<?= center('Lemuria-Auswertung') ?>
<?= center('~~~~~~~~~~~~~~~~~~~~~~~~') ?>

<?= center('für die ' . $week . '. Woche des Monats ' . $month . ' im ' . $season . ' des Jahres ' . $calendar->Year()) ?>
<?= center('(Runde ' . $calendar->Round() . ')') ?>


Dein Volk: <?= $party->Name() ?> [<?= $party->Id() ?>]

<?= line($party->Description()) ?>

Dein Volk zählt <?= $this->number($census->count(), 'race', $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.

<?= hr() ?>

<?= center('Ereignisse') ?>

<?php if ($report): ?>
<?php foreach ($report as $message): ?>
<?= $message ?>

<?php endforeach ?>

<?php endif ?>
<?= hr() ?>

<?= center('Alle bekannten Völker') ?>

<?php if ($acquaintances->count()): ?>
<?php foreach ($acquaintances as $acquaintance): ?>
<?= $acquaintance ?>

<?php endforeach ?>

<?php endif ?>
<?= hr() ?>

<?= center('Kontinent Lemuria [' . $party->Id() . ']') ?>

Dies ist der Hauptkontinent Lemuria.
<?php
foreach ($atlas as $region /* @var Region $region */):
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
	endif;
?>

<?php if ($hasUnits): ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>, <?= $this->get('landscape', $region->Landscape()) ?>, <?= $this->item(Peasant::class, $resources) ?>, <?= $this->item(Silver::class, $resources) ?>. <?php if ($t && $m): ?>
Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> geerntet sowie <?= $mining ?> abgebaut werden.<?php
elseif ($t): ?>
Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> geerntet werden.<?php
elseif ($g || $o): ?>
Hier <?= $g + $o === 1 ? 'kann' : 'können' ?> <?= $mining ?> abgebaut werden.<?php
endif ?><?php if ($a): ?> <?= $animals ?> <?= $a === 1 ? 'streift' : 'streifen' ?> durch die Wildnis.<?php
endif ?><?php if ($gr): ?> <?= $griffin ?> <?= $gr === 1 ? ' nistet ' : 'nisten' ?> in den Bergen.<?php
endif ?><?php if ($g > 0): ?> Die Region wird bewacht von <?= ucfirst(implode(', ', $guardNames)) ?>.<?php endif ?>

<?= ucfirst(implode(', ', $neighbours)) ?>
.<?= line(description($region)) ?>
<?php foreach ($report as $message): ?>
<?= $message ?>

<?php endforeach ?>
<?php else: ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>, <?= $this->get('landscape', $region->Landscape()) ?>.

<?= ucfirst(implode(', ', $neighbours)) ?>
.<?= line(description($region)) ?>
<?php endif ?>
<?php if ($hasUnits): ?>
<?php foreach ($region->Estate() as $construction /* @var Construction $construction */): ?>

  >> <?= $construction ?>, <?= $this->get('building', $construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?>
. Besitzer ist <?= count($construction->Inhabitants()) ? $construction->Inhabitants()->Owner() : 'niemand' ?>
.<?= line(description($construction)) ?>
<?php foreach ($report = $this->messages($construction) as $message): ?>
  <?= $message ?>

<?php endforeach ?>
<?php foreach ($construction->Inhabitants() as $unit /* @var Unit $unit */): ?>

    * <?= (string)$unit ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsGuarding()): echo ', bewacht die Region' ?><?php endif ?>.<?= description($unit) ?>
<?php
$talents = [];
foreach ($unit->Knowledge() as $ability /* @var Ability $ability */) {
	$talents[] = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($ability->Experience()) . ')';
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
?>
 Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>
. Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>
, Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
<?php foreach ($report = $this->messages($unit) as $message): ?>
<?= $message ?>

<?php endforeach ?>
<?php endforeach ?>
<?php endforeach ?>
<?php foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */): ?>

  >> <?= $vessel ?>, <?= $this->get('ship', $vessel->Ship()) ?>, freier Platz <?= $this->number((int)ceil($vessel->Space() / 100)) ?>
 GE. Kapitän ist <?= count($vessel->Passengers()) ? $vessel->Passengers()->Owner() : 'niemand' ?>
.<?= line(description($vessel)) ?>
<?php foreach ($report = $this->messages($vessel) as $message): ?>
<?= $message ?>

<?php endforeach ?>
<?php foreach ($vessel->Passengers() as $unit /* @var Unit $unit */): ?>
    * <?= (string)$unit ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?>.<?= description($unit) ?>
<?php
$talents = [];
foreach ($unit->Knowledge() as $ability /* @var Ability $ability */) {
	$talents[] = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($ability->Experience()) . ')';
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
?>
Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>
. Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>
, Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
<?php foreach ($report = $this->messages($unit) as $message): ?>
<?= $message ?>

<?php endforeach ?>
<?php endforeach ?>
<?php endforeach ?>
<?php foreach ($outlook->Apparitions($region) as $unit /* @var Unit $unit */): ?>

  -- <?= (string)$unit ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsGuarding()): echo ', bewacht die Region' ?><?php endif ?>.<?= description($unit) ?>
<?php
if ($unit->Party() === $party):
	$talents = [];
	foreach ($unit->Knowledge() as $ability /* @var Ability $ability */) {
		$talents[] = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($ability->Experience()) . ')';
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
?>
 Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>
. Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>
, Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
<?php foreach ($report = $this->messages($unit) as $message): ?>
<?= $message ?>

<?php endforeach ?>
<?php endif ?>
<?php endforeach ?>
<?php endif ?>
<?php endforeach ?>

<?= footer() ?>
