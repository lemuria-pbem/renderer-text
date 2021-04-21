<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Region $region */
$region   = $this->variables[0];
$party    = $this->party;
$census   = $this->census;
$outlook  = $this->outlook;
$atlas    = $this->atlas;
$map      = $this->map;
$hasUnits = $atlas->getVisibility($region) === TravelAtlas::WITH_UNIT;

if ($hasUnits):
	$report       = $this->messages($region);
	$intelligence = new Intelligence($region);
	$materialPool = [];
	foreach ($intelligence->getMaterialPool($party) as $quantity /* @var Quantity $quantity */):
		$materialPool[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
	endforeach;
endif;

?>
<?php if ($hasUnits): ?>
<?= $this->template('region/with-unit', $region) ?>
<?php foreach ($report as $message): ?>
<?= $message ?>

<?php endforeach ?>

Materialpool: <?= implode(', ', $materialPool) ?>.
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
<?php else: ?>
<?= $this->template('region/neighbour', $region) ?>
<?php endif ?>
