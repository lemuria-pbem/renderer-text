<?php
declare(strict_types = 1);

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region       = $this->variables[0];
$people       = $this->census->getPeople($region);
$materialPool = $this->regionPoolStatistics(Subject::RegionPool, $people->getFirst());

?>
<h5>Materialpool</h5>

<?php if (count($materialPool) > 0): ?>
	<p><?= implode('&nbsp;· ', $materialPool) ?></p>
<?php else: ?>
	<p>Der Materialpool ist leer.</p>
<?php endif ?>
