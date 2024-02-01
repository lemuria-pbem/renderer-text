<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\p3;
use Lemuria\Engine\Fantasya\Factory\Model\Trades;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\SortMode;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$treasury     = $construction->Treasury();
$unitsInside  = $construction->Inhabitants()->sort(SortMode::ByParty, $this->party);
$i            = 0;
$m            = count($this->messages($construction));
$h            = $treasury->isEmpty() ? 0 : 1;
$trades       = new Trades($construction);
$additional   = $this->building($trades, $construction, Html::LEFT);
$building     = $this->building($trades, $construction, Html::RIGHT);
$isMarket     = $building === 'market';
$columns      = 1 + ($m > 0 || $h ? 1 : 0) + ($building ? 1 : 0);
$fleet        = View::sortedFleet($construction);

?>
<?php if ($columns === 1): ?>
	<?= $this->template('construction/part/description', $construction) ?>
	<?php if ($additional): ?>
		<?= $this->template('construction/additional/' . $additional, $construction) ?>
	<?php endif ?>
<?php else: ?>
	<div class="container-fluid">
		<div class="row">
			<?php /** @noinspection PhpConditionAlreadyCheckedInspection */
			if ($columns === 2): ?>
				<div class="col-12 col-md-6 p-0 pe-md-3">
					<?= $this->template('construction/part/description', $construction) ?>
					<?php if ($additional): ?>
						<?= $this->template('construction/additional/' . $additional, $construction) ?>
					<?php endif ?>
				</div>
				<div class="col-12 col-md-6 p-0 ps-md-3 pt-md-5">
					<?php if ($building): ?>
						<?= $this->template('construction/building/' . $building, $construction) ?>
					<?php else: ?>
						<?php if ($h): ?>
							<?= $this->template('treasury/construction', $treasury) ?>
						<?php endif ?>
						<?php if ($m > 0): ?>
							<h6>Ereignisse</h6>
							<?= $this->template('report/default', $construction) ?>
						<?php endif ?>
					<?php endif ?>
				</div>
			<?php else: ?>
				<div class="col-12 col-md-6 col-xl-4 p-0 pe-md-3">
					<?= $this->template('construction/part/description', $construction) ?>
					<?php if ($additional): ?>
						<?= $this->template('construction/additional/' . $additional, $construction) ?>
					<?php endif ?>
				</div>
				<div class="col-12 col-md-6 col-xl-4 p-0 ps-md-3 pe-xl-3 pt-md-5">
					<?= $this->template('construction/building/' . $building, $construction) ?>
				</div>
				<div class="col-12 col-md-6 col-xl-4 p-0 pe-md-4 ps-xl-3 pe-xl-0 pt-xl-5">
					<?php if ($h): ?>
						<?= $this->template('treasury/construction', $treasury) ?>
					<?php endif ?>
					<?php if ($m > 0): ?>
						<h6>Ereignisse</h6>
						<?= $this->template('report/default', $construction) ?>
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
				<?= $this->template('unit', $unit, $isMarket ? $trades->forUnit($unit) : null) ?>
			</div>
		<?php endforeach ?>
		</div>
	</div>
<?php endif ?>

<?php foreach ($fleet as $vessel): ?>
	<?= $this->template('vessel/with-unit', $vessel) ?>
<?php endforeach ?>
