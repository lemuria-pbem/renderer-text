<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Market\Trade;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Trade $trade */
$trade = $this->variables[0];
$price = $trade->Price();

?>
<span class="badge badge-secondary"><?= $trade->Id() ?></span>
<?php if ($trade->Trade() === Trade::OFFER): ?>
	Angebot:
<?php else: ?>
	Gesuch:
<?php endif ?>
<?= $this->deal($trade->Goods()) ?> für <?= $this->deal($price) ?>
<?php if ($price->IsVariable()): ?> (verhandelbar)<?php endif ?>
