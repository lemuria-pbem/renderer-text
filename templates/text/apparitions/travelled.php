<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Region $region */
$region    = $this->variables[0];
$travelled = $this->outlook->getTravelled($region);

?>
<?php foreach ($travelled as $unit): ?>

<?= $this->template('unit/foreign', $unit) ?>
<?php endforeach ?>
