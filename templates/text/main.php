<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\footer;
use function Lemuria\Renderer\Text\View\hr;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$party     = $this->party;
$isPlayer  = $party->Type() === Type::PLAYER;
$banner    = $this->party->Banner() ? 'Unser Banner: ' . $this->party->Banner() : '(kein Banner gesetzt)';
$travelLog = $this->travelLog;
$calendar  = Lemuria::Calendar();
$season    = $this->get('calendar.season', $calendar->Season() - 1);
$month     = $this->get('calendar.month', $calendar->Month() - 1);
$continent = Continent::get(new Id(1));

?>
<?= center('Lemuria-Auswertung') ?>
<?= center('~~~~~~~~~~~~~~~~~~~~~~~~') ?>

<?= center('für die ' . $calendar->Week() . '. Woche des Monats ' . $month . ' im ' . $season . ' des Jahres ' . $calendar->Year()) ?>
<?= center('(Runde ' . $calendar->Round() . ')') ?>


<?php if ($isPlayer): ?>
<?= $this->template('statistics/player') ?>
<?php else: ?>
<?= $this->template('statistics/other') ?>
<?php endif ?>

<?= hr() ?>

<?= center('Ereignisse') ?>
<?= $this->template('report', $party) ?>
<?= $this->template('announcements') ?>
<?= $this->template('hostilities') ?>

<?php if ($isPlayer): ?>
<?= hr() ?>

<?= center('Alle bekannten Völker') ?>
<?= $this->wrappedTemplate('acquaintances', $party) ?>
<?php endif ?>
<?php foreach ($travelLog as $continent => $atlas): ?>
<?php if ($atlas->count() > 0): ?>
<?= hr() ?>

<?php if ($isPlayer): ?>
<?= $this->template('continent/player', $continent) ?>
<?php else: ?>
<?= $this->template('continent/other', $continent) ?>
<?php endif ?>

<?php foreach ($atlas as $region): ?>
<?= $this->wrappedTemplate('region', $region) ?>
<?php endforeach ?>
<?php endif ?>
<?php endforeach ?>
<?= footer($this->gameVersions()) ?>
