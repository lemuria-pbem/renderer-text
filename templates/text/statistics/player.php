<?php
declare (strict_types = 1);

use function Lemuria\number;
use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\line;
use function Lemuria\Renderer\Text\View\wrap;
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
$education = $this->numberStatistics(Subject::Education, $party, 'Gesamte Erfahrungspunkte');
$pool      = $this->materialPoolStatistics(Subject::MaterialPool, $party);

?>
Dein Volk: <?= $party->Name() ?> [<?= $party->Id() ?>]

<?= line($party->Description()) ?>

<?= line($banner) ?>

Dein Volk zählt <?= $this->number($census->count(), 'race', $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.
<?= wrap('Deine Einheiten sammeln ' . $this->loot() . '.') ?>
<?= wrap('Vorgaben für neue Einheiten: ' . implode(', ', $this->presettings()) . '.') ?>
<?php if ($this->isDevelopment()): ?>

<?= hr() ?>

<?= center('Statistik') ?>

<?= $header ?>
<?= $underline ?>
<?= $units ?>
<?= $people ?>
<?= $education ?>
<?= $underline ?>
<?php if (count($pool) > 0): ?>
Materialpool
<?php foreach ($pool as $resource => $number): ?>
<?= $number ?>
<?php endforeach ?>
<?php else: ?>
Der Materialpool ist leer.
<?php endif ?>
<?php endif ?>