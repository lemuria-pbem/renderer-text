<?php /** @noinspection PhpPossiblePolymorphicInvocationInspection */
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region = $this->variables[0];
$cols   = max(1, min(4, $this->variables[1]));
$prefix = match ($cols) {
	2       => 'md-stat-',
	3       => 'lg-stat-',
	4       => 'xl-stat-',
	default => 'stat-'
};
$realmId   = $region->Realm()?->Identifier();
$territory = $region->Realm()?->Territory();
$realm     = $territory ? ($territory->Central() === $region ? ' realm center' : ' realm') : '';
$class     = $prefix . id($region);

$population  = $this->numberStatistics(Subject::Population, $region);
$workers     = $this->numberStatistics(Subject::Workers, $region);
$workplaces  = $this->numberStatistics(Subject::Workplaces, $region);
$recruits    = $this->numberStatistics(Subject::Unemployment, $region);
$births      = $this->numberStatistics(Subject::Births, $region);
$migration   = $this->numberStatistics(Subject::Migration, $region);
$wealth      = $this->numberStatistics(Subject::Wealth, $region);
$income      = $this->numberStatistics(Subject::Income, $region);
$joblessness = $this->numberStatistics(Subject::Joblessness, $region);
$prosperity  = $this->numberStatistics(Subject::Prosperity, $region);
$expenses    = $this->multipleStatistics([
	'Ausgaben für Unterhalt'    => Subject::Support,
	'Ausgaben für Gebäude'      => Subject::Maintenance,
	'Ausgaben für Rekrutierung' => Subject::Recruiting,
	'Lernkosten'                => Subject::LearningCosts,
	'Handelseinkäufe'           => Subject::Purchase,
	'Almosen an Fremdeinheiten' => Subject::Charity
], $region);
$trees       = $this->numberStatistics(Subject::Trees, $region);
$animals     = $this->animalStatistics(Subject::Animals, $region);
$luxuries    = $this->marketStatistics(Subject::Market, $region);

$representative = $this->census->getPeople($region)->getFirst();
$unitForce      = $this->numberStatisticsOrNull(Subject::UnitForce, $representative);
$peopleForce    = $this->numberStatisticsOrNull(Subject::PeopleForce, $representative);
$reserve        = $this->regionSilverStatistics($representative);

if ($cols <= 1) {
	$ids = $class . '-population ' . $class . '-workers ' . $class . '-recruits ' . $class . '-births ' .
		   $class . '-migration ' . $class . '-wealth ' . $class . '-income ' . $class . '-trees';
} elseif ($cols === 2) {
	$ids = $class . '-joblessness ' . $class . '-population ' . $class . '-peasants ' . $class . '-wealth ' .
		   $class . '-prosperity';
} elseif ($cols === 3) {
	$ids = $class . '-joblessness ' . $class . '-population ' . $class . '-peasants';
} else {
	$ids = $class . '-joblessness ' . $class . '-population ' . $class;
}
foreach ($animals as $i => $animal) {
	if ($i % $cols === 0) {
		$ids .= ' ' . $class . '-' . $animal->key;
	}
}
if ($cols <= 1) {
	$ids .= ' ' . $class . '-unit-force ' . $class . '-people-force ' . $class . '-reserve';
} elseif ($cols === 2) {
	$ids .= ' ' . $class . '-unit-force ' . $class . '-reserve';
} else {
	$ids .= ' ' . $class . '-unit-force';
}
$i = 3;
foreach ($expenses as $name => $expense) {
	if ($i++ % $cols === 0) {
		$ids .= ' ' . $class . '-' . $expense->class;
	}
}
if (!empty($luxuries)) {
	$ids .= ' ' . $class . '-market';
}

?>
<?php if ($cols <= 1): ?>
	<tr class="region<?= $realm ?>">
		<th scope="rowgroup" colspan="3">
			<a href=".<?= $class ?>" title="Details..." data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="<?= $ids ?>">
				<?php if ($realmId): ?>
					<span class="badge text-bg-light font-monospace"><?= $realmId ?></span>
				<?php endif ?>
				<span><?= $this->translate($region->Landscape()) ?> <?= $region->Name() ?></span>
			</a>
			&nbsp;<a href="#<?= id($region) ?>" title="zur Region" class="text-body">⮞</a>
		</th>
	</tr>
	<tr id="<?= $class ?>-population" class="collapse <?= $population->movement ?> <?= $class ?>">
		<th scope="row">Bevölkerung</th>
		<td><?= $population->value ?></td>
		<td class="more-is-good"><?= $population->change ?></td>
	</tr>
	<tr id="<?= $class ?>-workplaces" class="collapse <?= $workplaces->movement ?> <?= $class ?>">
		<th scope="row">Arbeitsplätze</th>
		<td><?= $workplaces->value ?></td>
		<td class="more-is-good"><?= $workplaces->change ?></td>
	</tr>
	<tr id="<?= $class ?>-joblessness" class="collapse <?= $joblessness->movement ?> <?= $class ?>">
		<th scope="row">Arbeitslosigkeit</th>
		<td><?= $joblessness->value ?> %</td>
		<td class="less-is-good"><?= $joblessness->change ?></td>
	</tr>
	<tr id="<?= $class ?>-workers" class="collapse <?= $workers->movement ?> <?= $class ?>">
		<th scope="row">Arbeiter</th>
		<td><?= $workers->value ?></td>
		<td class="more-is-good"><?= $workers->change ?></td>
	</tr>
	<tr id="<?= $class ?>-recruits" class="collapse <?= $recruits->movement ?> <?= $class ?>">
		<th scope="row">Rekruten</th>
		<td><?= $recruits->value ?></td>
		<td class="more-is-good"><?= $recruits->change ?></td>
	</tr>
	<tr id="<?= $class ?>-births" class="collapse <?= $births->movement ?> <?= $class ?>">
		<th scope="row">Geburten</th>
		<td><?= $births->value ?></td>
		<td class="more-is-good"><?= $births->change ?></td>
	</tr>
	<tr id="<?= $class ?>-migration" class="collapse <?= $migration->movement ?> <?= $class ?>">
		<th scope="row">Bauernwanderung</th>
		<td><?= $migration->value ?></td>
		<td class="more-is-good"><?= $migration->change ?></td>
	</tr>
	<tr id="<?= $class ?>-prosperity" class="collapse <?= $prosperity->movement ?> <?= $class ?>">
		<th scope="row">Wohlstand</th>
		<td><?= $prosperity->value ?> a</td>
		<td class="more-is-good"><?= $prosperity->change ?></td>
	</tr>
	<tr id="<?= $class ?>-wealth" class="collapse <?= $wealth->movement ?> <?= $class ?>">
		<th scope="row">Silbervorrat</th>
		<td><?= $wealth->value ?></td>
		<td class="more-is-good"><?= $wealth->change ?></td>
	</tr>
	<tr id="<?= $class ?>-income" class="collapse <?= $income->movement ?> <?= $class ?>">
		<th scope="row">Einkommen</th>
		<td><?= $income->value ?></td>
		<td class="more-is-good"><?= $income->change ?></td>
	</tr>
	<tr id="<?= $class ?>-trees" class="collapse <?= $trees->movement ?> <?= $class ?>">
		<th scope="row">Baumbestand</th>
		<td><?= $trees->value ?></td>
		<td class="more-is-good"><?= $trees->change ?></td>
	</tr>
	<?php foreach ($animals as $animal): ?>
		<tr id="<?= $class . '-' . $animal->key ?>" class="collapse <?= $animal->movement ?> <?= $class ?>">
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<td class="more-is-good"><?= $animal->change ?></td>
		</tr>
	<?php endforeach ?>
	<tr id="<?= $class ?>-unit-force" class="collapse <?= $unitForce->movement ?> <?= $class ?>">
		<th scope="row">Einheiten</th>
		<td><?= $unitForce->value ?></td>
		<td class="more-is-good"><?= $unitForce->change ?></td>
	</tr>
	<tr id="<?= $class ?>-people-force" class="collapse <?= $peopleForce->movement ?> <?= $class ?>">
		<th scope="row">Personen</th>
		<td><?= $peopleForce->value ?></td>
		<td class="more-is-good"><?= $peopleForce->change ?></td>
	</tr>
	<tr id="<?= $class ?>-reserve" class="collapse <?= $reserve->movement ?> <?= $class ?>">
		<th scope="row">Silberreserve</th>
		<td><?= $reserve->value ?></td>
		<td class="more-is-good"><?= $reserve->change ?></td>
	</tr>
	<?php foreach ($expenses as $name => $expense): ?>
		<tr id="<?= $class . '-' . $expense->class ?>" class="collapse <?= $expense->movement ?> <?= $class ?>">
			<th scope="row"><?= $name ?></th>
			<td><?= $expense->value ?></td>
			<td class="less-is-good"><?= $expense->change ?></td>
		</tr>
	<?php endforeach ?>
	<?php if (!empty($luxuries)): ?>
		<tr id="<?= $class . '-market' ?>" class="collapse <?= $class ?>">
			<td colspan="3">
				<table class="market table">
					<caption>Marktpreise</caption>
					<tr>
						<?php foreach ($luxuries as $luxury): ?>
							<th scope="col" colspan="2" class="<?= $luxury->offerDemand ?>"><?= $this->translate($luxury->class) ?></th>
						<?php endforeach ?>
					</tr>
					<tr class="td-16">
						<?php foreach ($luxuries as $luxury): ?>
							<td class="<?= $luxury->offerDemand ?>"><?= $luxury->value ?></td>
							<td class="<?= $luxury->movement ?> <?= $luxury->moreOrLess ?> <?= $luxury->offerDemand ?>"><?= $luxury->change ?></td>
						<?php endforeach ?>
					</tr>
				</table>
			</td>
		</tr>
	<?php endif ?>

<?php elseif ($cols === 2): ?>
	<tr class="region<?= $realm ?>">
		<th scope="rowgroup" colspan="3">
			<a href=".<?= $class ?>" title="Details..." data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="<?= $ids ?>">
				<?php if ($realmId): ?>
					<span class="badge text-bg-light font-monospace"><?= $realmId ?></span>
				<?php endif ?>
				<span><?= $this->translate($region->Landscape()) ?> <?= $region->Name() ?></span>
			</a>
			&nbsp;<a href="#<?= id($region) ?>" title="zur Region" class="text-body">⮞</a>
		</th>
		<th scope="row">Bevölkerung</th>
		<td><?= $population->value ?></td>
		<td class="<?= $population->movement ?> more-is-good"><?= $population->change ?></td>
	</tr>
	<tr id="<?= $class?>-joblessness" class="collapse <?= $class ?>">
		<th scope="row">Arbeitslosigkeit</th>
		<td><?= $joblessness->value ?> %</td>
		<td class="<?= $joblessness->movement ?> less-is-good"><?= $joblessness->change ?></td>
		<th scope="row">Arbeitsplätze</th>
		<td><?= $workplaces->value ?></td>
		<td class="<?= $workplaces->movement ?> more-is-good"><?= $workplaces->change ?></td>
	</tr>
	<tr id="<?= $class ?>-population" class="collapse <?= $class ?>">
		<th scope="row">Rekruten</th>
		<td><?= $recruits->value ?></td>
		<td class="<?= $recruits->movement ?> more-is-good"><?= $recruits->change ?></td>
		<th scope="row">Arbeiter</th>
		<td><?= $workers->value ?></td>
		<td class="<?= $workers->movement ?> more-is-good"><?= $workers->change ?></td>
	</tr>
	<tr id="<?= $class ?>-peasants" class="collapse <?= $class ?>">
		<th scope="row">Geburten</th>
		<td><?= $births->value ?></td>
		<td class="<?= $births->movement ?> more-is-good"><?= $births->change ?></td>
		<th scope="row">Bauernwanderung</th>
		<td><?= $migration->value ?></td>
		<td class="<?= $migration->movement ?> more-is-good"><?= $migration->change ?></td>
	</tr>
	<tr id="<?= $class ?>-wealth" class="collapse <?= $class ?>">
		<th scope="row">Silbervorrat</th>
		<td><?= $wealth->value ?></td>
		<td class="<?= $wealth->movement ?> more-is-good"><?= $wealth->change ?></td>
		<th scope="row">Einkommen</th>
		<td><?= $income->value ?></td>
		<td class="<?= $income->movement ?> more-is-good"><?= $income->change ?></td>
	</tr>
	<tr id="<?= $class ?>-prosperity" class="collapse <?= $class ?>">
		<th scope="row">Wohlstand</th>
		<td><?= $prosperity->value ?> a</td>
		<td class="<?= $prosperity->movement ?> more-is-good"><?= $prosperity->change ?></td>
		<th scope="row">Baumbestand</th>
		<td><?= $trees->value ?></td>
		<td class="<?= $trees->movement ?> more-is-good"><?= $trees->change ?></td>
	</tr>
	<?php foreach ($animals as $i => $animal): ?>
		<?php if ($i % $cols === 0): ?>
			<tr id="<?= $class . '-' . $animal->key ?>" class="collapse <?= $class ?>">
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="4"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php else: ?>
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			</tr>
		<?php endif ?>
	<?php endforeach ?>
	<tr id="<?= $class ?>-unit-force" class="collapse <?= $class ?>">
		<th scope="row">Einheiten</th>
		<td><?= $unitForce->value ?></td>
		<td class="<?= $unitForce->movement ?> more-is-good"><?= $unitForce->change ?></td>
		<th scope="row">Personen</th>
		<td><?= $peopleForce->value ?></td>
		<td class="<?= $peopleForce->movement ?> more-is-good"><?= $peopleForce->change ?></td>
	</tr>
	<tr id="<?= $class ?>-reserve" class="collapse <?= $class ?>">
		<th scope="row">Silberreserve</th>
		<td><?= $reserve->value ?></td>
		<td class="<?= $reserve->movement ?> more-is-good" colspan="4"><?= $reserve->change ?></td>
	</tr>
	<?php $i = 0 ?>
	<?php foreach ($expenses as $name => $expense): ?>
		<?php if ($i % $cols === 0): ?>
			<tr id="<?= $class . '-' . $expense->class ?>" class="collapse <?= $class ?>">
			<th scope="row"><?= $name ?></th>
			<td><?= $expense->value ?></td>
			<?php if ($i === count($expenses) - 1): ?>
				<td class="<?= $expense->movement ?> less-is-good" colspan="4"><?= $expense->change ?></td>
			<?php else: ?>
				<td class="<?= $expense->movement ?> less-is-good"><?= $expense->change ?></td>
			<?php endif ?>
		<?php else: ?>
			<th scope="row"><?= $name ?></th>
			<td><?= $expense->value ?></td>
			<td class="<?= $expense->movement ?> less-is-good"><?= $expense->change ?></td>
			</tr>
		<?php endif ?>
		<?php $i++ ?>
	<?php endforeach ?>
	<?php if (!empty($luxuries)): ?>
		<tr id="<?= $class . '-market' ?>" class="collapse <?= $class ?>">
			<th scope="row">Marktpreise</th>
			<td colspan="5">
				<table class="market table">
					<tr>
						<?php foreach ($luxuries as $luxury): ?>
							<th scope="col" colspan="2" class="<?= $luxury->offerDemand ?>"><?= $this->translate($luxury->class) ?></th>
						<?php endforeach ?>
					</tr>
					<tr class="td-16">
						<?php foreach ($luxuries as $luxury): ?>
							<td class="<?= $luxury->offerDemand ?>"><?= $luxury->value ?></td>
							<td class="<?= $luxury->movement ?> <?= $luxury->moreOrLess ?> <?= $luxury->offerDemand ?>"><?= $luxury->change ?></td>
						<?php endforeach ?>
					</tr>
				</table>
			</td>
		</tr>
	<?php endif ?>

<?php elseif ($cols === 3): ?>
	<tr class="region<?= $realm ?>">
		<th scope="rowgroup" colspan="3">
			<a href=".<?= $class ?>" title="Details..." data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="<?= $ids ?>">
				<?php if ($realmId): ?>
					<span class="badge text-bg-light font-monospace"><?= $realmId ?></span>
				<?php endif ?>
				<span><?= $this->translate($region->Landscape()) ?> <?= $region->Name() ?></span>
			</a>
			&nbsp;<a href="#<?= id($region) ?>" title="zur Region" class="text-body">⮞</a>
		</th>
		<th scope="row">Bevölkerung</th>
		<td><?= $population->value ?></td>
		<td class="<?= $population->movement ?> more-is-good"><?= $population->change ?></td>
		<th scope="row">Silbervorrat</th>
		<td><?= $wealth->value ?></td>
		<td class="<?= $wealth->movement ?> more-is-good"><?= $wealth->change ?></td>
	</tr>
	<tr id="<?= $class?>-joblessness" class="collapse <?= $class ?>">
		<th scope="row">Arbeitslosigkeit</th>
		<td><?= $joblessness->value ?> %</td>
		<td class="<?= $joblessness->movement ?> less-is-good"><?= $joblessness->change ?></td>
		<th scope="row">Arbeitsplätze</th>
		<td><?= $workplaces->value ?></td>
		<td class="<?= $workplaces->movement ?> more-is-good"><?= $workplaces->change ?></td>
		<th scope="row">Wohlstand</th>
		<td><?= $prosperity->value ?> a</td>
		<td class="<?= $prosperity->movement ?> more-is-good"><?= $prosperity->change ?></td>
	</tr>
	<tr id="<?= $class ?>-population" class="collapse <?= $class ?>">
		<th scope="row">Rekruten</th>
		<td><?= $recruits->value ?></td>
		<td class="<?= $recruits->movement ?> more-is-good"><?= $recruits->change ?></td>
		<th scope="row">Arbeiter</th>
		<td><?= $workers->value ?></td>
		<td class="<?= $workers->movement ?> more-is-good"><?= $workers->change ?></td>
		<th scope="row">Einkommen</th>
		<td><?= $income->value ?></td>
		<td class="<?= $income->movement ?> more-is-good"><?= $income->change ?></td>
	</tr>
	<tr id="<?= $class ?>-peasants" class="collapse <?= $class ?>">
		<th scope="row">Geburten</th>
		<td><?= $births->value ?></td>
		<td class="<?= $births->movement ?> more-is-good"><?= $births->change ?></td>
		<th scope="row">Bauernwanderung</th>
		<td><?= $migration->value ?></td>
		<td class="<?= $migration->movement ?> more-is-good"><?= $migration->change ?></td>
		<th scope="row">Baumbestand</th>
		<td><?= $trees->value ?></td>
		<td class="<?= $trees->movement ?> more-is-good"><?= $trees->change ?></td>
	</tr>
	<?php foreach ($animals as $i => $animal): ?>
		<?php if ($i % $cols === 0): ?>
			<tr id="<?= $class . '-' . $animal->key ?>" class="collapse <?= $class ?>">
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="7"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php elseif ($i % $cols === 1): ?>
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="4"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php else: ?>
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			</tr>
		<?php endif ?>
	<?php endforeach ?>
	<tr id="<?= $class ?>-unit-force" class="collapse <?= $class ?>">
		<th scope="row">Einheiten</th>
		<td><?= $unitForce->value ?></td>
		<td class="<?= $unitForce->movement ?> more-is-good"><?= $unitForce->change ?></td>
		<th scope="row">Personen</th>
		<td><?= $peopleForce->value ?></td>
		<td class="<?= $peopleForce->movement ?> more-is-good"><?= $peopleForce->change ?></td>
		<th scope="row">Silberreserve</th>
		<td><?= $reserve->value ?></td>
		<td class="<?= $reserve->movement ?> more-is-good"><?= $reserve->change ?></td>
	</tr>
	<?php $i = 0 ?>
	<?php foreach ($expenses as $name => $expense): ?>
		<?php if ($i % $cols === 0): ?>
			<tr id="<?= $class . '-' . $expense->class ?>" class="collapse <?= $class ?>">
			<th scope="row"><?= $name ?></th>
			<td><?= $expense->value ?></td>
			<?php if ($i === count($expenses) - 1): ?>
				<td class="<?= $expense->movement ?> less-is-good" colspan="7"><?= $expense->change ?></td>
			<?php else: ?>
				<td class="<?= $expense->movement ?> less-is-good"><?= $expense->change ?></td>
			<?php endif ?>
		<?php elseif ($i % $cols === 1): ?>
			<th scope="row"><?= $name ?></th>
			<td><?= $expense->value ?></td>
			<?php if ($i === count($expenses) - 1): ?>
				<td class="<?= $expense->movement ?> less-is-good" colspan="4"><?= $expense->change ?></td>
			<?php else: ?>
				<td class="<?= $expense->movement ?> less-is-good"><?= $expense->change ?></td>
			<?php endif ?>
		<?php else: ?>
			<th scope="row"><?= $name ?></th>
			<td><?= $expense->value ?></td>
			<td class="<?= $expense->movement ?> less-is-good"><?= $expense->change ?></td>
			</tr>
		<?php endif ?>
		<?php $i++ ?>
	<?php endforeach ?>
	<?php if (!empty($luxuries)): ?>
		<tr id="<?= $class . '-market' ?>" class="collapse <?= $class ?>">
			<th scope="row">Marktpreise</th>
			<td colspan="8">
				<table class="market table">
					<tr>
						<?php foreach ($luxuries as $luxury): ?>
							<th scope="col" colspan="2" class="<?= $luxury->offerDemand ?>"><?= $this->translate($luxury->class) ?></th>
						<?php endforeach ?>
					</tr>
					<tr class="td-16">
						<?php foreach ($luxuries as $luxury): ?>
							<td class="<?= $luxury->offerDemand ?>"><?= $luxury->value ?></td>
							<td class="<?= $luxury->movement ?> <?= $luxury->moreOrLess ?> <?= $luxury->offerDemand ?>"><?= $luxury->change ?></td>
						<?php endforeach ?>
					</tr>
				</table>
			</td>
		</tr>
	<?php endif ?>

<?php else: ?>
	<tr class="region<?= $realm ?>">
		<th scope="rowgroup" colspan="3">
			<a href=".<?= $class ?>" title="Details..." data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="<?= $ids ?>">
				<?php if ($realmId): ?>
					<span class="badge text-bg-light font-monospace"><?= $realmId ?></span>
				<?php endif ?>
				<span><?= $this->translate($region->Landscape()) ?> <?= $region->Name() ?></span>
			</a>
			&nbsp;<a href="#<?= id($region) ?>" title="zur Region" class="text-body">⮞</a>
		</th>
		<th scope="row">Bevölkerung</th>
		<td><?= $population->value ?></td>
		<td class="<?= $population->movement ?> more-is-good"><?= $population->change ?></td>
		<th scope="row">Silbervorrat</th>
		<td><?= $wealth->value ?></td>
		<td class="<?= $wealth->movement ?> more-is-good"><?= $wealth->change ?></td>
		<th scope="row">Einkommen</th>
		<td><?= $income->value ?></td>
		<td class="<?= $income->movement ?> more-is-good"><?= $income->change ?></td>
	</tr>
	<tr id="<?= $class?>-joblessness" class="collapse <?= $class ?>">
		<th scope="row">Arbeitslosigkeit</th>
		<td><?= $joblessness->value ?> %</td>
		<td class="<?= $joblessness->movement ?> less-is-good"><?= $joblessness->change ?></td>
		<th scope="row">Arbeitsplätze</th>
		<td><?= $workplaces->value ?></td>
		<td class="<?= $workplaces->movement ?> more-is-good"><?= $workplaces->change ?></td>
		<th scope="row">Wohlstand</th>
		<td><?= $prosperity->value ?> a</td>
		<td class="<?= $prosperity->movement ?> more-is-good"><?= $prosperity->change ?></td>
		<th scope="row">Geburten</th>
		<td><?= $births->value ?></td>
		<td class="<?= $births->movement ?> more-is-good"><?= $births->change ?></td>
	</tr>
	<tr id="<?= $class ?>-population" class="collapse <?= $class ?>">
		<th scope="row">Rekruten</th>
		<td><?= $recruits->value ?></td>
		<td class="<?= $recruits->movement ?> more-is-good"><?= $recruits->change ?></td>
		<th scope="row">Arbeiter</th>
		<td><?= $workers->value ?></td>
		<td class="<?= $workers->movement ?> more-is-good"><?= $workers->change ?></td>
		<th scope="row">Bauernwanderung</th>
		<td><?= $migration->value ?></td>
		<td class="<?= $migration->movement ?> more-is-good"><?= $migration->change ?></td>
		<th scope="row">Baumbestand</th>
		<td><?= $trees->value ?></td>
		<td class="<?= $trees->movement ?> more-is-good" colspan="10"><?= $trees->change ?></td>
	</tr>
	<?php foreach ($animals as $i => $animal): ?>
		<?php if ($i % $cols === 0): ?>
			<tr id="<?= $class . '-' . $animal->key ?>" class="collapse <?= $class ?>">
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="10"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php elseif ($i % $cols === 1): ?>
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="7"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php elseif ($i % $cols === 2): ?>
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="4"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php else: ?>
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			</tr>
		<?php endif ?>
	<?php endforeach ?>
	<tr id="<?= $class ?>-unit-force" class="collapse <?= $class ?>">
		<th scope="row">Einheiten</th>
		<td><?= $unitForce->value ?></td>
		<td class="<?= $unitForce->movement ?> more-is-good"><?= $unitForce->change ?></td>
		<th scope="row">Personen</th>
		<td><?= $peopleForce->value ?></td>
		<td class="<?= $peopleForce->movement ?> more-is-good"><?= $peopleForce->change ?></td>
		<th scope="row">Silberreserve</th>
		<td><?= $reserve->value ?></td>
		<td class="<?= $reserve->movement ?> more-is-good" colspan="4"><?= $reserve->change ?></td>
	</tr>
	<?php $i = 0 ?>
	<?php foreach ($expenses as $name => $expense): ?>
		<?php if ($i % $cols === 0): ?>
			<tr id="<?= $class . '-' . $expense->class ?>" class="collapse <?= $class ?>">
			<th scope="row"><?= $name ?></th>
			<td><?= $expense->value ?></td>
			<?php if ($i === count($expenses) - 1): ?>
				<td class="<?= $expense->movement ?> less-is-good" colspan="10"><?= $expense->change ?></td>
			<?php else: ?>
				<td class="<?= $expense->movement ?> less-is-good"><?= $expense->change ?></td>
			<?php endif ?>
		<?php elseif ($i % $cols === 1): ?>
			<th scope="row"><?= $name ?></th>
			<td><?= $expense->value ?></td>
			<?php if ($i === count($expenses) - 1): ?>
				<td class="<?= $expense->movement ?> less-is-good" colspan="7"><?= $expense->change ?></td>
			<?php else: ?>
				<td class="<?= $expense->movement ?> less-is-good"><?= $expense->change ?></td>
			<?php endif ?>
		<?php elseif ($i % $cols === 2): ?>
			<th scope="row"><?= $name ?></th>
			<td><?= $expense->value ?></td>
			<?php if ($i === count($expenses) - 1): ?>
				<td class="<?= $expense->movement ?> less-is-good" colspan="4"><?= $expense->change ?></td>
			<?php else: ?>
				<td class="<?= $expense->movement ?> less-is-good"><?= $expense->change ?></td>
			<?php endif ?>
		<?php else: ?>
			<th scope="row"><?= $name ?></th>
			<td><?= $expense->value ?></td>
			<td class="<?= $expense->movement ?> less-is-good"><?= $expense->change ?></td>
			</tr>
		<?php endif ?>
		<?php $i++ ?>
	<?php endforeach ?>
	<?php if (!empty($luxuries)): ?>
		<tr id="<?= $class . '-market' ?>" class="collapse <?= $class ?>">
			<th scope="row">Marktpreise</th>
			<td colspan="11">
				<table class="market table">
					<tr>
						<?php foreach ($luxuries as $luxury): ?>
							<th scope="col" colspan="2" class="<?= $luxury->offerDemand ?>"><?= $this->translate($luxury->class) ?></th>
						<?php endforeach ?>
					</tr>
					<tr class="td-16">
						<?php foreach ($luxuries as $luxury): ?>
							<td class="<?= $luxury->offerDemand ?>"><?= $luxury->value ?></td>
							<td class="<?= $luxury->movement ?> <?= $luxury->moreOrLess ?> <?= $luxury->offerDemand ?>"><?= $luxury->change ?></td>
						<?php endforeach ?>
					</tr>
				</table>
			</td>
		</tr>
	<?php endif ?>
<?php endif ?>
