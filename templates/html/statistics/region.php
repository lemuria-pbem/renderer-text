<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region = $this->variables[0];
$cols   = max(1, min(4, $this->variables[1]));
$prefix = match ($cols) {
	2       => 'md-',
	3       => 'lg-',
	4       => 'xl-',
	default => ''
};
$class  = $prefix . 'region-' . $region->Id()->Id();

$population = $this->numberStatistics(Subject::Population, $region);
$workers    = $this->numberStatistics(Subject::Workers, $region);
$recruits   = $this->numberStatistics(Subject::Unemployment, $region);
$births     = $this->numberStatistics(Subject::Births, $region);
$migration  = $this->numberStatistics(Subject::Migration, $region);
$wealth     = $this->numberStatistics(Subject::Wealth, $region);
$income     = $this->numberStatistics(Subject::Income, $region);
$trees      = $this->numberStatistics(Subject::Trees, $region);
$animals    = $this->animalStatistics(Subject::Animals, $region);
$luxuries   = $this->marketStatistics(Subject::Market, $region);

if ($cols <= 1) {
	$ids = $class . '-population ' . $class . '-workers ' . $class . '-recruits ' . $class . '-births ' .
		   $class . '-migration ' . $class . '-wealth ' . $class . '-income ' . $class . '-trees';
} elseif ($cols === 2) {
	$ids = $class . '-population ' . $class . '-peasants ' . $class . '-wealth ' . $class . '-trees';
} elseif ($cols === 3) {
	$ids = $class . '-population ' . $class . '-peasants';
} else {
	$ids = $class . '-population ' . $class . '-resources';
}
foreach ($animals as $i => $animal) {
	if ($i % $cols === 0) {
		$ids .= ' ' . $class . '-' . $animal->key;
	}
}
if (!empty($luxuries)) {
	$ids .= ' ' . $class . '-market';
}

?>
<?php if ($cols <= 1): ?>
	<tr>
		<th scope="rowgroup" colspan="3">
			<a href=".<?= $class ?>" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="<?= $ids ?>"><?= $this->get('landscape', $region->Landscape()) ?> <?= $region->Name() ?></a>
		</th>
	</tr>
	<tr id="<?= $class ?>-population" class="collapse <?= $population->movement ?> <?= $class ?>">
		<th scope="row">Bevölkerung</th>
		<td><?= $population->value ?></td>
		<td class="more-is-good"><?= $population->change ?></td>
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
			<th scope="row">Anzahl <?= $this->get('resource.' . $animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<td class="more-is-good"><?= $animal->change ?></td>
		</tr>
	<?php endforeach ?>
	<?php if (!empty($luxuries)): ?>
		<tr id="<?= $class . '-market' ?>" class="collapse <?= $class ?>">
			<td colspan="3">
				<table class="table">
					<caption>Marktpreise</caption>
					<tr>
						<?php foreach ($luxuries as $luxury): ?>
							<th scope="col" colspan="2"><?= $this->get('resource.' . $luxury->class) ?></th>
						<?php endforeach ?>
					</tr>
					<tr class="td-16">
						<?php foreach ($luxuries as $luxury): ?>
							<td><?= $luxury->value ?></td>
							<td class="<?= $luxury->movement ?> <?= $luxury->moreOrLess ?>"><?= $luxury->change ?></td>
						<?php endforeach ?>
					</tr>
				</table>
			</td>
		</tr>
	<?php endif ?>

<?php elseif ($cols === 2): ?>
	<tr>
		<th scope="rowgroup" colspan="3">
			<a href=".<?= $class ?>" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="<?= $ids ?>"><?= $this->get('landscape', $region->Landscape()) ?> <?= $region->Name() ?></a>
		</th>
		<th scope="row">Bevölkerung</th>
		<td><?= $population->value ?></td>
		<td class="<?= $population->movement ?> more-is-good"><?= $population->change ?></td>
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
		<th scope="row">Einkommen</th>
		<td><?= $income->value ?></td>
		<td class="<?= $income->movement ?> more-is-good"><?= $income->change ?></td>
		<th scope="row">Silbervorrat</th>
		<td><?= $wealth->value ?></td>
		<td class="<?= $wealth->movement ?> more-is-good"><?= $wealth->change ?></td>
	</tr>
	<tr id="<?= $class ?>-trees" class="collapse <?= $class ?>">
		<th scope="row">Baumbestand</th>
		<td><?= $trees->value ?></td>
		<td class="<?= $trees->movement ?> more-is-good" colspan="4"><?= $trees->change ?></td>
	</tr>
	<?php foreach ($animals as $i => $animal): ?>
		<?php if ($i % $cols === 0): ?>
			<tr id="<?= $class . '-' . $animal->key ?>" class="collapse <?= $class ?>">
			<th scope="row">Anzahl <?= $this->get('resource.' . $animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="4"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php else: ?>
			<th scope="row">Anzahl <?= $this->get('resource.' . $animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			</tr>
		<?php endif ?>
	<?php endforeach ?>
	<?php if (!empty($luxuries)): ?>
		<tr id="<?= $class . '-market' ?>" class="collapse <?= $class ?>">
			<th scope="row">Marktpreise</th>
			<td colspan="5">
				<table class="table">
					<tr>
						<?php foreach ($luxuries as $luxury): ?>
							<th scope="col" colspan="2"><?= $this->get('resource.' . $luxury->class) ?></th>
						<?php endforeach ?>
					</tr>
					<tr class="td-16">
						<?php foreach ($luxuries as $luxury): ?>
							<td><?= $luxury->value ?></td>
							<td class="<?= $luxury->movement ?> <?= $luxury->moreOrLess ?>"><?= $luxury->change ?></td>
						<?php endforeach ?>
					</tr>
				</table>
			</td>
		</tr>
	<?php endif ?>

<?php elseif ($cols === 3): ?>
	<tr>
		<th scope="rowgroup" colspan="3">
			<a href=".<?= $class ?>" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="<?= $ids ?>"><?= $this->get('landscape', $region->Landscape()) ?> <?= $region->Name() ?></a>
		</th>
		<th scope="row">Bevölkerung</th>
		<td><?= $population->value ?></td>
		<td class="<?= $population->movement ?> more-is-good"><?= $population->change ?></td>
		<th scope="row">Silbervorrat</th>
		<td><?= $wealth->value ?></td>
		<td class="<?= $wealth->movement ?> more-is-good"><?= $wealth->change ?></td>
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
			<th scope="row">Anzahl <?= $this->get('resource.' . $animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="7"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php elseif ($i % $cols === 1): ?>
			<th scope="row">Anzahl <?= $this->get('resource.' . $animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="4"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php else: ?>
			<th scope="row">Anzahl <?= $this->get('resource.' . $animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			</tr>
		<?php endif ?>
	<?php endforeach ?>
	<?php if (!empty($luxuries)): ?>
		<tr id="<?= $class . '-market' ?>" class="collapse <?= $class ?>">
			<th scope="row">Marktpreise</th>
			<td colspan="8">
				<table class="table">
					<tr>
						<?php foreach ($luxuries as $luxury): ?>
							<th scope="col" colspan="2"><?= $this->get('resource.' . $luxury->class) ?></th>
						<?php endforeach ?>
					</tr>
					<tr class="td-16">
						<?php foreach ($luxuries as $luxury): ?>
							<td><?= $luxury->value ?></td>
							<td class="<?= $luxury->movement ?> <?= $luxury->moreOrLess ?>"><?= $luxury->change ?></td>
						<?php endforeach ?>
					</tr>
				</table>
			</td>
		</tr>
	<?php endif ?>

<?php else: ?>
	<tr>
		<th scope="rowgroup" colspan="3">
			<a href=".<?= $class ?>" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="<?= $ids ?>"><?= $this->get('landscape', $region->Landscape()) ?> <?= $region->Name() ?></a>
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
	<tr id="<?= $class ?>-population" class="collapse <?= $class ?>">
		<th scope="row">Rekruten</th>
		<td><?= $recruits->value ?></td>
		<td class="<?= $recruits->movement ?> more-is-good"><?= $recruits->change ?></td>
		<th scope="row">Arbeiter</th>
		<td><?= $workers->value ?></td>
		<td class="<?= $workers->movement ?> more-is-good"><?= $workers->change ?></td>
		<th scope="row">Geburten</th>
		<td><?= $births->value ?></td>
		<td class="<?= $births->movement ?> more-is-good"><?= $births->change ?></td>
		<th scope="row">Bauernwanderung</th>
		<td><?= $migration->value ?></td>
		<td class="<?= $migration->movement ?> more-is-good"><?= $migration->change ?></td>
	</tr>
	<tr id="<?= $class ?>-resources" class="collapse <?= $class ?>">
		<th scope="row">Baumbestand</th>
		<td><?= $trees->value ?></td>
		<td class="<?= $trees->movement ?> more-is-good" colspan="10"><?= $trees->change ?></td>
	</tr>
	<?php foreach ($animals as $i => $animal): ?>
		<?php if ($i % $cols === 0): ?>
			<tr id="<?= $class . '-' . $animal->key ?>" class="collapse <?= $class ?>">
			<th scope="row">Anzahl <?= $this->get('resource.' . $animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="10"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php elseif ($i % $cols === 1): ?>
			<th scope="row">Anzahl <?= $this->get('resource.' . $animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="7"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php elseif ($i % $cols === 2): ?>
			<th scope="row">Anzahl <?= $this->get('resource.' . $animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<?php if ($i === count($animals) - 1): ?>
				<td class="<?= $animal->movement ?> more-is-good" colspan="4"><?= $animal->change ?></td>
			<?php else: ?>
				<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			<?php endif ?>
		<?php else: ?>
			<th scope="row">Anzahl <?= $this->get('resource.' . $animal->class, 1) ?></th>
			<td><?= $animal->value ?></td>
			<td class="<?= $animal->movement ?> more-is-good"><?= $animal->change ?></td>
			</tr>
		<?php endif ?>
	<?php endforeach ?>
	<?php if (!empty($luxuries)): ?>
		<tr id="<?= $class . '-market' ?>" class="collapse <?= $class ?>">
			<th scope="row">Marktpreise</th>
			<td colspan="11">
				<table class="table">
					<tr>
						<?php foreach ($luxuries as $luxury): ?>
							<th scope="col" colspan="2"><?= $this->get('resource.' . $luxury->class) ?></th>
						<?php endforeach ?>
					</tr>
					<tr class="td-16">
						<?php foreach ($luxuries as $luxury): ?>
							<td><?= $luxury->value ?></td>
							<td class="<?= $luxury->movement ?> <?= $luxury->moreOrLess ?>"><?= $luxury->change ?></td>
						<?php endforeach ?>
					</tr>
				</table>
			</td>
		</tr>
	<?php endif ?>
<?php endif ?>
