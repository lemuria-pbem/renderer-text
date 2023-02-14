<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Extension\Fee;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$isMaintained = $this->isMaintained($construction);
/** @var Fee $feeExtension */
$feeExtension = $construction->Extensions()->offsetGet(Fee::class);
$fee          = $feeExtension->Fee();

?>
<?php if ($isMaintained): ?>
<?php if ($fee instanceof Quantity): ?>
Die Nutzungsgebühr beträgt <?= $this->resource($fee) ?>.
<?php else: ?>
Es gibt keine Nutzungsgebühr.
<?php endif ?>
<?php else: ?>
Der Kanal ist derzeit nicht nutzbar.
<?php endif ?>
