<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit    = $this->variables[0];
$census  = $this->census;
$foreign = $census->getParty($unit);

?>
<h6>
	<?= $unit->Name() ?> <span class="badge badge-primary"><?= $unit->Id() ?></span>
	<?php if ($foreign): ?>
		von <?= $foreign->Name() ?> <span class="badge badge-secondary"><?= $foreign->Id() ?></span>
	<?php else: ?>
		(unbekannte Partei)
	<?php endif ?>
</h6>
<p>
	<?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
	<?= $unit->Description() ?>
</p>
