<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region = $this->variables[0];
$class  = 'region-' . $region->Id()->Id();

$population = $this->numberStatistics(Subject::Population, $region);
$workers    = $this->numberStatistics(Subject::Workers, $region);
$recruits   = $this->numberStatistics(Subject::Unemployment, $region);
$births     = $this->numberStatistics(Subject::Births, $region);
$migration  = $this->numberStatistics(Subject::Migration, $region);
$wealth     = $this->numberStatistics(Subject::Wealth, $region);
$income     = $this->numberStatistics(Subject::Income, $region);
$ids        = $class . '-population ' . $class . '-births ' . $class . '-migration ' . $class . '-workers ' . $class . '-recruits ' . $class . '-wealth ' . $class . '-income';

?>
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
