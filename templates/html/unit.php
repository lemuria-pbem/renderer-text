<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit   = $this->variables[0];
$trades = $this->variables[1] ?? null;
$party  = $unit->Party();

?>
<div id="<?= id($unit) ?>" class="unit">
	<?php if ($unit->Party() === $this->party): ?>
		<?= $this->template('unit/own', $unit, $trades) ?>
	<?php elseif ($party->Type() === Type::Monster): ?>
		<?= $this->template('unit/monster', $unit) ?>
	<?php elseif ($this->spyLevel($unit)): ?>
		<?= $this->template('unit/spied', $unit, $trades) ?>
	<?php else: ?>
		<?= $this->template('unit/foreign', $unit, $trades) ?>
	<?php endif ?>
</div>
