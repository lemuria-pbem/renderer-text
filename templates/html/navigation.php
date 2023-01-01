<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party     = $this->party;
$census    = $this->census->getAtlas();
$travelLog = $this->travelLog;

?>
<div class="fixed-top vh-100">
	<a id="navbar-toggle" class="btn btn-small btn-light fixed-top" data-bs-toggle="collapse" href="#navbar" role="button" aria-expanded="true" aria-controls="navbar" title="Taste: I">Inhalt</a>
	<nav id="navbar" class="navbar navbar-light bg-light collapse">
		<nav class="nav nav-pills flex-column">
			<?php foreach ($travelLog as $continent => $atlas): ?>
				<?php if ($atlas->count() > 0): ?>
					<a class="navbar-brand" href="#continent-<?= $continent->Id() ?>"><?= $continent->Name() ?></a>
					<?php foreach ($atlas as $region /* @var Region $region */): ?>
						<?php if ($census->has($region->Id())): ?>
							<?php if ($region->Landscape() instanceof Navigable): ?>
								<?php foreach (View::sortedFleet($region) as $vessel /* @var Vessel $vessel */): ?>
									<a class="vessel nav-link pb-0" href="#vessel-<?= $vessel->Id()->Id() ?>"><?= $vessel->Name() ?></a>
								<?php endforeach ?>
							<?php else: ?>
								<a class="region nav-link" href="#region-<?= $region->Id()->Id() ?>"><?= $region->Name() ?></a>
								<?php if ($region->Estate()->count() > 0 || $region->Fleet()->count() > 0): ?>
									<nav id="nav-region-<?= $region->Id()->Id() ?>" class="nav nav-pills flex-column">
										<?php foreach (View::sortedEstate($region) as $construction /* @var Construction $construction */): ?>
											<a class="construction nav-link ml-3 py-0" href="#construction-<?= $construction->Id()->Id() ?>"><?= $construction->Name() ?></a>
										<?php endforeach ?>
										<?php foreach (View::sortedFleet($region) as $vessel /* @var Vessel $vessel */): ?>
											<a class="vessel nav-link ml-3 py-0" href="#vessel-<?= $vessel->Id()->Id() ?>"><?= $vessel->Name() ?></a>
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
