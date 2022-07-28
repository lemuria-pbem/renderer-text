<?php
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\p3;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\World\SortMode;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$inhabitants  = $this->people($construction);
$people       = $inhabitants === 1 ? 'Bewohner' : 'Bewohnern';
$treasury     = $construction->Treasury();

$unitsInside = $construction->Inhabitants()->sort(SortMode::BY_PARTY, $this->party);
$owner       = $unitsInside->Owner();
$i           = 0;

?>
<h5 id="construction-<?= $construction->Id()->Id() ?>">
	<?= $construction->Name() ?>
	<span class="badge badge-secondary"><?= $construction->Id() ?></span>
</h5>
<p>
	<?= $this->get('building', $construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?> mit <?= $this->number($inhabitants) ?> <?= $people ?>.
	Besitzer ist
	<?php if (count($unitsInside)): ?>
		<?= $owner->Name() ?> <span class="badge badge-primary"><?= $owner->Id() ?></span>.
	<?php else: ?>
		niemand.
	<?php endif ?>
	<?= $this->template('description', $construction) ?>
	<?php if (!$treasury->isEmpty()): ?>
		<br>
		<?= $this->template('treasury/construction', $treasury) ?>
	<?php endif ?>
</p>

<?php if ($unitsInside->count() > 0): ?>
	<div class="container-fluid">
		<div class="row">
		<?php foreach ($unitsInside as $unit): ?>
			<div class="col-12 col-md-6 col-xl-4 <?= p3(++$i) ?>">
				<?= $this->template('unit', $unit) ?>
			</div>
		<?php endforeach ?>
		</div>
	</div>
<?php endif ?>
