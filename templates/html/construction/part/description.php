<?php
/** @noinspection PhpPossiblePolymorphicInvocationInspection */
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Building\Port;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\World\SortMode;
use Lemuria\Renderer\Text\Model\PortSpace;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$building     = $construction->Building();
$inhabitants  = $this->people($construction);
$people       = $inhabitants === 1 ? 'Bewohner' : 'Bewohnern';

$unitsInside = $construction->Inhabitants()->sort(SortMode::BY_PARTY, $this->party);
$owner       = $unitsInside->Owner();

?>
<h5 id="construction-<?= $construction->Id()->Id() ?>">
	<?= $construction->Name() ?>
	<span class="badge badge-secondary"><?= $construction->Id() ?></span>
</h5>
<p>
	<?= $this->get('building', $building) ?> der Größe <?= $this->number($construction->Size()) ?> mit <?= $this->number($inhabitants) ?> <?= $people ?>.
	Besitzer ist
	<?php if (count($unitsInside)): ?>
		<?= $owner->Name() ?> <span class="badge badge-primary"><?= $owner->Id() ?></span>.
	<?php else: ?>
		niemand.
	<?php endif ?>
	<?php if ($building instanceof Port): ?>
		<?= new PortSpace($construction) ?>
	<?php endif ?>
	<?= $this->template('description', $construction) ?>
</p>
