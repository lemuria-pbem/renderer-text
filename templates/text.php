<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\center;
use function Lemuria\Renderer\Text\description;
use function Lemuria\Renderer\Text\hr;
use function Lemuria\Renderer\Text\line;
use Lemuria\Lemuria;
use Lemuria\Model\Lemuria\Ability;
use Lemuria\Model\Lemuria\Commodity\Granite;
use Lemuria\Model\Lemuria\Commodity\Ore;
use Lemuria\Model\Lemuria\Commodity\Peasant;
use Lemuria\Model\Lemuria\Commodity\Silver;
use Lemuria\Model\Lemuria\Commodity\Tree;
use Lemuria\Model\Lemuria\Construction;
use Lemuria\Model\Lemuria\Quantity;
use Lemuria\Model\Lemuria\Region;
use Lemuria\Model\Lemuria\Unit;
use Lemuria\Model\Lemuria\Vessel;
use Lemuria\Renderer\Text\Intelligence;
use Lemuria\Renderer\Text\View;

/**
 * A parties' report in plain text.
 */

/* @var View $this */

$party         = $this->party;
$report        = Lemuria::Report()->getAll($party);
$acquaintances = $party->Diplomacy()->Acquaintances();
$census        = $this->census;
$map	       = $this->map;
$race	       = getClass($party->Race());
$calendar      = Lemuria::Calendar();
$season        = $this->get('calendar.season', $calendar->Season());
$month	       = $this->get('calendar.month', $calendar->Month());
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

<?php foreach ($report as $message): ?>
<?= $message ?>

<?php endforeach ?>

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
foreach ($census->getAtlas() as $region /* @var Region $region */):
	$report    = Lemuria::Report()->getAll($region);
	$resources = $region->Resources();
	$t         = $resources[Tree::class]->Count();
	$g         = $resources[Granite::class]->Count();
	$o         = $resources[Ore::class]->Count();
	$m         = $g && $o;
	$trees     = $this->item(Tree::class, $resources);
	$granite   = $this->item(Granite::class, $resources);
	$ore       = $this->item(Ore::class, $resources);
	$mining    = null;
	if ($m):
		$mining = $granite . ' und ' . $ore;
	elseif ($g):
		$mining = $granite;
	elseif ($o):
		$mining = $ore;
	endif;
	$neighbours = [];
	foreach ($map->getNeighbours($region)->getAll() as $direction => $neighbour):
		$neighbours[] = 'im ' . $this->get('world', $direction) . ' liegt ' . $this->neighbour($neighbour);
	endforeach;
	$n = count($neighbours);
	if ($n > 1):
		$neighbours[$n - 2] .= ' und ' . $neighbours[$n - 1];
		unset($neighbours[$n - 1]);
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
?>
>> <?= $region->Name() ?> <?= $map->getCoordinates($region) ?>, <?= $this->get('landscape', $region->Landscape()) ?>, <?= $this->item(Peasant::class, $resources) ?>, <?= $this->item(Silver::class, $resources) ?>. <?php if ($t && $m): ?>
Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> gefällt sowie <?= $mining ?> abgebaut werden. <?php
elseif ($t): ?>
Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> gefällt werden. <?php
elseif ($g || $o): ?>
Hier <?= $g + $o === 1 ? 'kann' : 'können' ?> <?= $mining ?> abgebaut werden. <?php
endif ?><?php if ($g > 0): ?> Die Region wird bewacht von <?= ucfirst(implode(', ', $guardNames)) ?><?php endif ?>.
<?= ucfirst(implode(', ', $neighbours)) ?>
.<?= line(description($region)) ?>
<?php foreach ($report as $message): ?>
<?= $message ?>

<?php endforeach ?>

<?php foreach ($region->Estate() as $construction /* @var Construction $construction */): ?>
  >> <?= $construction ?>, <?= $this->get('building', $construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?>
. Besitzer ist <?= count($construction->Inhabitants()) ? $construction->Inhabitants()->Owner() : 'niemand' ?>
.<?= line(description($construction)) ?>
<?php foreach ($report = Lemuria::Report()->getAll($construction) as $message): ?>
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
<?php foreach ($report = Lemuria::Report()->getAll($unit) as $message): ?>
<?= $message ?>

<?php endforeach ?>
<?php endforeach ?>
<?php endforeach ?>

<?php foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */): ?>
  >> <?= $vessel ?>, <?= $this->get('ship', $vessel->Ship()) ?>, freier Platz <?= $this->number((int)ceil($vessel->Space() / 100)) ?>
 GE. Kapitän ist <?= count($vessel->Passengers()) ? $vessel->Passengers()->Owner() : 'niemand' ?>
.<?= line(description($vessel)) ?>
<?php foreach ($report = Lemuria::Report()->getAll($construction) as $message): ?>
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
<?php foreach ($report = Lemuria::Report()->getAll($unit) as $message): ?>
<?= $message ?>

<?php endforeach ?>
<?php endforeach ?>
<?php endforeach ?>

<?php $unitsInRegions = 0 ?>
<?php foreach ($census->getPeople($region) as $unit /* @var Unit $unit */): ?>
<?php if (!$unit->Construction() && !$unit->Vessel()): ?>
  -- <?= (string)$unit ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsGuarding()): echo ', bewacht die Region' ?><?php endif ?>.<?= description($unit) ?>
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
<?php foreach ($report = Lemuria::Report()->getAll($unit) as $message): ?>
<?= $message ?>

<?php endforeach ?>
<?php $unitsInRegions++ ?>
<?php endif ?>
<?php endforeach ?>
<?= $unitsInRegions ? PHP_EOL : '' ?>
<?php endforeach ?>
