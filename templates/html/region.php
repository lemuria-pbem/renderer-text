<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Factory\Model\Visibility;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region     = $this->variables[0];
$atlas      = $this->atlas;
$map        = $this->map;
$visibility = $atlas->getVisibility($region);

?>
<?php if ($visibility === Visibility::WITH_UNIT): ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 col-lg-6 col-xl-4 p-0 pr-lg-3">
				<h4 id="region-<?= $region->Id()->Id() ?>">
					<?= $region->Name() ?>
					<span class="badge badge-light"><?= $map->getCoordinates($region) ?></span>
					<span class="badge badge-secondary"><?= $region->Id() ?></span>
				</h4>
				<?= $this->template('region/with-unit', $region) ?>
			</div>
			<div class="col-12 col-lg-6 col-xl-4 p-0 pl-lg-3 pr-xl-3">
				<?php if (count($this->messages($region))): ?>
					<h5>Ereignisse</h5>
					<?= $this->template('report', $region) ?>
				<?php endif ?>
			</div>
			<div class="col-12 col-xl-4 p-0 pl-xl-3 pr-xl-0">
				<?= $this->template('material-pool', $region) ?>
			</div>
		</div>
	</div>
	<?php foreach ($region->Estate() as $construction): ?>
		<?= $this->template('construction/with-unit', $construction) ?>
	<?php endforeach ?>
	<?php foreach ($region->Fleet() as $vessel): ?>
		<?= $this->template('vessel/with-unit', $vessel) ?>
	<?php endforeach ?>
	<?= $this->template('apparitions/with-unit', $region) ?>
<?php elseif ($visibility === Visibility::FARSIGHT): ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 col-lg-6 col-xl-4 p-0 pr-lg-3">
				<h4 id="region-<?= $region->Id()->Id() ?>">
					<?= $region->Name() ?>
					<span class="badge badge-light"><?= $map->getCoordinates($region) ?></span>
					<span class="badge badge-secondary"><?= $region->Id() ?></span>
				</h4>
				<?= $this->template('region/with-unit', $region) ?>
			</div>
		</div>
	</div>
	<?php foreach ($region->Estate() as $construction): ?>
		<?= $this->template('construction/farsight', $construction) ?>
	<?php endforeach ?>
	<?php foreach ($region->Fleet() as $vessel): ?>
		<?= $this->template('vessel/farsight', $vessel) ?>
	<?php endforeach ?>
	<?= $this->template('apparitions/with-unit', $region) ?>
<?php elseif ($visibility === Visibility::TRAVELLED): ?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 col-lg-6 pl-0">
				<h4 id="region-<?= $region->Id()->Id() ?>">
					<?= $region->Name() ?>
					<span class="badge badge-light"><?= $map->getCoordinates($region) ?></span>
					<span class="badge badge-secondary"><?= $region->Id() ?></span>
				</h4>
				<?= $this->template('region/with-unit', $region) ?>
			</div>
			<div class="col-12 col-lg-6 pr-0">
				<?php if (count($this->messages($region))): ?>
					<h5>Ereignisse</h5>
					<?= $this->template('report', $region) ?>
				<?php endif ?>
			</div>
		</div>
	</div>
	<?php foreach ($region->Estate() as $construction): ?>
		<?= $this->template('construction/travelled', $construction) ?>
	<?php endforeach ?>
	<?php foreach ($region->Fleet() as $vessel): ?>
		<?= $this->template('vessel/travelled', $vessel) ?>
	<?php endforeach ?>
	<?= $this->template('apparitions/travelled', $region) ?>
<?php elseif ($visibility === Visibility::LIGHTHOUSE): ?>
	<h4 id="region-<?= $region->Id()->Id() ?>">
		<?= $region->Name() ?>
		<span class="badge badge-light"><?= $map->getCoordinates($region) ?></span>
		<span class="badge badge-secondary"><?= $region->Id() ?></span>
	</h4>
	<?= $this->template('region/from-lighthouse', $region) ?>
	<?php foreach ($region->Estate() as $construction): ?>
		<?= $this->template('construction/travelled', $construction) ?>
	<?php endforeach ?>
	<?php foreach ($region->Fleet() as $vessel): ?>
		<?= $this->template('vessel/travelled', $vessel) ?>
	<?php endforeach ?>
<?php else: ?>
	<h4 id="region-<?= $region->Id()->Id() ?>">
		<?= $region->Name() ?>
		<span class="badge badge-light"><?= $map->getCoordinates($region) ?></span>
		<span class="badge badge-secondary"><?= $region->Id() ?></span>
	</h4>
	<?= $this->template('region/neighbour', $region) ?>
<?php endif ?>
