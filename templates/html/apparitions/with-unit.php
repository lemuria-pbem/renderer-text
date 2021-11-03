<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region         = $this->variables[0];
$outlook        = $this->outlook;
$unitsInRegions = 0;

?>
<?php foreach ($outlook->Apparitions($region) as $unit /* @var Unit $unit */): ?>
	<?php if ($unitsInRegions++ === 0): ?>
		<h5>Einheiten in der Region</h5>
		<br>
	<?php endif ?>
	<?= $this->template('unit', $unit) ?>
<?php endforeach ?>
