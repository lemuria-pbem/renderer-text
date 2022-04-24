<?php
declare(strict_types = 1);

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Region $region */
$region       = $this->variables[0];
$people       = $this->census->getPeople($region);
$materialPool = $this->regionPoolStatistics(Subject::RegionPool, $people->getFirst());

?>
<?php if (count($materialPool) > 0): ?>
Materialpool: <?= implode(', ', $materialPool) ?>.
<?php else: ?>
Der Materialpool ist leer.
<?php endif ?>
