<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\p3;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\SortMode;

/** @var Html $this */

/** @var Region $region */
$region         = $this->variables[0];
$outlook        = $this->outlook;
$apparitions    = $outlook->getApparitions($region)->sort(SortMode::ByParty, $this->party);
$unitsInRegions = 0;

?>
<?php foreach ($apparitions as $unit /* @var Unit $unit */): ?>
	<?php if ($unitsInRegions++ === 0): ?>
		<h5>Einheiten in der Region</h5>
		<br>
		<div class="container-fluid">
		<div class="row">
	<?php endif ?>
		<div class="col-12 col-md-6 col-xl-4 <?= p3($unitsInRegions) ?>">
			<?= $this->template('unit', $unit) ?>
		</div>
<?php endforeach ?>
</div>
</div>
