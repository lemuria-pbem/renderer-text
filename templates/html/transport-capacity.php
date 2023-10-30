<?php
declare(strict_types = 1);

use function Lemuria\number;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Realm\Wagoner;
use Lemuria\Engine\Fantasya\Travel\Transport;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

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
endif;

?>
<?php if ($isOwn): ?>
	<h5>
		Transportkapazität: <?= number($capacity / 100) ?> GE
		<?php if (count($fleet) > 1): ?>
			<?= $this->capacityLoad($realm) ?>
		<?php endif ?>
	</h5>

	<?php if (!empty($fleet)): ?>
		<ul>
			<?php foreach ($fleet as $id => $wagoner): ?>
				<li>
					<?php if ($wagoners[$id] === Transport::NO_RIDING): ?>
						<?= $wagoner->Unit() ?>: <s><?= number($wagoner->Maximum() / 100) ?> GE</s> (Reittalent zu niedrig)
					<?php else: ?>
						<?= $wagoner->Unit() ?>: <?= number($wagoner->Maximum() / 100) ?> GE<?= $this->capacityLoad($wagoner->Unit()) ?>
					<?php endif ?>
				</li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>
<?php endif ?>
