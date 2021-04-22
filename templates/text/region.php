<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Region $region */
$region   = $this->variables[0];
$outlook  = $this->outlook;
$atlas    = $this->atlas;
$hasUnits = $atlas->getVisibility($region) === TravelAtlas::WITH_UNIT;

?>
<?php if ($hasUnits): ?>
<?= $this->template('region/with-unit', $region) ?>
<?= $this->template('report', $region) ?>

<?= $this->template('material-pool', $region) ?>
<?php foreach ($region->Estate() as $construction): ?>
<?= $this->template('construction', $construction) ?>
<?php endforeach ?>
<?php foreach ($region->Fleet() as $vessel): ?>
<?= $this->template('vessel', $vessel) ?>
<?php endforeach ?>
<?php foreach ($outlook->Apparitions($region) as $unit): ?>
<?= $this->template('unit', $unit) ?>
<?php endforeach ?>
<?php else: ?>
<?= $this->template('region/neighbour', $region) ?>
<?php endif ?>
