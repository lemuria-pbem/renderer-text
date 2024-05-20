<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Scenario\Quest;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Quest $quest */
$quest = $this->variables[0];
/** @var Unit|null $person */
$person = $this->variables[1];

?>
<?php if ($person && $quest->isAssignedTo($person)): ?>
	<br>
	<a href="#unit-<?= $person->Id() ?>"><?= $person->Name() ?></a>&nbsp;<span class="badge text-bg-primary font-monospace"><a href="#unit-<?= $person->Id() ?>"><?= $person->Id() ?></a></span> hat den Auftrag übernommen.
<?php endif ?>
