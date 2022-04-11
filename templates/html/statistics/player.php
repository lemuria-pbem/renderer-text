<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\linkEmail;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party  = $this->party;
$census = $this->census;
$banner = $party->Banner() ? 'Unser Banner: ' . linkEmail($party->Banner()) : '(kein Banner gesetzt)';
$round  = Lemuria::Calendar()->Round();

$units  = $this->numberStatistics(Subject::Units, $party);
$people = $this->numberStatistics(Subject::People, $party);

?>
<h2><?= $party->Name() ?> <span class="badge badge-primary"><?= $party->Id() ?></span></h2>

<blockquote class="blockquote"><?= $party->Description() ?></blockquote>

<p><?= $banner ?></p>

<p>
	Dein Volk zählt <?= $this->number($census->count(), 'race', $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.<br>
	Deine Einheiten sammeln <?= $this->loot() ?>.<br>
	Vorgaben für neue Einheiten: <?= implode(', ', $this->presettings()) ?>.
</p>

<h3>Statistik</h3>

<div class="table-responsive">
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
				<?= $this->template('statistics/region', $region) ?>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
