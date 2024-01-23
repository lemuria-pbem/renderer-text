<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Market\Deal;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unicum $unicum */
$unicum = $this->variables[0];
/** @var Deal $price */
$price = $this->variables[1];
$isVar = $price->IsVariable();
$name  = $unicum->Name() ? $unicum->Name() . ' (' . $this->composition($unicum->Composition()) . ')'  : $this->composition($unicum->Composition());

?>
<span class="badge text-bg-magic font-monospace"><?= $unicum->Id() ?></span>
<span class="trade-flag var-<?= (int)$isVar ?>" title="<?= $isVar ? 'Verhandlungsbasis' : 'Festpreis' ?>">⇵</span>
<?= $name ?> für <?= $this->valuable($price, true) ?>
