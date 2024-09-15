<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\World\Direction;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Vessel $vessel */
$vessel  = $this->variables[0];
$ship    = $vessel->Ship();
$size    = $ship->Captain();
$captain = $vessel->Passengers()->Owner();
$foreign = $captain ? $this->census->getParty($captain) : null;

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12 p-0">
			<h5 id="<?= id($vessel) ?>"><?= $vessel->Name() ?> <span class="badge text-bg-info font-monospace"><?= $vessel->Id() ?></span></h5>
			<p>
				<?= $this->translate($ship) ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>%.
				Kapitän ist
				<?php if ($captain): ?>
					<?php if ($foreign): ?>
						die Partei <?= $foreign->Name() ?> <span class="badge text-bg-primary font-monospace"><?= $foreign->Id() ?></span>.
					<?php else: ?>
						eine unbekannte Partei.
					<?php endif ?>
				<?php else: ?>
					niemand.
				<?php endif ?>
				<?php if (!($vessel->Region()->Landscape() instanceof Navigable)): ?>
					<?php if ($vessel->Anchor() === Direction::None): ?>
						<?php if ($vessel->Port()): ?>
							Das Schiff liegt im Hafendock und belegt <?= $size > 1 ? $size . ' Ankerplätze' : '1 Ankerplatz' ?>.
						<?php else: ?>
							Das Schiff liegt im Dock.
						<?php endif ?>
					<?php else: ?>
						<?php if ($vessel->Port()): ?>
							Das Schiff ankert im <?= $this->get('world', $vessel->Anchor()) ?> und belegt <?= $size > 1 ? $size . ' Ankerplätze' : '1 Ankerplatz' ?> im Hafen.
						<?php else: ?>
							Das Schiff ankert im <?= $this->get('world', $vessel->Anchor()) ?>.
						<?php endif ?>
					<?php endif ?>
				<?php endif ?>
				<?= $this->template('description', $vessel) ?>
			</p>
		</div>
	</div>
</div>
