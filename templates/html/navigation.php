<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\id;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party     = $this->party;
$isMonster = $party->Type() === Type::Monster;
$census    = $this->census->getAtlas();
$travelLog = $this->travelLog;

?>
<div class="fixed-top vh-100">
	<a id="navbar-toggle" class="btn btn-small btn-light fixed-top" data-bs-toggle="collapse" href="#navbar" role="button" aria-expanded="true" aria-controls="navbar" title="Taste: I">Inhalt</a>
	<a id="toggle-map" class="btn btn-small btn-light fixed-bottom" data-bs-toggle="modal" data-bs-target="#modal-map" title="Taste: W">Karte</a>
	<nav id="navbar" class="navbar navbar-light bg-light collapse">
		<nav class="nav nav-pills flex-column">
			<?php foreach ($travelLog as $continent => $atlas): ?>
				<?php if ($atlas->count() > 0): ?>
					<a class="navbar-brand" href="#<?= id($continent) ?>"><?= $continent->Name() ?></a>
					<?php foreach ($atlas as $region /* @var Region $region */): ?>
						<?php if ($census->has($region->Id())): ?>
							<?php if ($region->Landscape() instanceof Navigable): ?>
								<?php if ($isMonster || !$region->Fleet()->isEmpty()): ?>
									<a class="navigable nav-link" href="#<?= id($region) ?>"><?= $region->Name() ?></a>
								<?php endif ?>
								<?php foreach (View::sortedFleet($region) as $vessel /* @var Vessel $vessel */): ?>
									<a class="vessel nav-link ml-3 py-0" href="#<?= id($vessel) ?>"><?= $vessel->Name() ?></a>
								<?php endforeach ?>
							<?php else: ?>
								<a class="location <?= strtolower(getClass($region->Landscape())) ?> nav-link" href="#<?= id($region) ?>"><?= $region->Name() ?></a>
								<?php if (!$region->Estate()->isEmpty() || !$region->Fleet()->isEmpty()): ?>
									<nav id="nav-<?= id($region) ?>" class="nav nav-pills flex-column">
										<?php foreach (View::sortedEstate($region) as $construction /* @var Construction $construction */): ?>
											<a class="construction nav-link ml-3 py-0" href="#<?= id($construction) ?>"><?= $construction->Name() ?></a>
										<?php endforeach ?>
										<?php foreach (View::sortedFleet($region) as $vessel /* @var Vessel $vessel */): ?>
											<a class="vessel nav-link ml-3 py-0" href="#<?= id($vessel) ?>"><?= $vessel->Name() ?></a>
										<?php endforeach ?>
									</nav>
								<?php endif ?>
							<?php endif ?>
						<?php endif ?>
					<?php endforeach ?>
				<?php endif ?>
			<?php endforeach ?>
		</nav>
	</nav>
</div>
