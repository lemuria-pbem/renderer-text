<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Region $region */
$region     = $this->variables[0];
$outlook    = $this->outlook;

?>
<?php foreach ($outlook->Apparitions($region) as $unit /* @var Unit $unit */): ?>
<?php if (!$this->hasTravelled($unit)):?>
<?= $this->template('unit', $unit) ?>
<?php endif ?>
<?php endforeach ?>
