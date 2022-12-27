<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Market\Trade;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Trade $trade */
$trade   = $this->variables[0];
$price   = $trade->Price();
$isOffer = $trade->Trade() === Trade::OFFER;

?>
<?php if ($isOffer): ?>Angebot <?php else: ?>Gesuch <?php endif ?>
[<?= $trade->Id() ?>]: <?= $this->deal($trade, $trade->Goods(), true) ?> für <?= $this->deal($trade, $price) ?>
<?php if ($price->IsVariable()): ?> (verhandelbar)<?php endif ?>
