<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party  = $this->party;
$census = $this->census;

?>
<div class="fixed-top vh-100">
	<a id="navbar-toggle" class="btn btn-small btn-light fixed-top" data-toggle="collapse" href="#navbar" role="button" aria-expanded="true" aria-controls="navbar" title="Taste: I">Inhalt</a>
	<nav id="navbar" class="navbar navbar-light bg-light collapse">
		<nav class="nav nav-pills flex-column">
			<a class="navbar-brand" href="#"><?= $party->Name() ?></a>
			<?php foreach ($census->getAtlas() as $region /* @var Region $region */): ?>
				<?php if ($region->Landscape() instanceof Ocean): ?>
					<?php foreach ($region->Fleet() as $vessel): ?>
						<a class="nav-link pb-0" href="#vessel-<?= $vessel->Id()->Id() ?>"><?= $vessel->Name() ?></a>
					<?php endforeach ?>
				<?php else: ?>
					<a class="nav-link" href="#region-<?= $region->Id()->Id() ?>"><?= $region->Name() ?></a>
					<?php if ($region->Estate()->count() > 0 || $region->Fleet()->count() > 0): ?>
						<nav id="nav-region-<?= $region->Id()->Id() ?>" class="nav nav-pills flex-column">
							<?php foreach ($region->Estate() as $construction): ?>
								<a class="nav-link ml-3 py-0" href="#construction-<?= $construction->Id()->Id() ?>"><?= $construction->Name() ?></a>
							<?php endforeach ?>
							<?php foreach ($region->Fleet() as $vessel): ?>
								<a class="nav-link ml-3 py-0" href="#vessel-<?= $vessel->Id()->Id() ?>"><?= $vessel->Name() ?></a>
							<?php endforeach ?>
						</nav>
					<?php endif ?>
				<?php endif ?>
			<?php endforeach ?>
		</nav>
	</nav>
</div>
