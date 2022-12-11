<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\p3;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\SortMode;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$treasury     = $construction->Treasury();
$unitsInside  = $construction->Inhabitants()->sort(SortMode::ByParty, $this->party);
$i            = 0;

?>
<?php if ($treasury->isEmpty()): ?>
	<?= $this->template('construction/part/description', $construction) ?>
<?php else: ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 col-md-6 p-0 pe-md-3">
				<?= $this->template('construction/part/description', $construction) ?>
			</div>
			<div class="col-12 col-md-6 p-0 ps-md-3">
				<?= $this->template('treasury/construction', $treasury) ?>
			</div>
		</div>
	</div>
<?php endif ?>

<?php if ($unitsInside->count() > 0): ?>
	<div class="container-fluid">
		<div class="row">
		<?php foreach ($unitsInside as $unit): ?>
			<div class="col-12 col-md-6 col-xl-4 <?= p3(++$i) ?>">
				<?= $this->template('unit', $unit) ?>
			</div>
		<?php endforeach ?>
		</div>
	</div>
<?php endif ?>
