<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Factory\Model\Visibility;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region     = $this->variables[0];
$atlas      = $this->atlas;
$map        = $this->map;
$type       = $this->party->Type();
$visibility = $atlas->getVisibility($region);
if ($visibility === Visibility::WithUnit) {
	$people        = $this->census->getPeople($region);
	$qualification = $this->qualificationStatistics(Subject::Qualification, $people->getFirst());
}
$estate = View::sortedEstate($region);
$fleet = View::sortedFleet($region);


?>
<?php if ($visibility === Visibility::WithUnit): ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 col-lg-6 col-xl-4 p-0 pe-lg-3">
				<h4 id="region-<?= $region->Id()->Id() ?>">
					<?= $region->Name() ?>
					<span class="badge text-bg-light"><?= $map->getCoordinates($region) ?></span>
					<span class="badge text-bg-secondary font-monospace"><?= $region->Id() ?></span>
				</h4>
				<?= $this->template('region/with-unit', $region) ?>
			</div>
			<div class="col-12 col-lg-6 col-xl-4 p-0 ps-lg-3 pe-xl-3">
				<?php if (count($this->messages($region))): ?>
					<h5>Ereignisse</h5>
					<?= $this->template('report/region', $region) ?>
				<?php endif ?>
			</div>
			<?php if ($type === Type::Player): ?>
				<div class="col-12 col-xl-4 p-0 ps-xl-3 pe-xl-0">
					<?= $this->template('material-pool', $region) ?>
				</div>
			<?php endif ?>
		</div>
	</div>
	<?php if ($type === Type::Player && !empty($qualification)): ?>
		<div class="table-responsive d-md-none">
			<?= $this->template('statistics/qualification', $qualification, 1) ?>
		</div>
		<div class="table-responsive d-none d-md-block d-lg-none">
			<?= $this->template('statistics/qualification', $qualification, 2) ?>
		</div>
		<div class="table-responsive d-none d-lg-block d-xl-none">
			<?= $this->template('statistics/qualification', $qualification, 2) ?>
		</div>
		<div class="table-responsive d-none d-xl-block">
			<?= $this->template('statistics/qualification', $qualification, 3) ?>
		</div>
	<?php endif ?>
	<?php foreach ($estate as $construction): ?>
		<?= $this->template('construction/with-unit', $construction) ?>
	<?php endforeach ?>
	<?php foreach ($fleet as $vessel): ?>
		<?= $this->template('vessel/with-unit', $vessel) ?>
	<?php endforeach ?>
	<?= $this->template('apparitions/with-unit', $region) ?>
<?php elseif ($visibility === Visibility::Farsight): ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 col-lg-6 col-xl-4 p-0 pe-lg-3">
				<h4 id="region-<?= $region->Id()->Id() ?>">
					<?= $region->Name() ?>
					<span class="badge text-bg-light"><?= $map->getCoordinates($region) ?></span>
					<span class="badge text-bg-secondary font-monospace"><?= $region->Id() ?></span>
				</h4>
				<?= $this->template('region/with-unit', $region) ?>
			</div>
		</div>
	</div>
	<?php foreach ($estate as $construction): ?>
		<?= $this->template('construction/farsight', $construction) ?>
	<?php endforeach ?>
	<?php foreach ($fleet as $vessel): ?>
		<?= $this->template('vessel/farsight', $vessel) ?>
	<?php endforeach ?>
	<?= $this->template('apparitions/with-unit', $region) ?>
<?php elseif ($visibility === Visibility::Travelled): ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 col-lg-6 ps-0">
				<h4 id="region-<?= $region->Id()->Id() ?>">
					<?= $region->Name() ?>
					<span class="badge text-bg-light"><?= $map->getCoordinates($region) ?></span>
					<span class="badge text-bg-secondary font-monospace"><?= $region->Id() ?></span>
				</h4>
				<?= $this->template('region/with-unit', $region) ?>
			</div>
			<div class="col-12 col-lg-6 pe-0">
				<?php if (count($this->messages($region))): ?>
					<h5>Ereignisse</h5>
					<?= $this->template('report/region', $region) ?>
				<?php endif ?>
			</div>
		</div>
	</div>
	<?php foreach ($estate as $construction): ?>
		<?= $this->template('construction/travelled', $construction) ?>
	<?php endforeach ?>
	<?php foreach ($fleet as $vessel): ?>
		<?= $this->template('vessel/travelled', $vessel) ?>
	<?php endforeach ?>
	<?= $this->template('apparitions/travelled', $region) ?>
<?php elseif ($visibility === Visibility::Lighthouse): ?>
	<h4 id="region-<?= $region->Id()->Id() ?>">
		<?= $region->Name() ?>
		<span class="badge text-bg-light"><?= $map->getCoordinates($region) ?></span>
		<span class="badge text-bg-secondary font-monospace"><?= $region->Id() ?></span>
	</h4>
	<?= $this->template('region/from-lighthouse', $region) ?>
	<?php foreach ($estate as $construction): ?>
		<?= $this->template('construction/travelled', $construction) ?>
	<?php endforeach ?>
	<?php foreach ($fleet as $vessel): ?>
		<?= $this->template('vessel/travelled', $vessel) ?>
	<?php endforeach ?>
	<?= $this->template('apparitions/travelled', $region) ?>
<?php else: ?>
	<h4 id="region-<?= $region->Id()->Id() ?>">
		<?= $region->Name() ?>
		<span class="badge text-bg-light"><?= $map->getCoordinates($region) ?></span>
		<span class="badge text-bg-secondary font-monospace"><?= $region->Id() ?></span>
	</h4>
	<?= $this->template('region/neighbour', $region) ?>
<?php endif ?>
