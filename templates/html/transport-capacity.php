<?php
declare(strict_types = 1);

use function Lemuria\number;
use Lemuria\Engine\Fantasya\Realm\Wagoner;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region   = $this->variables[0];
$realm    = $region->Realm();
$isOwn    = $realm?->Party() === $this->party && $region === $realm->Territory()->Central();
$fleet    = [];
$capacity = 0;
if ($isOwn):
	foreach ($this->census->getPeople($region) as $unit):
		if ($unit->IsTransporting()):
			$wagoner                  = new Wagoner($unit);
			$fleet[$unit->Id()->Id()] = $wagoner;
			$capacity                += $wagoner->Maximum();
		endif;
	endforeach;
	ksort($fleet);
endif;

?>
<?php if ($isOwn): ?>
	<h5>Transportkapazität: <?= number($capacity / 100) ?> GE</h5>

	<?php if (!empty($fleet)): ?>
		<ul>
			<?php foreach ($fleet as $wagoner): ?>
				<li><?= $wagoner->Unit() ?>: <?= number($wagoner->Maximum() / 100) ?> GE</li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>
<?php endif ?>
