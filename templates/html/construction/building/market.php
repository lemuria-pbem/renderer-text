<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Commodity;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Extension\Market;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
/** @var Market $market */
$market     = $construction->Extensions()->offsetGet(Market::class);
$fee        = $market->Fee();
$tradeables = $market->Tradeables();
$goods      = [];
foreach ($tradeables as $commodity /** @var Commodity $commodity */) {
	$goods[] = $this->things($commodity);
}

?>
<h6>Marktordnung</h6>

<?php if ($fee instanceof Quantity): ?>
	<p>Die Marktgebühr beträgt <?= $this->resource($fee) ?>.</p>
<?php elseif (is_float($fee)): ?>
	<p>Die Marktgebühr beträgt <?= (int)round(100.0 * $fee) ?> % des Umsatzes.</p>
<?php else: ?>
	<p>Es gibt keine Marktgebühr.</p>
<?php endif ?>

<?php if (count($goods)): ?>
	<?php if ($tradeables->IsExclusion()): ?>
		<p>Die Marktaufsicht hat den Handel mit den folgenden Waren verboten: <?= implode(', ', $goods) ?></p>
	<?php else: ?>
		<p>Auf diesem Markt dürfen nur die folgenden Waren gehandelt werden: <?= implode(', ', $goods) ?></p>
	<?php endif ?>
<?php else: ?>
	<p>Es gibt keine Handelsbeschränkungen.</p>
<?php endif ?>
