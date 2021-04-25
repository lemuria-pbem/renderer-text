<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Vessel $vessel */
$vessel  = $this->variables[0];
$captain = $vessel->Passengers()->Owner()?->Party();

?>

  >> <?= $vessel ?>, <?= $this->get('ship', $vessel->Ship()) ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>
%. Kapitän ist <?= $captain ? $captain : 'niemand' ?>
<?php if (!($vessel->Region()->Landscape() instanceof Ocean)): ?>
<?php if ($vessel->Anchor() === Vessel::IN_DOCK): ?>
. Das Schiff liegt im Dock<?php else: ?>
. Das Schiff ankert im <?= $this->get('world', $vessel->Anchor()) ?>
<?php endif ?>
<?php endif ?>
.<?= line(description($vessel)) ?>
<?= $this->template('report', $vessel) ?>
