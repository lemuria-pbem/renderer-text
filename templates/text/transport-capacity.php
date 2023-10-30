<?php
declare(strict_types = 1);

use function Lemuria\number;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Realm\Wagoner;
use Lemuria\Engine\Fantasya\Travel\Transport;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Region $region */
$region   = $this->variables[0];
$realm    = $region->Realm();
$isOwn    = $realm?->Party() === $this->party && $region === $realm->Territory()->Central();
if ($isOwn):
	$fleet    = [];
	$wagoners = [];
	$capacity = 0;
	foreach ($this->census->getPeople($region) as $unit):
		if ($unit->IsTransporting()):
			$calculus      = new Calculus($unit);
			$transport     = Transport::check($calculus->getTrip());
			$wagoner       = new Wagoner($unit);
			$id            = $unit->Id()->Id();
			$fleet[$id]    = $wagoner;
			$wagoners[$id] = $transport;
			if ($transport === Transport::LAND):
				$capacity += $wagoner->Maximum();
			endif;
		endif;
	endforeach;
	ksort($fleet);
	$total = ($capacity > 0 ? 'insgesamt ' : '') . number($capacity / 100) . ' GE';
	if (count($fleet) > 1):
		$total .= ' ' . $this->capacityLoad($realm);
	endif;
endif;

?>
<?php if ($isOwn): ?>

Transportkapazität: <?= $total ?>

<?php foreach ($fleet as $id => $wagoner): ?>
<?php if ($wagoners[$id] === Transport::NO_RIDING): ?>
<?= $wagoner->Unit() ?> (<?= number($wagoner->Maximum() / 100) ?> GE): Reittalent zu niedrig
<?php else: ?>
<?= $wagoner->Unit() ?>: <?= number($wagoner->Maximum() / 100) ?> GE<?= $this->capacityLoad($wagoner->Unit()) ?>

<?php endif ?>
<?php endforeach ?>
<?php endif ?>
