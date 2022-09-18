<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Market\Trade;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Trade $trade */
$trade = $this->variables[0];

?>
<span class="badge badge-secondary"><?= $trade->Id() ?></span>
<?php if ($trade->Trade() === Trade::OFFER): ?>
	Angebot:
<?php else: ?>
	Gesuch:
<?php endif ?>
<?= $this->deal($trade->Goods(), true) ?> für <?= $this->deal($trade->Price(), true) ?>
<?php if ($trade->IsRepeat()): ?>
	(wiederholt)
<?php endif ?>
