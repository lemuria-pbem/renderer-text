<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party     = $this->party;
$travelLog = $this->travelLog;

?>
<div class="fixed-top vh-100">
	<a id="navbar-toggle" class="btn btn-small btn-light fixed-top" data-toggle="collapse" href="#navbar" role="button" aria-expanded="true" aria-controls="navbar" title="Taste: I">Inhalt</a>
	<nav id="navbar" class="navbar navbar-light bg-light collapse">
		<nav class="nav nav-pills flex-column">
			<?php foreach ($travelLog as $continent => $atlas): ?>
				<?php if ($atlas->count() > 0): ?>
					<a class="navbar-brand" href="#continent-<?= $continent->Id() ?>"><?= $continent->Name() ?></a>
					<?php foreach ($atlas as $region /* @var Region $region */): ?>
						<?php if ($region->Landscape() instanceof Ocean): ?>
							<?php foreach (View::sortedFleet($region) as $vessel): ?>
								<a class="vessel nav-link pb-0" href="#vessel-<?= $vessel->Id()->Id() ?>"><?= $vessel->Name() ?></a>
							<?php endforeach ?>
						<?php else: ?>
							<a class="region nav-link" href="#region-<?= $region->Id()->Id() ?>"><?= $region->Name() ?></a>
							<?php if ($region->Estate()->count() > 0 || $region->Fleet()->count() > 0): ?>
								<nav id="nav-region-<?= $region->Id()->Id() ?>" class="nav nav-pills flex-column">
									<?php foreach (View::sortedEstate($region) as $construction): ?>
										<a class="construction nav-link ml-3 py-0" href="#construction-<?= $construction->Id()->Id() ?>"><?= $construction->Name() ?></a>
									<?php endforeach ?>
									<?php foreach (View::sortedFleet($region) as $vessel): ?>
										<a class="vessel nav-link ml-3 py-0" href="#vessel-<?= $vessel->Id()->Id() ?>"><?= $vessel->Name() ?></a>
									<?php endforeach ?>
								</nav>
							<?php endif ?>
						<?php endif ?>
					<?php endforeach ?>
				<?php endif ?>
			<?php endforeach ?>
		</nav>
	</nav>
</div>
