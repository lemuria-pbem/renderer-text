<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Unit $unit */
$unit  = $this->variables[0];
$trades = $this->variables[1];
$party = $unit->Party();

?>

<?php if ($party === $this->party): ?>
<?= $this->template('unit/own', $unit, $trades) ?>
<?php elseif ($party->Type() === Type::MONSTER): ?>
<?= $this->template('unit/monster', $unit) ?>
<?php elseif ($this->spyLevel($unit)): ?>
<?= $this->template('unit/spied', $unit, $trades) ?>
<?php else: ?>
<?= $this->template('unit/foreign', $unit, $trades) ?>
<?php endif ?>
