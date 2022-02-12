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
<h4>
	<?= $region->Name() ?>
	<span class="badge badge-light"><?= $map->getCoordinates($region) ?></span>
	<span class="badge badge-secondary"><?= $region->Id() ?></span>
</h4>

<?php if ($visibility === Visibility::WITH_UNIT): ?>
	<?= $this->template('region/with-unit', $region) ?>

	<?php if (count($this->messages($region))): ?>
		<h5>Ereignisse</h5>
		<?= $this->template('report', $region) ?>
	<?php endif ?>

	<?= $this->template('material-pool', $region) ?>

	<?php foreach ($region->Estate() as $construction): ?>
		<?= $this->template('construction/with-unit', $construction) ?>
	<?php endforeach ?>

	<?php foreach ($region->Fleet() as $vessel): ?>
		<?= $this->template('vessel/with-unit', $vessel) ?>
	<?php endforeach ?>

	<?= $this->template('apparitions/with-unit', $region) ?>
<?php elseif ($visibility === Visibility::TRAVELLED): ?>
	<?= $this->template('region/with-unit', $region) ?>

	<?php if (count($this->messages($region))): ?>
		<h5>Ereignisse</h5>
		<?= $this->template('report', $region) ?>
	<?php endif ?>

	<?php foreach ($region->Estate() as $construction): ?>
		<?= $this->template('construction/travelled', $construction) ?>
	<?php endforeach ?>

	<?php foreach ($region->Fleet() as $vessel): ?>
		<?= $this->template('vessel/travelled', $vessel) ?>
	<?php endforeach ?>

	<?= $this->template('apparitions/travelled', $region) ?>
<?php elseif ($visibility === Visibility::LIGHTHOUSE): ?>
	<?= $this->template('region/from-lighthouse', $region) ?>

	<?php foreach ($region->Estate() as $construction): ?>
		<?= $this->template('construction/travelled', $construction) ?>
	<?php endforeach ?>

	<?php foreach ($region->Fleet() as $vessel): ?>
		<?= $this->template('vessel/travelled', $vessel) ?>
	<?php endforeach ?>
<?php else: ?>
	<?= $this->template('region/neighbour', $region) ?>
<?php endif ?>
