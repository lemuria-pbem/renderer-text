<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Market\Trade;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Trade $trade */
$trade = $this->variables[0];
$price = $trade->Price();

?>
<?php if ($trade->Trade() === Trade::OFFER): ?>Angebot <?php else: ?>Gesuch <?php endif ?>
[<?= $trade->Id() ?>]: <?= $this->deal($trade->Goods(), true) ?> für <?= $this->deal($price) ?>
<?php if ($price->IsVariable()): ?> (verhandelbar)<?php endif ?>
