<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\description;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Factory\Model\Orders;
use Lemuria\Engine\Fantasya\Factory\Model\Trades;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Unit $unit */
$unit = $this->variables[0];
/** @var Trades|null $trades */
$trades    = $this->variables[1];
$prefix    = $unit->Construction() || $unit->Vessel() ? '   * ' : '  -- ';
$aura      = $unit->Aura();
$disguised = $unit->Disguise();
$calculus  = new Calculus($unit);
$hitpoints = $calculus->hitpoints();
$health    = (int)floor($unit->Health() * $hitpoints);
$payload   = 0;
$orders    = new Orders($unit);

$talents    = [];
$statistics = $this->talentStatistics(Subject::Talents, $unit);
foreach ($unit->Knowledge() as $ability):
	$experience = $ability->Experience();
	$talent     = $ability->Talent();
	$ability    = $calculus->knowledge($talent);
	$knowledge  = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level();
	$change     = $statistics[getClass($talent)] ?? 0;
	if ($change > 0) {
		$knowledge .= ' (+' . $change . '/';
	} elseif ($change < 0) {
		$knowledge .= ' (' . $change . '/';
	} else {
		$knowledge .= ' (';
	}
	$knowledge .= Ability::getLevel($experience) . '/' . $this->number($experience) . ')';
	$talents[]  = $knowledge;
endforeach;

$inventory = [];
foreach ($unit->Inventory() as $quantity):
	$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
	$payload     += $quantity->Weight();
endforeach;
$n = count($inventory);
if ($n > 1):
	$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
	unset($inventory[$n - 1]);
endif;

$treasury = $unit->Treasury();
foreach ($treasury as $unicum):
	$payload += $unicum->Composition()->Weight();
endforeach;

$weight = (int)ceil($payload / 100);
$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);

$spells       = [];
$battleSpells = $unit->BattleSpells();
if ($battleSpells):
	$preparation = $battleSpells->Preparation();
	if ($preparation):
		$spells[] = $this->get('spell', $preparation->Spell()) . ' (' . $preparation->Level() . ')';
	endif;
	$combat = $battleSpells->Combat();
	if ($combat):
		$spells[] = $this->get('spell', $combat->Spell()) . ' (' . $combat->Level() . ')';
	endif;
endif;

?>
<?= $prefix . $unit ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?>
<?php if ($aura): ?>, Aura <?= $aura->Aura() ?>/<?= $aura->Maximum() ?><?php endif ?>
, <?= $this->battleRow($unit) ?>, <?= $this->health($unit) ?> (<?= $health ?>/<?= $hitpoints ?>)<?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?>
<?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?>
<?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?>
<?php if (!$unit->IsLooting()): ?>, sammelt nicht<?php endif ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>
<?php if ($treasury->isEmpty()): ?>. <?php else: ?><?= $this->template('treasury/unit', $treasury) ?><?php endif ?>
Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>
, Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?>
 GE.
<?php if (!empty($spells)): ?>Eingesetzte Kampfzauber: <?= implode(', ', $spells) ?>
.
<?php endif ?>
<?php if ($trades && $trades->HasMarket() && $trades->count() > 0): ?>

<?= center('Aktuelle Marktangebote') ?>

<?php foreach ($trades->Available() as $trade): ?>
<?= $this->template('trade/own', $trade) ?>

<?php endforeach ?>
<?php foreach ($trades->Impossible() as $trade): ?>
nicht vorrätig: <?= $this->template('trade/own', $trade) ?>

<?php endforeach ?>
<?php foreach ($trades->Forbidden() as $trade): ?>
Handel untersagt: <?= $this->template('trade/own', $trade) ?>

<?php endforeach ?>
<?php elseif ($trades && $trades->count() > 0): ?>

<?= center('Angebote für den Markthandel') ?>

<?php foreach ($trades->Available() as $trade): ?>
<?= $this->template('trade/own', $trade) ?>

<?php endforeach ?>
<?php foreach ($trades->Impossible() as $trade): ?>
nicht vorrätig: <?= $this->template('trade/own', $trade) ?>

<?php endforeach ?>
<?php elseif ($trades && $trades->HasMarket()): ?>

<?= center('Aktuelle Marktangebote') ?>

Wir haben aktuell nichts anzubieten.
<?php endif ?>
<?php if (!empty($orders->comments)): ?>

Notizen:
<?php foreach ($orders->comments as $line): ?>
 „<?= $line ?>“
<?php endforeach ?>
<?php endif ?>
<?= $this->template('report', $unit) ?>
