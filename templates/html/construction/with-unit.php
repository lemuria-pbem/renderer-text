<?php
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\p3;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Extension\Market;
use Lemuria\Model\Fantasya\Market\Sales;
use Lemuria\Model\World\SortMode;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$treasury     = $construction->Treasury();
$unitsInside  = $construction->Inhabitants()->sort(SortMode::BY_PARTY, $this->party);
$i            = 0;
$m            = count($this->messages($construction));
$h            = $treasury->isEmpty() ? 0 : 1;
$sales        = $construction->Extensions()->offsetExists(Market::class) ? new Sales($construction) : null;
$columns      = 1 + ($m > 0 || $h ? 1 : 0) + ($sales ? 1 : 0);

?>
<?php if ($columns === 1): ?>
	<?= $this->template('construction/part/description', $construction) ?>
<?php else: ?>
	<div class="container-fluid">
		<div class="row">
			<?php if ($columns === 2): ?>
				<div class="col-12 col-md-6 p-0 pr-md-3">
					<?= $this->template('construction/part/description', $construction) ?>
				</div>
				<div class="col-12 col-md-6 p-0 pl-md-3 pt-md-5">
					<?php if ($sales): ?>
						<?= $this->template('construction/building/market', $construction, $sales) ?>
					<?php else: ?>
						<?php if ($h): ?>
							<?= $this->template('treasury/construction', $treasury) ?>
						<?php endif ?>
						<?php if ($m > 0): ?>
							<h6>Ereignisse</h6>
							<?= $this->template('report', $construction) ?>
						<?php endif ?>
					<?php endif ?>
				</div>
			<?php else: ?>
				<div class="col-12 col-md-6 col-xl-4 p-0 pr-md-3">
					<?= $this->template('construction/part/description', $construction) ?>
				</div>
				<div class="col-12 col-md-6 col-xl-4 p-0 pl-md-3 pr-xl-3 pt-md-5">
					<?= $this->template('construction/building/market', $construction, $sales) ?>
				</div>
				<div class="col-12 col-md-6 col-xl-4 p-0 pr-md-4 pl-xl-3 pr-xl-0 pt-xl-5">
					<?php if ($h): ?>
						<?= $this->template('treasury/construction', $treasury) ?>
					<?php endif ?>
					<?php if ($m > 0): ?>
						<h6>Ereignisse</h6>
						<?= $this->template('report', $construction) ?>
					<?php endif ?>
				</div>
			<?php endif ?>
		</div>
	</div>
<?php endif ?>

<?php if ($unitsInside->count() > 0): ?>
	<div class="container-fluid">
		<div class="row">
		<?php foreach ($unitsInside as $unit): ?>
			<div class="col-12 col-md-6 col-xl-4 <?= p3(++$i) ?>">
				<?= $this->template('unit', $unit, $sales) ?>
			</div>
		<?php endforeach ?>
		</div>
	</div>
<?php endif ?>
