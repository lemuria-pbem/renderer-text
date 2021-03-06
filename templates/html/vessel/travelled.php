<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Vessel $vessel */
$vessel  = $this->variables[0];
$captain = $vessel->Passengers()->Owner()?->Party();

?>
<h5><?= $vessel->Name() ?> <span class="badge badge-info"><?= $vessel->Id() ?></span></h5>
<p>
	<?= $this->get('ship', $vessel->Ship()) ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>%.
	Kapitän ist
	<?php if ($captain): ?>
		die Partei <?= $captain->Name() ?> <span class="badge badge-primary"><?= $captain->Id() ?></span>.
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
