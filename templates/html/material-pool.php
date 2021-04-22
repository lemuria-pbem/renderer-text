<?php
declare(strict_types = 1);

use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region       = $this->variables[0];
$party        = $this->party;
$intelligence = new Intelligence($region);
$materialPool = [];
foreach ($intelligence->getMaterialPool($party) as $quantity /* @var Quantity $quantity */):
	$materialPool[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
endforeach;

?>
<h5>Materialpool</h5>

<?php if (count($materialPool) > 0): ?>
	<p><?= implode(', ', $materialPool) ?>.</p>
<?php else: ?>
	<p>Der Materialpool ist leer.</p>
<?php endif ?>
