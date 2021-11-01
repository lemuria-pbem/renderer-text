<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Region $region */
$region     = $this->variables[0];
$outlook    = $this->outlook;
$atlas      = $this->atlas;
$visibility = $atlas->getVisibility($region);

?>
<?php if ($visibility === TravelAtlas::WITH_UNIT): ?>
<?= $this->template('region/with-unit', $region) ?>
<?= $this->template('report', $region) ?>

<?= $this->template('material-pool', $region) ?>
<?php foreach ($region->Estate() as $construction): ?>
<?= $this->template('construction/with-unit', $construction) ?>
<?php endforeach ?>
<?php foreach ($region->Fleet() as $vessel): ?>
<?= $this->template('vessel/with-unit', $vessel) ?>
<?php endforeach ?>
<?= $this->template('apparitions/with-unit', $region) ?>
<?php elseif ($visibility === TravelAtlas::TRAVELLED): ?>
<?= $this->template('region/with-unit', $region) ?>
<?= $this->template('report', $region) ?>
<?php foreach ($region->Estate() as $construction): ?>
<?= $this->template('construction/travelled', $construction) ?>
<?php endforeach ?>
<?php foreach ($region->Fleet() as $vessel): ?>
<?= $this->template('vessel/travelled', $vessel) ?>
<?php endforeach ?>
<?= $this->template('apparitions/travelled', $region) ?>
<?php else: ?>
<?= $this->template('region/neighbour', $region) ?>
<?php endif ?>
