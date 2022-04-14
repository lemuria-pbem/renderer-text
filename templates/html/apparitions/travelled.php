<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\p3;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region = $this->variables[0];
$units  = [];
foreach ($region->Residents() as $unit /* @var Unit $unit */) {
	if (!$unit->IsHiding() && !$unit->Construction() && !$unit->Vessel() && !$this->hasTravelled($unit)) {
		$units[] = $unit;
	}
}

$i = 0;

?>
<?php if (!empty($units)): ?>
	<h5>Einheiten in der Region</h5>
	<br>
	<div class="container-fluid">
		<div class="row">
			<?php foreach ($units as $unit): ?>
			<div class="col-12 col-md-6 col-xl-4 <?= p3(++$i) ?>">
				<?= $this->template('unit/foreign', $unit) ?>
			</div>
			<?php endforeach ?>
		</div>
	</div>
<?php endif ?>
