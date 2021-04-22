<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit = $this->variables[0];

?>
<div class="unit">
	<?php if ($unit->Party() === $this->party): ?>
		<?= $this->template('unit/own', $unit) ?>
	<?php else: ?>
		<?= $this->template('unit/foreign', $unit) ?>
	<?php endif ?>
</div>
