<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Treasury;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Treasury $treasury */
$treasury = $this->variables[0];
$i        = 0;

?>
Besondere Gegenstände:
<?php foreach ($treasury as $unicum /* @var Unicum $unicum */): ?>
	<?php if ($i++): ?>·<?php endif ?>
	<?= $this->composition($unicum->Composition()) ?> <span class="badge badge-dark"><?= $unicum->Id() ?></span>
<?php endforeach ?>