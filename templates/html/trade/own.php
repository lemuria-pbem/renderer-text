<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Market\Sales;
use Lemuria\Model\Fantasya\Market\Trade;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Trade $trade */
$trade    = $this->variables[0];
$status   = $this->variables[1];
$isPPP    = $trade->Goods()->IsVariable();
$isVar    = $trade->Price()->IsVariable();
$isRepeat = $trade->IsRepeat();
$isOffer  = $trade->Trade() === Trade::OFFER;
$badge    = match ($status) {
	Sales::FORBIDDEN => 'danger',
	Sales::UNSATISFIABLE => 'light',
	default => 'secondary',
};
$title    = match ($status) {
	Sales::FORBIDDEN => 'Handel untersagt',
	Sales::UNSATISFIABLE => 'Ware nicht vorrätig',
	default => 'Aktives Angebot',
};

?>
<span class="badge text-bg-<?= $badge ?> font-monospace" title="<?= $title ?>"><?= $trade->Id() ?></span>
<span class="trade-flag ppp-<?= (int)$isPPP ?>" title="<?= $isPPP ? 'Stückpreisangebot' : 'Fixangebot' ?>">∞</span>
<span class="trade-flag var-<?= (int)$isVar ?>" title="<?= $isVar ? 'Verhandlungsbasis' : 'Festpreis' ?>">⇵</span>
<span class="trade-flag rep-<?= (int)$isRepeat ?>" title="<?= $isRepeat ? 'wird wiederholt' : 'einmalig angeboten' ?>">🗘</span>
<?php if ($isOffer): ?>
	Angebot:
<?php else: ?>
	Gesuch:
<?php endif ?>
<?= $this->deal($trade->Goods(), $isOffer, true) ?> für <?= $this->deal($trade->Price(), $isOffer, true) ?>
