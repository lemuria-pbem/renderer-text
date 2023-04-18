<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$owner        = $construction->Inhabitants()->Owner()?->Party();

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12 p-0">
			<h5 id="<?= id($construction) ?>">
				<?= $construction->Name() ?>
				<span class="badge text-bg-secondary font-monospace"><?= $construction->Id() ?></span>
			</h5>
			<p>
				<?= $this->translate($construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?>.
				<?php if ($owner): ?>
					Besitzer ist die Partei <?= $owner->Name() ?> <span class="badge text-bg-primary font-monospace"><?= $owner->Id() ?></span>.
				<?php else: ?>
					Besitzer ist niemand.
				<?php endif ?>
				<?= $this->template('description', $construction) ?>
			</p>
		</div>
	</div>
</div>
