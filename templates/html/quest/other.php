<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;
use Lemuria\Model\Fantasya\Scenario\Quest;

/** @var Html $this */

/** @var Quest $quest */
$quest = $this->variables[0];
$unit  = $quest->Owner();

?>
Auftrag von <a href="#unit-<?= $unit->Id() ?>"><?= $unit->Name() ?></a>&nbsp;<span class="badge text-bg-primary font-monospace"><a href="#unit-<?= $unit->Id() ?>"><?= $unit->Id() ?></a></span>
