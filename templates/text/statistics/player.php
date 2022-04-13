<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\line;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Renderer\Text\Statistics\Data\TextNumber;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$party  = $this->party;
$census = $this->census;
$banner = $party->Banner() ? 'Unser Banner: ' . $party->Banner() : '(kein Banner gesetzt)';
$round  = Lemuria::Calendar()->Round();

$length     = TextNumber::LENGTH - strlen('Runde ');
$statUnits  = $this->numberStatistics(Subject::Units, $party);
$statPeople = $this->numberStatistics(Subject::People, $party);

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

Deine Partei              Runde <?= sprintf('%-' . $length . 'u', $round) ?>   Veränderung
--------------------------------------------------
Anzahl Einheiten          <?= $statUnits->value ?>   <?= $statUnits->change ?>

Anzahl Personen           <?= $statPeople->value ?>   <?= $statPeople->change ?>

<?php endif ?>