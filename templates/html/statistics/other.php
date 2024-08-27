<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party = $this->party;
$round = Lemuria::Calendar()->Round();

$units  = $this->numberStatistics(Subject::Units, $party);
$people = $this->numberStatistics(Subject::People, $party);
$races  = $this->raceStatistics($party);

?>
<div class="table-responsive d-md-none">
	<table class="statistics table table-sm table-bordered">
		<thead class="table-light">
			<tr>
				<th scope="col">Partei <?= $party->Name() ?></th>
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
			<?php foreach ($races as $numbers): ?>
				<?php foreach ($numbers as $what => $race): ?>
					<tr class="<?= $race->movement ?>">
						<th scope="row"><?= $this->translate($race->class) ?>-<?= $what ?></th>
						<td><?= $race->value ?></td>
						<td class="more-is-good"><?= $race->change ?></td>
					</tr>
				<?php endforeach ?>
			<?php endforeach ?>
		</tbody>
	</table>
</div>

<div class="table-responsive d-none d-md-block d-lg-none">
	<table class="statistics table table-sm table-bordered">
		<thead class="table-light">
		<tr>
			<th scope="col">Partei <?= $party->Name() ?></th>
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
		<?php foreach ($races as $numbers): ?>
			<tr>
			<?php foreach ($numbers as $what => $race): ?>
				<th scope="row"><?= $this->translate($race->class) ?>-<?= $what ?></th>
				<td><?= $race->value ?></td>
				<td class="<?= $race->movement ?> more-is-good"<?= count($numbers) === 1 ? ' colspan="4"' : '' ?>><?= $race->change ?></td>
			<?php endforeach ?>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
</div>

<div class="table-responsive d-none d-lg-block d-xl-none">
	<table class="statistics table table-sm table-bordered">
		<thead class="table-light">
		<tr>
			<th scope="col">Partei <?= $party->Name() ?></th>
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
			<td class="<?= $people->movement ?> more-is-good" colspan="4"><?= $people->change ?></td>
		</tr>
		<?php foreach ($races as $numbers): ?>
			<tr>
				<?php foreach ($numbers as $what => $race): ?>
					<th scope="row"><?= $this->translate($race->class) ?>-<?= $what ?></th>
					<td><?= $race->value ?></td>
					<td class="<?= $race->movement ?> more-is-good" <?= count($numbers) === 1 ? 'colspan="7"' : '' ?><?= count($numbers) > 1 && $what === 'Personen' ? 'colspan="4"' : '' ?>><?= $race->change ?></td>
				<?php endforeach ?>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
</div>

<div class="table-responsive d-none d-xl-block">
	<table class="statistics table table-sm table-bordered">
		<thead class="table-light">
		<tr>
			<th scope="col">Partei <?= $party->Name() ?></th>
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
			<td class="<?= $people->movement ?> more-is-good" colspan="7"><?= $people->change ?></td>
		</tr>
		<?php foreach ($races as $numbers): ?>
			<tr>
				<?php foreach ($numbers as $what => $race): ?>
					<th scope="row"><?= $this->translate($race->class) ?>-<?= $what ?></th>
					<td><?= $race->value ?></td>
					<td class="<?= $race->movement ?> more-is-good" <?= count($numbers) === 1 ? 'colspan="11"' : '' ?><?= count($numbers) > 1 && $what === 'Personen' ? 'colspan="7"' : '' ?>><?= $race->change ?></td>
				<?php endforeach ?>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>
</div>
