<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Unit $unit */
$unit  = $this->variables[0];
$party = $unit->Party();

?>

<?php if ($party === $this->party): ?>
<?= $this->template('unit/own', $unit) ?>
<?php elseif ($party->Type() === Party::MONSTER): ?>
<?= $this->template('unit/monster', $unit) ?>
<?php elseif ($this->spyLevel($unit)): ?>
<?= $this->template('unit/spied', $unit) ?>
<?php else: ?>
<?= $this->template('unit/foreign', $unit) ?>
<?php endif ?>
