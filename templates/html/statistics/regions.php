<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Renderer\Text\Model\World\SortedAtlas;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party = $this->party;
$atlas = new SortedAtlas($this->census);
$round = Lemuria::Calendar()->Round();

$units     = $this->numberStatistics(Subject::Units, $party);
$people    = $this->numberStatistics(Subject::People, $party);
$education = $this->numberStatistics(Subject::Education, $party);
$expenses  = $this->numberStatistics(Subject::Expenses, $party);
$pool      = $this->materialPoolStatistics(Subject::MaterialPool, $party);
$pCount    = count($pool);
$experts   = $this->expertsStatistics(Subject::Experts, $party);

?>
<div id="region-statistics-responsive">
	<div id="toggle-region-statistics-sm" class="accordion accordion-flush table-responsive d-md-none">
		<h4 class="accordion-header" title="Taste: R">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#region-statistics-sm" aria-expanded="false" aria-controls="region-statistics-sm">Regionsübersicht</button>
		</h4>
		<div id="region-statistics-sm" class="accordion-collapse collapse" data-bs-parent="#toggle-region-statistics-sm">
			<table class="statistics table table-sm table-bordered">
				<tbody>
					<?php $r = 0; foreach ($atlas as $region): ?>
						<?php if (!($region->Landscape() instanceof Navigable)): ?>
							<?= $this->template('statistics/region', $region, 1, $r++) ?>
						<?php endif ?>
					<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</div>

	<div id="toggle-region-statistics-md" class="accordion accordion-flush table-responsive d-none d-md-block d-lg-none">
		<h4 class="accordion-header" title="Taste: R">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#region-statistics-md" aria-expanded="false" aria-controls="region-statistics-md">Regionsübersicht</button>
		</h4>
		<div id="region-statistics-md" class="accordion-collapse collapse"  data-bs-parent="#toggle-region-statistics-md">
			<table class="statistics table table-sm table-bordered">
				<tbody>
				<?php $r = 0; foreach ($atlas as $region): ?>
					<?php if (!($region->Landscape() instanceof Navigable)): ?>
						<?= $this->template('statistics/region', $region, 2, $r++) ?>
					<?php endif ?>
				<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</div>

	<div id="toggle-region-statistics-lg" class="accordion accordion-flush table-responsive d-none d-lg-block d-xl-none">
		<h4 class="accordion-header" title="Taste: R">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#region-statistics-lg" aria-expanded="false" aria-controls="region-statistics-lg">Regionsübersicht</button>
		</h4>
		<div id="region-statistics-lg" class="accordion-collapse collapse"  data-bs-parent="#toggle-region-statistics-lg">
			<table class="statistics table table-sm table-bordered">
				<tbody>
				<?php $r = 0; foreach ($atlas as $region): ?>
					<?php if (!($region->Landscape() instanceof Navigable)): ?>
						<?= $this->template('statistics/region', $region, 3, $r++) ?>
					<?php endif ?>
				<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</div>

	<div id="toggle-region-statistics-xl" class="accordion accordion-flush table-responsive d-none d-xl-block">
		<h4 class="accordion-header" title="Taste: R">
			<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#region-statistics-xl" aria-expanded="false" aria-controls="region-statistics-xl">Regionsübersicht</button>
		</h4>
		<div id="region-statistics-xl" class="accordion-collapse collapse"  data-bs-parent="#toggle-region-statistics-xl">
			<table class="statistics table table-sm table-bordered">
				<tbody>
				<?php $r = 0; foreach ($atlas as $region): ?>
					<?php if (!($region->Landscape() instanceof Navigable)): ?>
						<?= $this->template('statistics/region', $region, 4, $r++) ?>
					<?php endif ?>
				<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
