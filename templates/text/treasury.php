<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Treasury;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Treasury $treasury */
$treasury = $this->variables[0];

?>
. Besondere Gegenstände: <?php foreach ($treasury as $unicum /* @var Unicum $unicum */): ?>
<?php if ($unicum->Description()): ?>
<?php if ($unicum->Name()): ?>
<?= $unicum->Name() ?> [<?= $unicum->Id() ?>], <?= $this->composition($unicum->Composition()) ?>
<?php else: ?>
<?= $this->composition($unicum->Composition()) ?> [<?= $unicum->Id() ?>
]<?php endif ?>
: <?= $unicum->Description() ?>
 <?php else: ?>
<?php if ($unicum->Name()): ?>
<?= $unicum->Name() ?> [<?= $unicum->Id() ?>], <?= $this->composition($unicum->Composition()) ?>
 <?php else: ?>
<?= $this->composition($unicum->Composition()) ?> [<?= $unicum->Id() ?>
] <?php endif ?>
<?php endif ?>
<?php endforeach ?>