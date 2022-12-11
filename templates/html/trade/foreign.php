<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Market\Trade;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Trade $trade */
$trade   = $this->variables[0];
$goods   = $trade->Goods();
$isPPP   = $goods->IsVariable();
$price   = $trade->Price();
$isVar   = $price->IsVariable();
$isOffer = $trade->Trade() === Trade::OFFER;

?>
<span class="badge text-bg-secondary font-monospace"><?= $trade->Id() ?></span>
<span class="trade-flag ppp-<?= (int)$isPPP ?>" title="<?= $isPPP ? 'Stückpreisangebot' : 'Fixangebot' ?>">∞</span>
<span class="trade-flag var-<?= (int)$isVar ?>" title="<?= $isVar ? 'Verhandlungsbasis' : 'Festpreis' ?>">⇵</span>
<?php if ($isOffer): ?>
	Angebot:
<?php else: ?>
	Gesuch:
<?php endif ?>
<?= $this->deal($goods, $isOffer, true) ?> für <?= $this->deal($price, $isOffer) ?>
