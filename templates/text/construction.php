<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Construction $construction */
$construction = $this->variables[0];

?>

  >> <?= $construction ?>, <?= $this->get('building', $construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?> mit <?= $this->number($this->people($construction)) ?>
 Bewohnern. Besitzer ist <?= count($construction->Inhabitants()) ? $construction->Inhabitants()->Owner() : 'niemand' ?>
.<?= line(description($construction)) ?>
<?= $this->template('report', $construction) ?>
<?php foreach ($construction->Inhabitants() as $unit): ?>
<?= $this->template('unit', $unit) ?>
<?php endforeach ?>
