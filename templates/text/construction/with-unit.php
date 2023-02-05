<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Engine\Fantasya\Factory\Model\Trades;
use Lemuria\Model\Fantasya\Building\Port;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\Model\PortSpace;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$building     = $construction->Building();
$inhabitants  = $this->people($construction);
$people       = $inhabitants === 1 ? 'Bewohner' : 'Bewohnern';
$treasury     = $construction->Treasury();
$trades       = new Trades($construction);

?>

  >> <?= $construction ?>, <?= $this->get('building', $building) ?> der Größe <?= $this->number($construction->Size()) ?> mit <?= $this->number($inhabitants) ?>
 <?= $people ?>. Besitzer ist <?= count($construction->Inhabitants()) ? $construction->Inhabitants()->Owner() : 'niemand' ?>
<?php if ($building instanceof Port): ?>
. <?= new PortSpace($construction) ?><?= line(description($construction)) ?>
<?php else: ?>
.<?= line(description($construction)) ?>
<?php endif ?>
<?php if (!$treasury->isEmpty()): ?><?= $this->template('treasury/region', $treasury) ?><?php endif ?>
<?php if ($trades->HasMarket()): ?>

<?= $this->template('construction/building/market', $construction, $trades) ?>
<?php endif ?>
<?= $this->template('report', $construction) ?>
<?php foreach ($construction->Inhabitants() as $unit): ?>
<?= $this->template('unit', $unit, $trades->forUnit($unit)) ?>
<?php endforeach ?>
