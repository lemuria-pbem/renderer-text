<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Engine\Fantasya\Command\Trespass\Enter;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$building     = $construction->Building();
$canEnter     = !in_array($building::class, Enter::FORBIDDEN);
$owner        = $construction->Inhabitants()->Owner();
$party        = $owner ? $this->census->getParty($owner) : null;
$fleet        = View::sortedFleet($construction);

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12 p-0">
			<h5 id="<?= id($construction) ?>">
				<?= $construction->Name() ?>
				<span class="badge text-bg-secondary font-monospace"><?= $construction->Id() ?></span>
			</h5>
			<p>
				<?= $this->translate($building) ?> der Größe <?= $this->number($construction->Size()) ?>.
				<?php if ($canEnter): ?>
					<?php if ($owner): ?>
						<?php if ($party): ?>
							Besitzer ist die Partei <?= $party->Name() ?> <span class="badge text-bg-primary font-monospace"><?= $party->Id() ?></span>.
						<?php else: ?>
							Besitzer ist eine unbekannte Partei.
						<?php endif ?>
					<?php else: ?>
						Besitzer ist niemand.
					<?php endif ?>
				<?php endif ?>
				<?= $this->template('description', $construction) ?>
			</p>
		</div>
	</div>
</div>

<?php foreach ($fleet as $vessel): ?>
	<?php if (!$this->hasTravelled($vessel)): ?>
		<?= $this->template('vessel/travelled', $vessel) ?>
	<?php endif ?>
<?php endforeach ?>
