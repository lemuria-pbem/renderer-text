<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Vessel $vessel */
$vessel     = $this->variables[0];
$passengers = $this->people($vessel);
$people     = $passengers === 1 ? 'Passagier' : 'Passagieren';

?>
<h5><?= $vessel->Name() ?> <span class="badge badge-info"><?= $vessel->Id() ?></span></h5>
<p>
	<?= $this->get('ship', $vessel->Ship()) ?> mit <?= $this->number($passengers) ?> <?= $people ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>%, <?php if ($vessel->Space() < 0): ?>überladen mit<?php else: ?>freier Platz<?php endif ?> <?= $this->number((int)ceil(abs($vessel->Space()) / 100)) ?> GE.
	Kapitän ist
	<?php if (count($vessel->Passengers())): ?>
		<?= $vessel->Passengers()->Owner()->Name() ?> <span class="badge badge-primary"><?= $vessel->Passengers()->Owner()->Id() ?></span>.
	<?php else: ?>
		niemand.
	<?php endif ?>
	<?php if (!($vessel->Region()->Landscape() instanceof Ocean)): ?>
		<?php if ($vessel->Anchor() === Vessel::IN_DOCK): ?>
			Das Schiff liegt im Dock.
		<?php else: ?>
			Das Schiff ankert im <?= $this->get('world', $vessel->Anchor()) ?>.
		<?php endif ?>
	<?php endif ?>
	<?= $vessel->Description() ?>
</p>

<?php if (count($this->messages($vessel))): ?>
	<h6>Ereignisse</h6>
	<?= $this->template('report', $vessel) ?>
<?php endif ?>

<?php foreach ($vessel->Passengers() as $unit /* @var Unit $unit */): ?>
	<?= $this->template('unit', $unit) ?>
<?php endforeach ?>
