<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit      = $this->variables[0];
$party     = $this->party;
$census    = $this->census;
$aura      = $unit->Aura();
$disguised = $unit->Disguise();
$calculus  = new Calculus($unit);
$hitpoints = $calculus->hitpoints();
$health    = (int)floor($unit->Health() * $hitpoints);
$mark      = $this->healthMark($unit);

$talents = [];
foreach ($unit->Knowledge() as $ability /* @var Ability $ability */):
	$experience = $ability->Experience();
	$ability    = $calculus->knowledge($ability->Talent());
	$talents[]  = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($experience) . ')';
endforeach;

$inventory = [];
$payload   = 0;
foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */):
	$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
	$payload     += $quantity->Weight();
endforeach;
$n = count($inventory);
if ($n > 1):
	$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
	unset($inventory[$n - 1]);
endif;
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
<h6>
	<?= $unit->Name() ?> <span class="badge badge-primary"><?= $unit->Id() ?></span>
	<?php if ($mark): ?>
		<span class="badge badge-danger"><?= $mark ?></span>
	<?php endif ?>
</h6>
<p>
	<?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($aura): ?>, Aura <?= $aura->Aura()?>/<?= $aura->Maximum() ?><?php endif ?>, <?= $this->battleRow($unit) ?>,
	<?= $this->health($unit) ?> (<?= $health ?>/<?= $hitpoints ?>)<?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?><?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?><?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
	<?= $unit->Description() ?>
	<br>
	Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>.
	<br>
	Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>,
	Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
	<?php if (!empty($spells)): ?>
		<br>
		Eingesetzte Kampfzauber: <?= implode(', ', $spells) ?>.
	<?php endif ?>
</p>
<?php if (count($this->messages($unit))): ?>
	<?= $this->template('report', $unit) ?>
<?php endif ?>
