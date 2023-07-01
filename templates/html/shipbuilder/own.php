<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit = $this->variables[0];
$aura      = $unit->Aura();
$disguised = $unit->Disguise();
$calculus  = new Calculus($unit);
$hitpoints = $calculus->hitpoints();
$health    = (int)floor($unit->Health() * $hitpoints);
$mark      = $this->healthMark($unit);

?>
<h6>
	<?= $unit->Name() ?> <span class="badge text-bg-primary font-monospace"><?= $unit->Id() ?></span>
	<?php if ($mark): ?>
		<span class="badge text-bg-danger"><?= $mark ?></span>
	<?php endif ?>
</h6>
<p>
	<?= $this->number($unit->Size(), $unit->Race()) ?><?php if ($aura): ?>, Aura <?= $aura->Aura()?>/<?= $aura->Maximum() ?><?php endif ?>, <?= $this->battleRow($unit) ?>,
	<?= $this->health($unit) ?> (<?= $health ?>/<?= $hitpoints ?>)<?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?><?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?><?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?><?php if (!$unit->IsLooting()): ?>, sammelt nicht<?php endif ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
	<?= $this->template('description', $unit) ?>
	<br>
	Die Einheit ist mit dem Bau des Schiffes beschäftigt.
</p>