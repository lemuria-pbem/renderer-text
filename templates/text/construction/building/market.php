<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use Lemuria\Model\Fantasya\Commodity;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Extension\Market;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Construction $construction */
$construction = $this->variables[0];
/** @var Market $market */
$market     = $construction->Extensions()->offsetGet(Market::class);
$fee        = $market->Fee();
$tradeables = $market->Tradeables();
$goods      = [];
foreach ($tradeables as $commodity /* @var Commodity $commodity */) {
	$goods[] = $this->things($commodity);
}

?>
<?= center('Marktordnung') ?>

<?php if ($fee instanceof Quantity): ?>
Die Marktgebühr beträgt <?= $this->resource($fee) ?>.
<?php elseif (is_float($fee)): ?>
Die Marktgebühr beträgt <?= (int)round(100.0 * $fee) ?>% des Umsatzes.
<?php else: ?>
Es gibt keine Marktgebühr.
<?php endif ?>
<?php if (count($goods)): ?>
<?php if ($tradeables->IsExclusion()): ?>
Die Marktaufsicht hat den Handel mit den folgenden Waren verboten: <?= implode(', ', $goods) ?>
<?php else: ?>
Auf diesem Markt dürfen nur die folgenden Waren gehandelt werden: <?= implode(', ', $goods) ?>
<?php endif ?>
<?php else: ?>
Es gibt keine Handelsbeschränkungen.
<?php endif ?>
