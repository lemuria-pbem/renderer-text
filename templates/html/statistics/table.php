<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party  = $this->party;
$census = $this->census;
$round  = Lemuria::Calendar()->Round();

$units  = $this->numberStatistics(Subject::Units, $party);
$people = $this->numberStatistics(Subject::People, $party);

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
			<?php foreach ($census->getAtlas() as $region /* @var Region $region */): ?>
				<?= $this->template('statistics/region', $region, 1) ?>
			<?php endforeach ?>
		</tbody>
	</table>
</div>

<div class="table-responsive d-none d-md-block d-xl-none">
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
		<tr class="<?= $units->movement ?>">
			<th scope="row">Anzahl Einheiten</th>
			<td><?= $units->value ?></td>
			<td class="more-is-good"><?= $units->change ?></td>
			<th scope="row">Anzahl Personen</th>
			<td><?= $people->value ?></td>
			<td class="more-is-good"><?= $people->change ?></td>
		</tr>
		<?php foreach ($census->getAtlas() as $region /* @var Region $region */): ?>
			<?= $this->template('statistics/region', $region, 2) ?>
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
		</tr>
		</thead>
		<tbody>
		<tr class="<?= $units->movement ?>">
			<th scope="row">Anzahl Einheiten</th>
			<td><?= $units->value ?></td>
			<td class="more-is-good"><?= $units->change ?></td>
			<th scope="row">Anzahl Personen</th>
			<td><?= $people->value ?></td>
			<td class="more-is-good" colspan="4"><?= $people->change ?></td>
		</tr>
		<?php foreach ($census->getAtlas() as $region /* @var Region $region */): ?>
			<?= $this->template('statistics/region', $region, 3) ?>
		<?php endforeach ?>
		</tbody>
	</table>
</div>
