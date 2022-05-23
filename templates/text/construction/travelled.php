<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$owner        = $construction->Inhabitants()->Owner()?->Party();

?>

  >> <?= $construction ?>, <?= $this->get('building', $construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?>
 . Besitzer ist <?= $owner ? 'die Partei ' . $owner : 'niemand' ?>
.<?= line(description($construction)) ?>
<?= $this->template('report', $construction) ?>
