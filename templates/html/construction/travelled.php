<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$owner        = $construction->Inhabitants()->Owner()?->Party();

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-lg-6 pl-0">
			<h5>
				<?= $construction->Name() ?>
				<span class="badge badge-secondary"><?= $construction->Id() ?></span>
			</h5>
			<p>
				<?= $this->get('building', $construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?>.
				<?php if ($owner): ?>
					Besitzer ist die Partei <?= $owner->Name() ?> <span class="badge badge-primary"><?= $owner->Id() ?></span>.
				<?php else: ?>
					Besitzer ist niemand.
				<?php endif ?>
				<?= $construction->Description() ?>
			</p>
		</div>
		<div class="col-12 col-lg-6 pr-0">
			<?php if (count($this->messages($construction))): ?>
				<h6>Ereignisse</h6>
				<?= $this->template('report', $construction) ?>
			<?php endif ?>
		</div>
	</div>
</div>
