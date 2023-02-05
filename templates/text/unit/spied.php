<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\description;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Factory\Model\Trades;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Unit $unit */
$unit    = $this->variables[0];
/** @var Trades|null $trades */
$trades  = $this->variables[1];
$census  = $this->census;
$prefix  = $unit->Construction() || $unit->Vessel() ? '   * ' : '  -- ';
$foreign = $census->getParty($unit);
if (!$foreign):
	$foreign = 'unbekannte Partei';
endif;
$disguised = $unit->Disguise();
$calculus  = new Calculus($unit);
$talents   = [];
foreach ($unit->Knowledge() as $ability):
	$experience = $ability->Experience();
	$ability    = $calculus->knowledge($ability->Talent());
	$talents[]  = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($experience) . ')';
endforeach;
$inventory = [];
$payload   = 0;
foreach ($unit->Inventory() as $quantity):
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

?>
<?= $prefix . $unit ?> von <?= $foreign ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?>
<?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?>
<?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?>
<?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>
. Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>
, Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?>
GE.
<?php if ($trades && $trades->HasMarket()): ?>

<?= center('Marktangebote') ?>

<?php if (count($trades->Available()) > 0): ?>
<?php foreach ($trades->Available() as $trade): ?>
<?= $this->template('trade/foreign', $trade) ?>

<?php endforeach ?>
<?php else: ?>
Dieser Händler hat gerade nichts anzubieten.
<?php endif ?>
<?php endif ?>
