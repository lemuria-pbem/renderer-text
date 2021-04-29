<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Unit $unit */
$unit = $this->variables[0];

?>

<?php if ($unit->Party() === $this->party): ?>
<?= $this->template('unit/own', $unit) ?>
<?php elseif ($this->spyLevel($unit)): ?>
<?= $this->template('unit/spied', $unit) ?>
<?php else: ?>
<?= $this->template('unit/foreign', $unit) ?>
<?php endif ?>
