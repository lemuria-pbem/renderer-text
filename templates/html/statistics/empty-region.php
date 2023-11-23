<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region = $this->variables[0];
$quotas = $this->quotasTitle($region);
$cols   = max(1, min(4, $this->variables[1]));
$prefix = match ($cols) {
	2       => 'md-stat-',
	3       => 'lg-stat-',
	4       => 'xl-stat-',
	default => 'stat-'
};
$stripe    = $this->variables[2] % 2 ? ' table-light' : '';
$realmId   = $region->Realm()?->Identifier();
$territory = $region->Realm()?->Territory();
$realm     = $territory ? ($territory->Central() === $region ? ' realm center' : ' realm') : '';
$class     = $prefix . id($region);

$population     = $this->numberStatistics(Subject::Population, $region);
$workers        = $this->numberStatistics(Subject::Workers, $region);
$infrastructure = $this->numberStatistics(Subject::Infrastructure, $region);
$workplaces     = $this->numberStatistics(Subject::Workplaces, $region);
$recruits       = $this->numberStatistics(Subject::Unemployment, $region);
$births         = $this->numberStatistics(Subject::Births, $region);
$migration      = $this->numberStatistics(Subject::Migration, $region);
$wealth         = $this->numberStatistics(Subject::Wealth, $region);
$income         = $this->numberStatistics(Subject::Income, $region);
$joblessness    = $this->numberStatistics(Subject::Joblessness, $region);
$prosperity     = $this->numberStatistics(Subject::Prosperity, $region);
$trees          = $this->numberStatistics(Subject::Trees, $region);
$animals        = $this->animalStatistics(Subject::Animals, $region);
$luxuries       = $this->marketStatistics(Subject::Market, $region);

?>
<?php if ($cols <= 1): ?>
	<tr class="region<?= $realm . $stripe ?>">
		<th scope="rowgroup" colspan="3">
			<a href="#<?= id($region) ?>" title="zur Region">
				<?php if ($realmId): ?>
					<span class="badge text-bg-light font-monospace"><?= $realmId ?></span>
				<?php endif ?>
				<span><?= $this->translate($region->Landscape()) ?> <?= $region->Name() ?></span>
			</a>
			<?php if ($quotas): ?>
				<span title="Grenzen: <?= $quotas ?>">&nbsp;🛠️</span>
			<?php endif ?>
		</th>
	</tr>
	<tr class="<?= $population->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Bevölkerung</th>
		<td><?= $population->value ?></td>
		<td class="more-is-good"><?= $population->change ?></td>
	</tr>
	<tr class="<?= $infrastructure->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Baupunkte</th>
		<td><?= $infrastructure->value ?></td>
		<td class="more-is-good"><?= $infrastructure->change ?></td>
	</tr>
	<tr class="<?= $workplaces->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Arbeitsplätze</th>
		<td><?= $workplaces->value ?></td>
		<td class="more-is-good"><?= $workplaces->change ?></td>
	</tr>
	<tr class="<?= $joblessness->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Arbeitslosigkeit</th>
		<td><?= $joblessness->value ?> %</td>
		<td class="less-is-good"><?= $joblessness->change ?></td>
	</tr>
	<tr class="<?= $workers->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Arbeiter</th>
		<td><?= $workers->value ?></td>
		<td class="more-is-good"><?= $workers->change ?></td>
	</tr>
	<tr class="<?= $recruits->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Rekruten</th>
		<td><?= $recruits->value ?></td>
		<td class="more-is-good"><?= $recruits->change ?></td>
	</tr>
	<tr class="<?= $births->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Geburten</th>
		<td><?= $births->value ?></td>
		<td class="more-is-good"><?= $births->change ?></td>
	</tr>
	<tr class="<?= $migration->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Bauernwanderung</th>
		<td><?= $migration->value ?></td>
		<td class="more-is-good"><?= $migration->change ?></td>
	</tr>
	<tr class="<?= $prosperity->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Wohlstand</th>
		<td><?= $prosperity->value ?> a</td>
		<td class="more-is-good"><?= $prosperity->change ?></td>
	</tr>
	<tr class="<?= $wealth->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Silbervorrat</th>
		<td><?= $wealth->value ?></td>
		<td class="more-is-good"><?= $wealth->change ?></td>
	</tr>
	<tr class="<?= $income->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Einkommen</th>
		<td><?= $income->value ?></td>
		<td class="more-is-good"><?= $income->change ?></td>
	</tr>
	<tr class="<?= $trees->movement ?> <?= $class . $stripe ?>">
		<th scope="row">Baumbestand</th>
		<td><?= $trees->value ?></td>
		<td class="more-is-good"><?= $trees->change ?></td>
	</tr>
	<?php foreach ($animals as $animal): ?>
		<tr class="<?= $animal->movement ?> <?= $class . $stripe ?>">
			<th scope="row">Anzahl <?= $this->translate($animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<td class="more-is-good"><?= $animal->change ?></td>
		</tr>
	<?php endforeach ?>
	<?php if (!empty($luxuries)): ?>
		<tr class="<?= $class . $stripe ?>">
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
	<tr class="region<?= $realm . $stripe ?>">
		<th scope="rowgroup" colspan="3">
			<a href="#<?= id($region) ?>" title="zur Region">
				<?php if ($realmId): ?>
					<span class="badge text-bg-light font-monospace"><?= $realmId ?></span>
				<?php endif ?>
				<span><?= $this->translate($region->Landscape()) ?> <?= $region->Name() ?></span>
			</a>
			<?php if ($quotas): ?>
				<span title="Grenzen: <?= $quotas ?>">&nbsp;🛠️</span>
			<?php endif ?>
		</th>
		<th scope="row">Bevölkerung</th>
		<td><?= $population->value ?></td>
		<td class="<?= $population->movement ?> more-is-good"><?= $population->change ?></td>
	</tr>
	<tr class="<?= $class . $stripe ?>">
		<th scope="row">Arbeitslosigkeit</th>
		<td><?= $joblessness->value ?> %</td>
		<td class="<?= $joblessness->movement ?> less-is-good"><?= $joblessness->change ?></td>
		<th scope="row">Arbeitsplätze</th>
		<td><?= $workplaces->value ?></td>
		<td class="<?= $workplaces->movement ?> more-is-good"><?= $workplaces->change ?></td>
	</tr>
	<tr class="<?= $class . $stripe ?>">
		<th scope="row">Rekruten</th>
		<td><?= $recruits->value ?></td>
		<td class="<?= $recruits->movement ?> more-is-good"><?= $recruits->change ?></td>
		<th scope="row">Arbeiter</th>
		<td><?= $workers->value ?></td>
		<td class="<?= $workers->movement ?> more-is-good"><?= $workers->change ?></td>
	</tr>
	<tr class="<?= $class . $stripe ?>">
		<th scope="row">Geburten</th>
		<td><?= $births->value ?></td>
		<td class="<?= $births->movement ?> more-is-good"><?= $births->change ?></td>
		<th scope="row">Bauernwanderung</th>
		<td><?= $migration->value ?></td>
		<td class="<?= $migration->movement ?> more-is-good"><?= $migration->change ?></td>
	</tr>
	<tr class="<?= $class . $stripe ?>">
		<th scope="row">Silbervorrat</th>
		<td><?= $wealth->value ?></td>
		<td class="<?= $wealth->movement ?> more-is-good"><?= $wealth->change ?></td>
		<th scope="row">Einkommen</th>
		<td><?= $income->value ?></td>
		<td class="<?= $income->movement ?> more-is-good"><?= $income->change ?></td>
	</tr>
	<tr class="<?= $class . $stripe ?>">
		<th scope="row">Wohlstand</th>
		<td><?= $prosperity->value ?> a</td>
		<td class="<?= $prosperity->movement ?> more-is-good"><?= $prosperity->change ?></td>
		<th scope="row">Baumbestand</th>
		<td><?= $trees->value ?></td>
		<td class="<?= $trees->movement ?> more-is-good"><?= $trees->change ?></td>
	</tr>
	<?php foreach ($animals as $i => $animal): ?>
		<?php if ($i % $cols === 0): ?>
			<tr class="<?= $class . $stripe ?>">
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
	<tr class="<?= $class . $stripe ?>">
		<th scope="row">Baupunkte</th>
		<td><?= $infrastructure->value ?></td>
		<td class="<?= $infrastructure->movement ?> more-is-good" colspan="4"><?= $infrastructure->change ?></td>
	</tr>
	<?php if (!empty($luxuries)): ?>
		<tr class="<?= $class . $stripe ?>">
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
	<tr class="region<?= $realm . $stripe ?>">
		<th scope="rowgroup" colspan="3">
			<a href="#<?= id($region) ?>" title="zur Region">
				<?php if ($realmId): ?>
					<span class="badge text-bg-light font-monospace"><?= $realmId ?></span>
				<?php endif ?>
				<span><?= $this->translate($region->Landscape()) ?> <?= $region->Name() ?></span>
			</a>
			<?php if ($quotas): ?>
				<span title="Grenzen: <?= $quotas ?>">&nbsp;🛠️</span>
			<?php endif ?>
		</th>
		<th scope="row">Bevölkerung</th>
		<td><?= $population->value ?></td>
		<td class="<?= $population->movement ?> more-is-good"><?= $population->change ?></td>
		<th scope="row">Silbervorrat</th>
		<td><?= $wealth->value ?></td>
		<td class="<?= $wealth->movement ?> more-is-good"><?= $wealth->change ?></td>
	</tr>
	<tr class="<?= $class . $stripe ?>">
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
	<tr class="<?= $class . $stripe ?>">
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
	<tr class="<?= $class . $stripe ?>">
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
			<tr class="<?= $class . $stripe ?>">
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
	<tr class="<?= $class . $stripe ?>">
		<th scope="row">Baupunkte</th>
		<td><?= $infrastructure->value ?></td>
		<td class="<?= $infrastructure->movement ?> more-is-good" colspan="7"><?= $infrastructure->change ?></td>
	</tr>
	<?php if (!empty($luxuries)): ?>
		<tr class="<?= $class . $stripe ?>">
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
	<tr class="region<?= $realm . $stripe ?>">
		<th scope="rowgroup" colspan="3">
			<a href="#<?= id($region) ?>" title="zur Region">
				<?php if ($realmId): ?>
					<span class="badge text-bg-light font-monospace"><?= $realmId ?></span>
				<?php endif ?>
				<span><?= $this->translate($region->Landscape()) ?> <?= $region->Name() ?></span>
			</a>
			<?php if ($quotas): ?>
				<span title="Grenzen: <?= $quotas ?>">&nbsp;🛠️</span>
			<?php endif ?>
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
	<tr class="<?= $class . $stripe ?>">
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
	<tr class="<?= $class . $stripe ?>">
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
			<tr class="<?= $class . $stripe ?>">
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
	<tr class="<?= $class . $stripe ?>">
		<th scope="row">Baupunkte</th>
		<td><?= $infrastructure->value ?></td>
		<td class="<?= $infrastructure->movement ?> more-is-good" colspan="10"><?= $infrastructure->change ?></td>
	</tr>
	<?php if (!empty($luxuries)): ?>
		<tr class="<?= $class . $stripe ?>">
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
