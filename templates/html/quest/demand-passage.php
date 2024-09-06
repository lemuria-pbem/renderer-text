<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Scenario\Quest;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\Scenario\Fantasya\Quest\Controller\DemandPassage;

/** @var Html $this */

/** @var Quest $quest */
$quest = $this->variables[0];
/** @var Unit|null $person */
$person = $this->variables[1];
$unit  = $quest->Owner();
/** @var DemandPassage $controller */
$controller  = $quest->Controller()->setPayload($quest);
$start       = $unit->Region();
$destination = $controller->Destination();
$weight      = $unit->Size() * $unit->Race()->Weight();
$payload     = 0;
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
<strong>Schiffspassage gesucht</strong> von <a href="#unit-<?= $unit->Id() ?>"><?= $unit->Name() ?></a>&nbsp;<span class="badge text-bg-primary font-monospace"><a href="#unit-<?= $unit->Id() ?>"><?= $unit->Id() ?></a></span>
<br>
<?php if ($quest->isAssignedTo($person)): ?>
	<?= $unit->Name() ?> (Gewicht <?= $weight ?> GE) hat uns <strong><?= $this->toAndString($payment) ?></strong> für eine Überfahrt nach <?= $destination->Name() ?> gezahlt.
	<?= $this->template('quest/quest-assigned-to', $quest, $person) ?>
<?php else: ?>
	<?= $unit->Name() ?> (Gewicht <?= $weight ?> GE) bietet uns <strong><?= $this->toAndString($payment) ?></strong> für eine Überfahrt von <?= $start->Name() ?> nach <?= $destination->Name() ?>.
<?php endif ?>