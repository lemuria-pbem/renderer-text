<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Extension\Duty;
use Lemuria\Model\Fantasya\Extension\Fee;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$isMaintained = $this->isMaintained($construction);
/** @var Fee $feeExtension */
$feeExtension = $construction->Extensions()->offsetGet(Fee::class);
$fee          = $feeExtension->Fee();
/** @var Duty $dutyExtension */
$dutyExtension = $construction->Extensions()->offsetGet(Duty::class);
$duty          = (int)floor($dutyExtension->Duty() * 100.0);

?>
<h6>Liegegebühr</h6>

<?php if ($isMaintained): ?>
	<?php if ($fee instanceof Quantity): ?>
		<p>Die Liegegebühr beträgt <?= $this->resource($fee) ?> pro Ankerplatz.</p>
	<?php else: ?>
		<p>Es gibt keine Liegegebühr.</p>
	<?php endif ?>
<?php else: ?>
	<p>Der Hafen ist derzeit außer Betrieb.</p>
<?php endif ?>

<?php if ($isMaintained && $duty > 0.0): ?>
	<h6>Zoll auf Luxuswaren</h6>

	<p>Der Hafenmeister erhebt <?= $duty ?> % Zoll auf eingeführte Luxuswaren.</p>
<?php endif ?>
