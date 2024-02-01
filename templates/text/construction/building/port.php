<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Message\Casus;
use Lemuria\Model\Fantasya\Extension\Duty;
use Lemuria\Model\Fantasya\Extension\Fee;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$isMaintained = $this->isMaintained($construction);
/** @var Fee $feeExtension */
$feeExtension = $construction->Extensions()->offsetGet(Fee::class);
$fee          = $feeExtension->Fee();
/** @var Duty $dutyExtension */
$dutyExtension = $construction->Extensions()->offsetGet(Duty::class);
$duty          = (int)floor($dutyExtension->Duty() * 100.0);

$vessels = [];
foreach (View::sortedPortFleet($construction) as $vessel):
	$vessels[] = $this->combineGrammar($vessel->Ship(), 'das', Casus::Nominative) . ' „' . $vessel->Name() . '“';
endforeach;
$v = count($vessels);

?>

<?php if ($isMaintained): ?>
<?php if ($fee instanceof Quantity): ?>
Die Liegegebühr beträgt <?= $this->resource($fee) ?> pro Ankerplatz.
<?php else: ?>
Es gibt keine Liegegebühr.
<?php endif ?>
<?php else: ?>
Der Hafen ist derzeit außer Betrieb.
<?php endif ?>
<?php if ($isMaintained && $duty > 0.0): ?>
Der Hafenmeister erhebt <?= $duty ?> % Zoll auf eingeführte Luxuswaren.
<?php endif ?>
<?php if ($v > 0): ?>
<?php if ($v === 1): ?>
Im Hafen ankert <?= $vessels[0] ?>.
<?php else: ?>
Im Hafen ankern <?= $this->toAndString($vessels) ?>.
<?php endif ?>
<?php else: ?>
Hier liegen derzeit keine Schiffe vor Anker.
<?php endif ?>
