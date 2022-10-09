<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\World\Direction;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Vessel $vessel */
$vessel  = $this->variables[0];
$ship    = $vessel->Ship();
$size    = $ship->Captain();
$captain = $vessel->Passengers()->Owner()?->Party();

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<h5 id="vessel-<?= $vessel->Id()->Id() ?>"><?= $vessel->Name() ?> <span class="badge badge-info"><?= $vessel->Id() ?></span></h5>
			<p>
				<?= $this->get('ship', $ship) ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>%.
				Kapitän ist
				<?php if ($captain): ?>
					die Partei <?= $captain->Name() ?> <span class="badge badge-primary"><?= $captain->Id() ?></span>.
				<?php else: ?>
					niemand.
				<?php endif ?>
				<?php if (!($vessel->Region()->Landscape() instanceof Ocean)): ?>
					<?php if ($vessel->Anchor() === Direction::NONE): ?>
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
