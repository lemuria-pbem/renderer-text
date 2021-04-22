<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];

?>
<h5>
	<?= $construction->Name() ?>
	<span class="badge badge-secondary"><?= $construction->Id() ?></span>
</h5>
<p>
	<?= $this->get('building', $construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?> mit <?= $this->number($this->people($construction)) ?> Bewohnern.
	Besitzer ist
	<?php if (count($construction->Inhabitants())): ?>
		<?= $construction->Inhabitants()->Owner()->Name() ?> <span class="badge badge-primary"><?= $construction->Inhabitants()->Owner()->Id() ?></span>.
	<?php else: ?>
		niemand.
	<?php endif ?>
	<?= $construction->Description() ?>
</p>

<?php if (count($this->messages($construction))): ?>
	<h6>Ereignisse</h6>
	<?= $this->template('report', $construction) ?>
<?php endif ?>

<?php foreach ($construction->Inhabitants() as $unit): ?>
	<?= $this->template('unit', $unit) ?>
<?php endforeach ?>
