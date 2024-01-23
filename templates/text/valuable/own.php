<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Market\Deal;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Unicum $unicum */
$unicum = $this->variables[0];
/** @var Deal $price */
$price = $this->variables[1];
$isVar = $price->IsVariable();
$name  = $unicum->Name() ? $unicum->Name() . ' (' . $this->composition($unicum->Composition()) . ')'  : $this->composition($unicum->Composition());

?>
Unikat [<?= $unicum->Id() ?>]: <?= $name ?> für <?= $this->valuable($price, true) ?>
