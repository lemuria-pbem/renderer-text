<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Region $region */
$region     = $this->variables[0];
$outlook    = $this->outlook;

?>
<?php foreach ($outlook->Apparitions($region) as $unit): ?>
<?= $this->template('unit', $unit) ?>
<?php endforeach ?>
