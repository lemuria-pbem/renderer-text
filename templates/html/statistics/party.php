<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Renderer\Text\Model\World\SortedAtlas;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party = $this->party;
$atlas = new SortedAtlas($this->census);
$round = Lemuria::Calendar()->Round();

$units     = $this->numberStatistics(Subject::Units, $party);
$people    = $this->numberStatistics(Subject::People, $party);
$races     = $this->raceStatistics($party);
$education = $this->numberStatistics(Subject::Education, $party);
$expenses  = $this->numberStatistics(Subject::Expenses, $party);
$pool      = $this->materialPoolStatistics(Subject::MaterialPool, $party);
$pCount    = count($pool);
$experts   = $this->expertsStatistics(Subject::Experts, $party);

?>
<p>Wähle ein Talent, um eine vollständige Übersicht zu erhalten.</p>

<div class="table-responsive d-md-none">
	<table class="statistics table table-sm table-bordered">
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
			<?php foreach ($races as $numbers): ?>
				<?php foreach ($numbers as $what => $race): ?>
					<tr class="<?= $race->movement ?>">
						<th scope="row"><?= $this->translate($race->class) ?>-<?= $what ?></th>
						<td><?= $race->value ?></td>
						<td class="more-is-good"><?= $race->change ?></td>
					</tr>
				<?php endforeach ?>
			<?php endforeach ?>
			<tr class="table-light">
				<td colspan="3">
					<?= $this->template('statistics/experts', $experts, 4) ?>
				</td>
			</tr>
			<?php if ($pCount > 0): ?>
				<tr class="table-light">
					<td colspan="3">
						<?= $this->template('statistics/material-pool', $pool, 4) ?>
					</td>
				</tr>
			<?php endif ?>
		</tbody>
	</table>
</div>

<div class="table-responsive d-none d-md-block d-lg-none">
	<table class="statistics table table-sm table-bordered">
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
		<?php foreach ($races as $numbers): ?>
			<tr>
			<?php foreach ($numbers as $what => $race): ?>
				<th scope="row"><?= $this->translate($race->class) ?>-<?= $what ?></th>
				<td><?= $race->value ?></td>
				<td class="more-is-good"<?= count($numbers) === 1 ? ' colspan="4"' : '' ?>><?= $race->change ?></td>
			<?php endforeach ?>
			</tr>
		<?php endforeach ?>
		<tr class="table-light">
			<td colspan="6">
				<?= $this->template('statistics/experts', $experts, 6) ?>
			</td>
		</tr>
		<?php if ($pCount > 0): ?>
			<tr class="table-light">
				<td colspan="6">
					<?= $this->template('statistics/material-pool', $pool, 6) ?>
				</td>
			</tr>
		<?php endif ?>
		</tbody>
	</table>
</div>

<div class="table-responsive d-none d-lg-block d-xl-none">
	<table class="statistics table table-sm table-bordered">
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
		<?php foreach ($races as $numbers): ?>
			<tr>
				<?php foreach ($numbers as $what => $race): ?>
					<th scope="row"><?= $this->translate($race->class) ?>-<?= $what ?></th>
					<td><?= $race->value ?></td>
					<td class="more-is-good" <?= count($numbers) === 1 ? 'colspan="7"' : '' ?><?= count($numbers) > 1 && $what === 'Personen' ? 'colspan="4"' : '' ?>><?= $race->change ?></td>
				<?php endforeach ?>
			</tr>
		<?php endforeach ?>
		<tr class="table-light">
			<td colspan="9">
				<?= $this->template('statistics/experts', $experts, 8) ?>
			</td>
		</tr>
		<?php if ($pCount > 0): ?>
			<tr class="table-light">
				<td colspan="9">
					<?= $this->template('statistics/material-pool', $pool, 8) ?>
				</td>
			</tr>
		<?php endif ?>
		</tbody>
	</table>
</div>

<div class="table-responsive d-none d-xl-block">
	<table class="statistics table table-sm table-bordered">
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
		<?php foreach ($races as $numbers): ?>
			<tr>
				<?php foreach ($numbers as $what => $race): ?>
					<th scope="row"><?= $this->translate($race->class) ?>-<?= $what ?></th>
					<td><?= $race->value ?></td>
					<td class="more-is-good" <?= count($numbers) === 1 ? 'colspan="11"' : '' ?><?= count($numbers) > 1 && $what === 'Personen' ? 'colspan="7"' : '' ?>><?= $race->change ?></td>
				<?php endforeach ?>
			</tr>
		<?php endforeach ?>
		<tr class="table-light">
			<td colspan="12">
				<?= $this->template('statistics/experts', $experts, 10) ?>
			</td>
		</tr>
		<?php if ($pCount > 0): ?>
			<tr class="table-light">
				<td colspan="12">
					<?= $this->template('statistics/material-pool', $pool, 10) ?>
				</td>
			</tr>
		<?php endif ?>
		</tbody>
	</table>
</div>
