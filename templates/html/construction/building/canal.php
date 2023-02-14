<?php
declare (strict_types = 1);

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

?>
<h6>Nutzungsgebühr</h6>

<?php if ($isMaintained): ?>
	<?php if ($fee instanceof Quantity): ?>
		<p>Die Nutzungsgebühr beträgt <?= $this->resource($fee) ?>.</p>
	<?php else: ?>
		<p>Es gibt keine Nutzungsgebühr.</p>
	<?php endif ?>
<?php else: ?>
	<p>Der Kanal ist derzeit nicht nutzbar.</p>
<?php endif ?>
