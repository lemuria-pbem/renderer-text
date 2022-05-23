<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\World\Direction;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Vessel $vessel */
$vessel     = $this->variables[0];
$passengers = $this->people($vessel);
$people     = $passengers === 1 ? 'Passagier' : 'Passagieren';
$treasury   = $vessel->Treasury();

?>

  >> <?= $vessel ?>, <?= $this->get('ship', $vessel->Ship()) ?> mit <?= $this->number($passengers) ?> <?= $people ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>%, <?php if ($vessel->Space() < 0): ?>überladen mit<?php else: ?>freier Platz<?php endif ?> <?= $this->number((int)ceil(abs($vessel->Space()) / 100)) ?>
 GE. Kapitän ist <?= count($vessel->Passengers()) ? $vessel->Passengers()->Owner() : 'niemand' ?>
<?php if (!($vessel->Region()->Landscape() instanceof Ocean)): ?>
<?php if ($vessel->Anchor() === Direction::NONE): ?>
. Das Schiff liegt im Dock<?php else: ?>
. Das Schiff ankert im <?= $this->get('world', $vessel->Anchor()) ?>
<?php endif ?>
<?php endif ?>
.<?= line(description($vessel)) ?>
<?php if (!$treasury->isEmpty()): ?><?= $this->template('treasury/region', $treasury) ?><?php endif ?>
<?= $this->template('report', $vessel) ?>
<?php foreach ($vessel->Passengers() as $unit): ?>
<?= $this->template('unit', $unit) ?>
<?php endforeach ?>
