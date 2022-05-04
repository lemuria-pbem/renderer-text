<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party  = $this->party;
$census = $this->census;
$round  = Lemuria::Calendar()->Round();

$units     = $this->numberStatistics(Subject::Units, $party);
$people    = $this->numberStatistics(Subject::People, $party);
$education = $this->numberStatistics(Subject::Education, $party);
$expenses  = $this->numberStatistics(Subject::Expenses, $party);
$pool      = $this->materialPoolStatistics(Subject::MaterialPool, $party);
$pCount    = count($pool);
$experts   = $this->expertsStatistics(Subject::Experts, $party);

?>
<div class="table-responsive d-md-none">
	<table class="statistics table table-sm table-striped table-bordered">
		<thead class="table-light">
			<tr>
				<th scope="col">Deine Partei</th>
				<th scope="col">Runde <?= $round ?></th>
				<th scope="col">Veränderung</th>
			</tr>
		</thead>
		<tbody>
			<tr class="<?= $units->movement ?>">
				<th scope="row">Anzahl Einheiten</th>
				<td><?= $units->value ?></td>
				<td class="more-is-good"><?= $units->change ?></td>
			</tr>
			<tr class="<?= $people->movement ?>">
				<th scope="row">Anzahl Personen</th>
				<td><?= $people->value ?></td>
				<td class="more-is-good"><?= $people->change ?></td>
			</tr>
			<tr class="<?= $education->movement ?>">
				<th scope="row">Gesamte Erfahrungspunkte</th>
				<td><?= $education->value ?></td>
				<td class="more-is-good"><?= $education->change ?></td>
			</tr>
			<tr class="<?= $expenses->movement ?>">
				<th scope="row">Gesamte Ausgaben</th>
				<td><?= $expenses->value ?></td>
				<td class="less-is-good"><?= $expenses->change ?></td>
			</tr>
			<tr>
				<td colspan="3">
					<?= $this->template('statistics/experts', $experts, 4) ?>
				</td>
			</tr>
			<?php if ($pCount > 0): ?>
				<tr>
					<td colspan="3">
						<?= $this->template('statistics/material-pool', $pool, 4) ?>
					</td>
				</tr>
			<?php endif ?>
			<?php foreach ($census->getAtlas() as $region /* @var Region $region */): ?>
				<?php if (!($region->Landscape() instanceof Ocean)): ?>
					<?= $this->template('statistics/region', $region, 1) ?>
				<?php endif ?>
			<?php endforeach ?>
		</tbody>
	</table>
</div>

<div class="table-responsive d-none d-md-block d-lg-none">
	<table class="statistics table table-sm table-striped table-bordered">
		<thead class="table-light">
		<tr>
			<th scope="col">Deine Partei</th>
			<th scope="col">Runde <?= $round ?></th>
			<th scope="col">Veränderung</th>
			<th scope="col"></th>
			<th scope="col">Runde <?= $round ?></th>
			<th scope="col">Veränderung</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<th scope="row">Anzahl Einheiten</th>
			<td><?= $units->value ?></td>
			<td class="<?= $units->movement ?> more-is-good"><?= $units->change ?></td>
			<th scope="row">Anzahl Personen</th>
			<td><?= $people->value ?></td>
			<td class="<?= $people->movement ?> more-is-good"><?= $people->change ?></td>
		</tr>
		<tr>
			<th scope="row">Gesamte Erfahrungspunkte</th>
			<td><?= $education->value ?></td>
			<td class="<?= $education->movement ?> more-is-good"><?= $education->change ?></td>
			<th scope="row">Gesamte Ausgaben</th>
			<td><?= $expenses->value ?></td>
			<td class="<?= $expenses->movement ?> less-is-good"><?= $expenses->change ?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?= $this->template('statistics/experts', $experts, 6) ?>
			</td>
		</tr>
		<?php if ($pCount > 0): ?>
			<tr>
				<td colspan="6">
					<?= $this->template('statistics/material-pool', $pool, 6) ?>
				</td>
			</tr>
		<?php endif ?>
		<?php foreach ($census->getAtlas() as $region /* @var Region $region */): ?>
			<?php if (!($region->Landscape() instanceof Ocean)): ?>
				<?= $this->template('statistics/region', $region, 2) ?>
			<?php endif ?>
		<?php endforeach ?>
		</tbody>
	</table>
</div>

<div class="table-responsive d-none d-lg-block d-xl-none">
	<table class="statistics table table-sm table-striped table-bordered">
		<thead class="table-light">
		<tr>
			<th scope="col">Deine Partei</th>
			<th scope="col">Runde <?= $round ?></th>
			<th scope="col">Veränderung</th>
			<th scope="col"></th>
			<th scope="col">Runde <?= $round ?></th>
			<th scope="col">Veränderung</th>
			<th scope="col"></th>
			<th scope="col">Runde <?= $round ?></th>
			<th scope="col">Veränderung</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<th scope="row">Anzahl Einheiten</th>
			<td><?= $units->value ?></td>
			<td class="<?= $units->movement ?> more-is-good"><?= $units->change ?></td>
			<th scope="row">Anzahl Personen</th>
			<td><?= $people->value ?></td>
			<td class="<?= $units->movement ?> more-is-good"><?= $people->change ?></td>
			<th scope="row">Gesamte Erfahrungspunkte</th>
			<td><?= $education->value ?></td>
			<td class="<?= $education->movement ?> more-is-good"><?= $education->change ?></td>
		</tr>
		<tr>
			<th scope="row">Gesamte Ausgaben</th>
			<td><?= $expenses->value ?></td>
			<td class="<?= $expenses->movement ?> less-is-good" colspan="7"><?= $expenses->change ?></td>
		</tr>
		<tr>
			<td colspan="9">
				<?= $this->template('statistics/experts', $experts, 8) ?>
			</td>
		</tr>
		<?php if ($pCount > 0): ?>
			<tr>
				<td colspan="9">
					<?= $this->template('statistics/material-pool', $pool, 8) ?>
				</td>
			</tr>
		<?php endif ?>
		<?php foreach ($census->getAtlas() as $region /* @var Region $region */): ?>
			<?php if (!($region->Landscape() instanceof Ocean)): ?>
				<?= $this->template('statistics/region', $region, 3) ?>
			<?php endif ?>
		<?php endforeach ?>
		</tbody>
	</table>
</div>

<div class="table-responsive d-none d-xl-block">
	<table class="statistics table table-sm table-striped table-bordered">
		<thead class="table-light">
		<tr>
			<th scope="col">Deine Partei</th>
			<th scope="col">Runde <?= $round ?></th>
			<th scope="col">Veränderung</th>
			<th scope="col"></th>
			<th scope="col">Runde <?= $round ?></th>
			<th scope="col">Veränderung</th>
			<th scope="col"></th>
			<th scope="col">Runde <?= $round ?></th>
			<th scope="col">Veränderung</th>
			<th scope="col"></th>
			<th scope="col">Runde <?= $round ?></th>
			<th scope="col">Veränderung</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<th scope="row">Anzahl Einheiten</th>
			<td><?= $units->value ?></td>
			<td class="<?= $units->movement ?> more-is-good"><?= $units->change ?></td>
			<th scope="row">Anzahl Personen</th>
			<td><?= $people->value ?></td>
			<td class="<?= $units->movement ?> more-is-good"><?= $people->change ?></td>
			<th scope="row">Gesamte Erfahrungspunkte</th>
			<td><?= $education->value ?></td>
			<td class="<?= $education->movement ?> more-is-good"><?= $education->change ?></td>
			<th scope="row">Gesamte Ausgaben</th>
			<td><?= $expenses->value ?></td>
			<td class="<?= $expenses->movement ?> less-is-good"><?= $expenses->change ?></td>
		</tr>
		<tr>
			<td colspan="12">
				<?= $this->template('statistics/experts', $experts, 10) ?>
			</td>
		</tr>
		<?php if ($pCount > 0): ?>
			<tr>
				<td colspan="12">
					<?= $this->template('statistics/material-pool', $pool, 10) ?>
				</td>
			</tr>
		<?php endif ?>
		<?php foreach ($census->getAtlas() as $region /* @var Region $region */): ?>
			<?php if (!($region->Landscape() instanceof Ocean)): ?>
				<?= $this->template('statistics/region', $region, 4) ?>
			<?php endif ?>
		<?php endforeach ?>
		</tbody>
	</table>
</div>
