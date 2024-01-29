<?php
declare (strict_types = 1);

use function Lemuria\number;
use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Renderer\Text\Text\TableRow;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$party  = $this->party;
$census = $this->census;
$banner = $party->Banner() ? 'Unser Banner: ' . $party->Banner() : '(kein Banner gesetzt)';
$round  = Lemuria::Calendar()->Round();

$header    = (string)new TableRow('Deine Partei', 'Runde ' . number($round), 'Veränderung');
$underline = str_pad('-------------------------------', mb_strlen(trim($header)), '-') . PHP_EOL;
$units     = $this->numberStatistics(Subject::Units, $party, 'Anzahl Einheiten');
$people    = $this->numberStatistics(Subject::People, $party, 'Anzahl Personen');
$races     = $this->racesStatistics($party);
$education = $this->numberStatistics(Subject::Education, $party, 'Gesamte Erfahrungspunkte');
$expenses  = $this->numberStatistics(Subject::Expenses, $party, 'Gesamte Ausgaben');
$experts   = $this->expertsStatistics(Subject::Experts, $party);
$pool      = $this->materialPoolStatistics(Subject::MaterialPool, $party);

?>
<?= hr() ?>

<?= center('Statistik') ?>

<?= $header ?>
<?= $underline ?>
<?= $units ?>
<?= $people ?>
<?php foreach ($races as $number): ?>
<?= $number ?>
<?php endforeach ?>
<?= $education ?>
<?= $expenses ?>
<?= $underline ?>
Talent-Experten
<?php foreach ($experts as $number): ?>
<?= $number ?>
<?php endforeach ?>
<?= $underline ?>
<?php if (count($pool) > 0): ?>
Materialpool
<?php foreach ($pool as $number): ?>
<?= $number ?>
<?php endforeach ?>
<?php else: ?>
Der Materialpool ist leer.
<?php endif ?>