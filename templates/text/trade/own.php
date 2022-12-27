<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Market\Trade;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Trade $trade */
$trade   = $this->variables[0];
$isOffer = $trade->Trade() === Trade::OFFER;

?>
<?php if ($isOffer): ?>Angebot <?php else: ?>Gesuch <?php endif ?>
[<?= $trade->Id() ?>]: <?= $this->ownDeal($trade, $trade->Goods()) ?> für <?= $this->ownDeal($trade, $trade->Price()) ?>
<?php if ($trade->IsRepeat()): ?> (wiederholt)<?php endif ?>
