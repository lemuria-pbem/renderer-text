<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Engine\Fantasya\Command\Trespass\Enter;
use Lemuria\Model\Fantasya\Building\Port;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\Model\PortSpace;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\SortMode;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$building     = $construction->Building();
$canEnter     = !in_array($building::class, Enter::FORBIDDEN);
$inhabitants  = $this->people($construction);
$people       = $inhabitants === 1 ? 'Bewohner' : 'Bewohnern';

$unitsInside = $construction->Inhabitants()->sort(SortMode::ByParty, $this->party);
$hasUnits    = !$unitsInside->isEmpty();
$owner       = $unitsInside->Owner();

?>
<h5 id="<?= id($construction) ?>">
	<?= $construction->Name() ?>
	<span class="badge text-bg-secondary font-monospace"><?= $construction->Id() ?></span>
</h5>
<p>
	<?php if ($canEnter || $hasUnits): ?>
		<?= $this->translate($building) ?> der Größe <?= $this->number($construction->Size()) ?> mit <?= $this->number($inhabitants) ?> <?= $people ?>.
		Besitzer ist
		<?php if ($hasUnits): ?>
			<?= $owner->Name() ?> <span class="badge text-bg-primary font-monospace"><?= $owner->Id() ?></span>.
		<?php else: ?>
			niemand.
		<?php endif ?>
		<?php if ($building instanceof Port): ?>
			<?= new PortSpace($construction) ?>
		<?php endif ?>
	<?php else: ?>
		<?= $this->translate($building) ?> der Größe <?= $this->number($construction->Size()) ?>.
	<?php endif ?>
	<?= $this->template('description', $construction) ?>
</p>
