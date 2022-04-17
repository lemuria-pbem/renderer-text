<?php
declare (strict_types = 1);

use function Lemuria\number;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\Text\TableRow;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Region $region */
$region = $this->variables[0];
foreach ($region->Residents() as $unit /* @var Unit $unit */) {
	if ($unit->Party() === $this->party) {
		break;
	}
}
$round = Lemuria::Calendar()->Round();

$header      = (string)new TableRow('Regionsstatistik', 'Runde ' . number($round), 'Veränderung');
$charity     = $this->numberStatisticsOrNull(Subject::Charity, $unit, 'Almosen an Fremdeinheiten');
$learning    = $this->numberStatisticsOrNull(Subject::LearningCosts, $unit, 'Lernkosten');
$maintenance = $this->numberStatisticsOrNull(Subject::Maintenance, $unit, 'Ausgaben für Gebäude');
$purchase    = $this->numberStatisticsOrNull(Subject::Purchase, $unit, 'Handelseinkäufe');
$recruiting  = $this->numberStatisticsOrNull(Subject::Recruiting, $unit, 'Ausgaben für Rekrutierung');
$support     = $this->numberStatisticsOrNull(Subject::Support, $unit, 'Ausgaben für Unterhalt');

?>
<?= $header ?>
<?= $support ?: '' ?>
<?= $maintenance ?: '' ?>
<?= $recruiting ?: '' ?>
<?= $learning ?: '' ?>
<?= $purchase ?: '' ?>
<?= $charity ?: '' ?>
