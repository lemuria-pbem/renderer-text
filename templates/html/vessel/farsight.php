<?php
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\p3;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\World\Direction;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\SortMode;

/** @var Html $this */

/** @var Vessel $vessel */
$vessel     = $this->variables[0];
$ship       = $vessel->Ship();
$size       = $ship->Captain();
$passengers = $this->people($vessel);
$people     = $passengers === 1 ? 'Passagier' : 'Passagieren';
$treasury   = $vessel->Treasury();

$unitsInside = $vessel->Passengers()->sort(SortMode::ByParty, $this->party);
$captain     = $unitsInside->Owner();
$i           = 0;

?>
<h5 id="vessel-<?= $vessel->Id()->Id() ?>"><?= $vessel->Name() ?> <span class="badge text-bg-info"><?= $vessel->Id() ?></span></h5>
<p>
	<?= $this->get('ship', $ship) ?> mit <?= $this->number($passengers) ?> <?= $people ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>%, <?php if ($vessel->Space() < 0): ?>überladen mit<?php else: ?>freier Platz<?php endif ?> <?= $this->number((int)ceil(abs($vessel->Space()) / 100)) ?> GE.
	Kapitän ist
	<?php if (count($unitsInside)): ?>
		<?= $captain->Name() ?> <span class="badge text-bg-primary"><?= $captain->Id() ?></span>.
	<?php else: ?>
		niemand.
	<?php endif ?>
	<?php if (!($vessel->Region()->Landscape() instanceof Ocean)): ?>
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
	<?php if (!$treasury->isEmpty()): ?>
		<br>
		<?= $this->template('treasury/vessel', $treasury) ?>
	<?php endif ?>
</p>

<?php if ($unitsInside->count() > 0): ?>
	<div class="container-fluid">
		<div class="row">
			<?php foreach ($unitsInside as $unit /* @var Unit $unit */): ?>
				<div class="col-12 col-md-6 col-xl-4 <?= p3(++$i) ?>">
					<?= $this->template('unit', $unit) ?>
				</div>
			<?php endforeach ?>
		</div>
	</div>
<?php endif ?>
