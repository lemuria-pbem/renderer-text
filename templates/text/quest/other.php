<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Text;
use Lemuria\Model\Fantasya\Scenario\Quest;

/** @var Text $this */

/** @var Quest $quest */
$quest = $this->variables[0];
$unit  = $quest->Owner();

?>
[<?= $quest->Id() ?>] Auftrag von <?= $unit ?>

