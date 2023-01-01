<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Model\World\Direction;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Vessel $vessel */
$vessel  = $this->variables[0];
$ship    = $vessel->Ship();
$size    = $ship->Captain();
$captain = $vessel->Passengers()->Owner()?->Party();

?>

  >> <?= $vessel ?>, <?= $this->get('ship', $ship) ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>
%. Kapitän ist <?= $captain ? 'die Partei ' . $captain : 'niemand' ?>
<?php if (!($vessel->Region()->Landscape() instanceof Navigable)): ?>
<?php if ($vessel->Anchor() === Direction::None): ?>
<?php if ($vessel->Port()): ?>
. Das Schiff liegt im Hafendock und belegt <?= $size > 1 ? $size . ' Ankerplätze' : '1 Ankerplatz' ?><?php else: ?>
. Das Schiff liegt im Dock
<?php endif ?>
<?php else: ?>
<?php if ($vessel->Port()): ?>
. Das Schiff ankert im <?= $this->get('world', $vessel->Anchor()) ?> und belegt <?= $size > 1 ? $size . ' Ankerplätze' : '1 Ankerplatz' ?> im Hafen<?php else: ?>
. Das Schiff ankert im <?= $this->get('world', $vessel->Anchor()) ?>
<?php endif ?>
<?php endif ?>
<?php endif ?>
.<?= line(description($vessel)) ?>
