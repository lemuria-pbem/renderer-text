<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Unit $unit */
$unit  = $this->variables[0];
$party = $unit->Party();

?>

<?php if ($party === $this->party): ?>
<?= $this->template('shipbuilder/own', $unit, $trades) ?>
<?php elseif ($this->spyLevel($unit)): ?>
<?= $this->template('shipbuilder/spied', $unit, $trades) ?>
<?php else: ?>
<?= $this->template('shipbuilder/foreign', $unit, $trades) ?>
<?php endif ?>
