<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Engine\Fantasya\Factory\Model\Trades;
use Lemuria\Model\Fantasya\Building\Port;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\Model\PortSpace;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$building     = $construction->Building();
$isMarket     = $building === 'market';
$inhabitants  = $this->people($construction);
$people       = $inhabitants === 1 ? 'Bewohner' : 'Bewohnern';
$treasury     = $construction->Treasury();
$trades       = new Trades($construction);
$additional   = $this->building($trades, $construction);
$fleet        = View::sortedFleet($construction);

?>

  >> <?= $construction ?>, <?= $this->translate($building) ?> der Größe <?= $this->number($construction->Size()) ?> mit <?= $this->number($inhabitants) ?>
 <?= $people ?>. Besitzer ist <?= count($construction->Inhabitants()) ? $construction->Inhabitants()->Owner() : 'niemand' ?>
<?php if ($building instanceof Port): ?>
. <?= new PortSpace($construction) ?><?= line(description($construction)) ?>
<?php else: ?>
.<?= line(description($construction)) ?>
<?php endif ?>
<?php if (!$treasury->isEmpty()): ?><?= $this->template('treasury/region', $treasury) ?><?php endif ?>
<?php if ($additional === 'market'): ?>
<?= $this->template('construction/building/market', $construction, $trades) ?>
<?php elseif ($additional): ?>
<?= $this->template('construction/building/' . $additional, $construction) ?>
<?php endif ?>
<?= $this->template('report', $construction) ?>
<?php foreach ($construction->Inhabitants() as $unit): ?>
<?= $this->template('unit', $unit, $isMarket ? $trades->forUnit($unit) : null) ?>
<?php endforeach ?>
<?php foreach ($fleet as $vessel): ?>
<?= $this->template('vessel/with-unit', $vessel) ?>
<?php endforeach ?>
