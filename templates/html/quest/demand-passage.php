<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Scenario\Quest;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\Scenario\Fantasya\Quest\Controller\DemandPassage;

/** @var Html $this */

/** @var Quest $quest */
$quest = $this->variables[0];
$unit  = $quest->Unit();
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
<?= $unit->Name() ?> (Gewicht <?= $weight ?> GE) bietet uns <strong><?= $this->toAndString($payment) ?></strong> für eine Überfahrt von <?= $start->Name() ?> nach <?= $destination->Name() ?>. <span class="badge text-bg-primary font-monospace"><?= $destination->Id() ?></span>.
