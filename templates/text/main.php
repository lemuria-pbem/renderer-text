<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\footer;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

$party            = $this->party;
$banner           = $this->party->Banner() ? 'Unser Banner: ' . $this->party->Banner() : '(kein Banner gesetzt)';
$report           = $this->messages($party);
$diplomacy        = $party->Diplomacy();
$acquaintances    = $diplomacy->Acquaintances();
$generalRelations = $diplomacy->search($party);
$census           = $this->census;
$outlook          = $this->outlook;
$atlas            = $this->atlas;
$map              = $this->map;
$race             = getClass($party->Race());
$calendar         = Lemuria::Calendar();
$season           = $this->get('calendar.season', $calendar->Season() - 1);
$month            = $this->get('calendar.month', $calendar->Month() - 1);
$week             = $calendar->Week();

?>
<?= center('Lemuria-Auswertung') ?>
<?= center('~~~~~~~~~~~~~~~~~~~~~~~~') ?>

<?= center('für die ' . $week . '. Woche des Monats ' . $month . ' im ' . $season . ' des Jahres ' . $calendar->Year()) ?>
<?= center('(Runde ' . $calendar->Round() . ')') ?>


Dein Volk: <?= $party->Name() ?> [<?= $party->Id() ?>]

<?= line($party->Description()) ?>

<?= line($banner) ?>

Dein Volk zählt <?= $this->number($census->count(), 'race', $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.

<?= hr() ?>

<?= center('Ereignisse') ?>

<?= $this->template('report', $party) ?>

<?= hr() ?>

<?= center('Alle bekannten Völker') ?>
<?= $this->wrappedTemplate('acquaintances', $party) ?>
<?= hr() ?>

<?= center('Kontinent Lemuria [' . $party->Id() . ']') ?>

Dies ist der Hauptkontinent Lemuria.

<?php foreach ($atlas as $region /* @var Region $region */): ?>
<?= $this->wrappedTemplate('region', $region) ?>
<?php endforeach ?>
<?= footer() ?>
