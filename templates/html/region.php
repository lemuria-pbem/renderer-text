<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region   = $this->variables[0];
$outlook  = $this->outlook;
$atlas    = $this->atlas;
$map      = $this->map;
$hasUnits = $atlas->getVisibility($region) === TravelAtlas::WITH_UNIT;

?>
<h4>
	<?= $region->Name() ?>
	<span class="badge badge-light"><?= $map->getCoordinates($region) ?></span>
	<span class="badge badge-secondary"><?= $region->Id() ?></span>
</h4>

<?php if ($hasUnits): ?>
	<?= $this->template('region/with-unit', $region) ?>

	<?php if (count($this->messages($region))): ?>
		<h5>Ereignisse</h5>
		<?= $this->template('report', $region) ?>
	<?php endif ?>

	<?= $this->template('material-pool', $region) ?>

	<?php foreach ($region->Estate() as $construction): ?>
		<?= $this->template('construction', $construction) ?>
	<?php endforeach ?>

	<?php foreach ($region->Fleet() as $vessel): ?>
		<?= $this->template('vessel', $vessel) ?>
	<?php endforeach ?>

	<?php $unitsInRegions = 0 ?>
	<?php foreach ($outlook->Apparitions($region) as $unit /* @var Unit $unit */): ?>
		<?php if ($unitsInRegions++ === 0): ?>
			<h5>Einheiten in der Region</h5>
			<br>
		<?php endif ?>
		<?= $this->template('unit', $unit) ?>
	<?php endforeach ?>
<?php else: ?>
	<?= $this->template('region/neighbour', $region) ?>
<?php endif ?>
