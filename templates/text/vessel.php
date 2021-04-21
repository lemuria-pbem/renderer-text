<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Vessel $vessel */
$vessel     = $this->variables[0];
$passengers = $this->people($vessel)

?>

  >> <?= $vessel ?>, <?= $this->get('ship', $vessel->Ship()) ?> mit <?= $this->number($passengers) ?> <?php if ($passengers === 1): ?>Passagier<?php else: ?>Passagieren<?php endif ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>%, <?php if ($vessel->Space() < 0): ?>überladen mit<?php else: ?>freier Platz<?php endif ?> <?= $this->number((int)ceil(abs($vessel->Space()) / 100)) ?>
 GE. Kapitän ist <?= count($vessel->Passengers()) ? $vessel->Passengers()->Owner() : 'niemand' ?>
<?php if (!($vessel->Region()->Landscape() instanceof Ocean)): ?>
<?php if ($vessel->Anchor() === Vessel::IN_DOCK): ?>
. Das Schiff liegt im Dock<?php else: ?>
. Das Schiff ankert im <?= $this->get('world', $vessel->Anchor()) ?>
<?php endif ?>
<?php endif ?>
.<?= line(description($vessel)) ?>
<?= $this->template('report', $vessel) ?>
<?php foreach ($vessel->Passengers() as $unit): ?>
<?= $this->template('unit', $unit) ?>
<?php endforeach ?>
