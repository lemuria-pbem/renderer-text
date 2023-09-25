<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Factory\Model\Visibility;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Region $region */
$region     = $this->variables[0];
$atlas      = $this->atlas;
$visibility = $atlas->getVisibility($region);
$estate     = View::sortedEstate($region);
$fleet      = View::sortedFleet($region);

?>
<?php if ($visibility === Visibility::WithUnit): ?>
<?= $this->template('region/with-unit', $region, true) ?>
<?= $this->template('report', $region) ?>

<?= $this->template('statistics/region', $region) ?>

<?php if ($this->party->Type() === Type::Player): ?>
<?= $this->template('material-pool', $region) ?>
<?= $this->template('transport-capacity', $region) ?>
<?php endif ?>
<?php foreach ($estate as $construction): ?>
<?= $this->template('construction/with-unit', $construction) ?>
<?php endforeach ?>
<?php foreach ($fleet as $vessel): ?>
<?= $this->template('vessel/with-unit', $vessel) ?>
<?php endforeach ?>
<?= $this->template('apparitions/with-unit', $region) ?>
<?php elseif ($visibility === Visibility::Farsight): ?>
<?= $this->template('region/with-unit', $region, false) ?>

<?php foreach ($estate as $construction): ?>
<?= $this->template('construction/foreign', $construction) ?>
<?php endforeach ?>
<?php foreach ($fleet as $vessel): ?>
<?= $this->template('vessel/foreign', $vessel) ?>
<?php endforeach ?>
<?= $this->template('apparitions/with-unit', $region) ?>
<?php elseif ($visibility === Visibility::Travelled): ?>
<?= $this->template('region/with-unit', $region, true) ?>
<?= $this->template('report', $region) ?>
<?php foreach ($estate as $construction): ?>
<?= $this->template('construction/travelled', $construction) ?>
<?php endforeach ?>
<?php foreach ($fleet as $vessel): ?>
<?= $this->template('vessel/travelled', $vessel) ?>
<?php endforeach ?>
<?= $this->template('apparitions/travelled', $region) ?>
<?php elseif ($visibility === Visibility::Lighthouse): ?>
<?= $this->template('region/from-lighthouse', $region) ?>
<?php foreach ($estate as $construction): ?>
<?= $this->template('construction/travelled', $construction) ?>
<?php endforeach ?>
<?php foreach ($fleet as $vessel): ?>
<?= $this->template('vessel/travelled', $vessel) ?>
<?php endforeach ?>
<?= $this->template('apparitions/travelled', $region) ?>
<?php else: ?>
<?= $this->template('region/neighbour', $region) ?>
<?php endif ?>
