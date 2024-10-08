<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Model\Fantasya\Building\Port;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\Model\PortSpace;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$building     = $construction->Building();
$owner        = $construction->Inhabitants()->Owner();
$party        = $owner ? $this->census->getParty($owner) : null;
$fleet        = View::sortedFleet($construction);

?>

  >> <?= $construction ?>, <?= $this->translate($building) ?> der Größe <?= $this->number($construction->Size()) ?>
. Besitzer ist <?= $owner ? ($party ? 'die Partei ' . $party : 'eine unbekannte Partei') : 'niemand' ?>
<?php if ($building instanceof Port): ?>
. <?= new PortSpace($construction) ?><?= line(description($construction)) ?>
<?php else: ?>
.<?= line(description($construction)) ?>
<?php endif ?>
<?php foreach ($fleet as $vessel): ?>
<?php if (!$this->hasTravelled($vessel)): ?>
<?= $this->template('vessel/travelled', $vessel) ?>
<?php endif ?>
<?php endforeach ?>
