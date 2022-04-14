<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\description;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Unit $unit */
$unit      = $this->variables[0];
$prefix    = $unit->Construction() || $unit->Vessel() ? '   * ' : '  -- ';
$aura      = $unit->Aura();
$disguised = $unit->Disguise();
$calculus  = new Calculus($unit);
$hitpoints = $calculus->hitpoints();
$health    = (int)floor($unit->Health() * $hitpoints);
$payload   = 0;

$talents    = [];
$statistics = $this->talentStatistics(Subject::Talents, $unit);
foreach ($unit->Knowledge() as $ability /* @var Ability $ability */):
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
	$knowledge .= $this->number($experience) . ')';
	$talents[]  = $knowledge;
endforeach;

$inventory = [];
foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */):
	$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
	$payload     += $quantity->Weight();
endforeach;
$n = count($inventory);
if ($n > 1):
	$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
	unset($inventory[$n - 1]);
endif;

$treasury = $unit->Treasury();
foreach ($treasury as $unicum /* @var Unicum $unicum */):
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
<?= $this->template('report', $unit) ?>
