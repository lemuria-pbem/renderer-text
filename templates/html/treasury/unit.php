<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Treasury;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Treasury $treasury */
$treasury = $this->variables[0];

?>
Besondere Gegenstände:
<?php foreach ($treasury as $unicum /* @var Unicum $unicum */): ?>
	<?php if ($treasury->count() > 1): ?>
		<br>
	<?php endif ?>
	<?php if ($unicum->Description()): ?>
		<?php if ($unicum->Name()): ?>
			<?= $unicum->Name() ?> <span class="badge badge-dark"><?= $unicum->Id() ?></span>, <?= $this->composition($unicum->Composition()) ?>:
		<?php else: ?>
			<?= $this->composition($unicum->Composition()) ?> <span class="badge badge-dark"><?= $unicum->Id() ?></span>:
		<?php endif ?>
		<?= $this->template('description', $unicum) ?>
	<?php else: ?>
		<?php if ($unicum->Name()): ?>
			<?= $unicum->Name() ?> <span class="badge badge-dark"><?= $unicum->Id() ?></span>, <?= $this->composition($unicum->Composition()) ?>
		<?php else: ?>
			<?= $this->composition($unicum->Composition()) ?> <span class="badge badge-dark"><?= $unicum->Id() ?></span>
		<?php endif ?>
	<?php endif ?>
<?php endforeach ?>
