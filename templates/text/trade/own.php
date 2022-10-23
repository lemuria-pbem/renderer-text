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
[<?= $trade->Id() ?>]: <?= $this->deal($trade->Goods(), $isOffer, true) ?> für <?= $this->deal($trade->Price(), $isOffer, true) ?>
<?php if ($trade->IsRepeat()): ?> (wiederholt)<?php endif ?>
