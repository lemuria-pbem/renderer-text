<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Market\Sales;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit = $this->variables[0];
/** @var Sales|null $sales */
$sales = $this->variables[1];
$party = $unit->Party();

?>
<div id="unit-<?= $unit->Id() ?>" class="unit">
	<?php if ($unit->Party() === $this->party): ?>
		<?= $this->template('unit/own', $unit, $sales) ?>
	<?php elseif ($party->Type() === Type::MONSTER): ?>
		<?= $this->template('unit/monster', $unit) ?>
	<?php elseif ($this->spyLevel($unit)): ?>
		<?= $this->template('unit/spied', $unit, $sales) ?>
	<?php else: ?>
		<?= $this->template('unit/foreign', $unit, $sales) ?>
	<?php endif ?>
</div>
