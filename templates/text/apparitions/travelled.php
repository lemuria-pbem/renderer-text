<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Region $region */
$region = $this->variables[0];
$units  = [];
foreach ($region->Residents() as $unit):
	if (!$unit->IsHiding() && !$unit->Construction() && !$unit->Vessel() && !$this->hasTravelled($unit)):
		$units[] = $unit;
	endif;
endforeach

?>
<?php foreach ($units as $unit): ?>

<?= $this->template('unit/foreign', $unit) ?>
<?php endforeach ?>
