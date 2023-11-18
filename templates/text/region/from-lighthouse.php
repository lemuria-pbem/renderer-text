<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Model\Fantasya\Landscape\Lake;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Region $region */
$region     = $this->variables[0];
$map        = $this->map;
$landscape  = $region->Landscape();
$name       = $region->Name();
$neighbours = $this->neighbours($region);

?>
<?php if ($landscape instanceof Ocean && $name === 'Ozean' || $landscape instanceof Lake && $name === 'See'): ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>, vom Leuchtturm gesehen.
<?php else: ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>, <?= $this->translate($landscape) ?>, vom Leuchtturm gesehen.<?= $this->template('quotas', $region, true) ?>
<?php endif ?>

<?= ucfirst(implode(', ', $neighbours)) ?>
.<?= line(description($region)) ?>
