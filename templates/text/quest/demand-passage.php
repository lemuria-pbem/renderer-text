<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Scenario\Quest;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;
use Lemuria\Scenario\Fantasya\Quest\Controller\DemandPassage;

/** @var Text $this */

/** @var Quest $quest */
$quest = $this->variables[0];
/** @var Unit|null $person */
$person = $this->variables[1];
$unit  = $quest->Owner();
/** @var DemandPassage $controller */
$controller = $quest->Controller()->setPayload($quest);
$start      = $unit->Region();
$weight     = $unit->Size() * $unit->Race()->Weight();
$payload    = 0;
foreach ($unit->Inventory() as $quantity) {
	$payload += $quantity->Weight();
}
$payment = [];
foreach ($controller->Payment() as $quantity) {
	$payload  -= $quantity->Weight();
	$payment[] = $this->resource($quantity);
}
$weight = (int)ceil(($weight + max(0, $payload)) / 100);

?>
[<?= $quest->Id() ?>] Schiffspassage gesucht - von <?= $unit ?>

<?php if ($quest->isAssignedTo($person)): ?>
<?= $unit->Name() ?> (Gewicht <?= $weight ?> GE) hat uns <?= $this->toAndString($payment) ?> für eine Überfahrt nach <?= $controller->Destination() ?> gezahlt.
<?= $this->template('quest/quest-assigned-to', $quest, $person) ?>
<?php else: ?>
<?= $unit->Name() ?> (Gewicht <?= $weight ?> GE) bietet uns <?= $this->toAndString($payment) ?> für eine Überfahrt von <?= $start->Name() ?> nach <?= $controller->Destination() ?>.
<?php endif ?>

