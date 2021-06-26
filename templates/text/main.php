<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\footer;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\line;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

$party     = $this->party;
$banner    = $this->party->Banner() ? 'Unser Banner: ' . $this->party->Banner() : '(kein Banner gesetzt)';
$census    = $this->census;
$atlas     = $this->atlas;
$calendar  = Lemuria::Calendar();
$season    = $this->get('calendar.season', $calendar->Season() - 1);
$month     = $this->get('calendar.month', $calendar->Month() - 1);
$week      = $calendar->Week();
$continent = Continent::get(new Id(1));

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

<?= $this->template('continent', $continent) ?>

<?php foreach ($atlas as $region): ?>
<?= $this->wrappedTemplate('region', $region) ?>
<?php endforeach ?>
<?= footer($this->gameVersions()) ?>
