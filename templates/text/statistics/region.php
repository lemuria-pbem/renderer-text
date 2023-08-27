<?php
/** @noinspection PhpUndefinedVariableInspection */
declare (strict_types = 1);

use function Lemuria\number;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\Text\TableRow;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Region $region */
$region = $this->variables[0];
foreach ($region->Residents() as $unit) {
	if ($unit->Party() === $this->party) {
		break;
	}
}
$round = Lemuria::Calendar()->Round();

$header         = (string)new TableRow('Regionsstatistik', 'Runde ' . number($round), 'Veränderung');
$infrastructure = $this->numberStatistics(Subject::Infrastructure, $region, 'Baupunkte');
$workplaces     = $this->numberStatistics(Subject::Workplaces, $region, 'Arbeitsplätze');
$joblessness    = $this->numberStatistics(Subject::Joblessness, $region, 'Arbeitslosigkeit', '%');
$prosperity     = $this->numberStatistics(Subject::Prosperity, $region, 'Wohlstand', 'a');
$charity        = $this->numberStatisticsOrNull(Subject::Charity, $unit, 'Almosen an Fremdeinheiten');
$learning       = $this->numberStatisticsOrNull(Subject::LearningCosts, $unit, 'Lernkosten');
$unitForce      = $this->numberStatisticsOrNull(Subject::UnitForce, $unit, 'Einheiten');
$peopleForce    = $this->numberStatisticsOrNull(Subject::PeopleForce, $unit, 'Personen');
$maintenance    = $this->numberStatisticsOrNull(Subject::Maintenance, $unit, 'Ausgaben für Gebäude');
$purchase       = $this->numberStatisticsOrNull(Subject::Purchase, $unit, 'Handelseinkäufe');
$recruiting     = $this->numberStatisticsOrNull(Subject::Recruiting, $unit, 'Ausgaben für Rekrutierung');
$support        = $this->numberStatisticsOrNull(Subject::Support, $unit, 'Ausgaben für Unterhalt');

?>
<?= $header ?>
<?= $infrastructure ?>
<?= $workplaces ?>
<?= $prosperity ?>
<?= $joblessness ?>
<?= $support ?: '' ?>
<?= $unitForce ?: '' ?>
<?= $peopleForce ?: '' ?>
<?= $maintenance ?: '' ?>
<?= $recruiting ?: '' ?>
<?= $learning ?: '' ?>
<?= $purchase ?: '' ?>
<?= $charity ?: '' ?>
