<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Region $region */
$region     = $this->variables[0];
$map        = $this->map;
$landscape  = $region->Landscape();
$neighbours = $this->neighbours($region);

?>
<?php if ($landscape instanceof Ocean && $region->Name() === 'Ozean'): ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>, vom Leuchtturm gesehen.
<?php else: ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>, <?= $this->get('landscape', $region->Landscape()) ?>, vom Leuchtturm gesehen.
<?php endif ?>

<?= ucfirst(implode(', ', $neighbours)) ?>
.<?= line(description($region)) ?>
