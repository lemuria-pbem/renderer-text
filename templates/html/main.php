<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\linkEmail;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\Party\Type;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party     = $this->party;
$isPlayer  = $party->Type() === Type::PLAYER;
$census    = $this->census;
$atlas     = $this->atlas;
$calendar  = Lemuria::Calendar();
$season    = $this->get('calendar.season', $calendar->Season() - 1);
$month     = $this->get('calendar.month', $calendar->Month() - 1);
$banner    = $party->Banner() ? 'Unser Banner: ' . linkEmail($party->Banner()) : '(kein Banner gesetzt)';
$continent = Continent::get(new Id(1));

?>
<header>
	<h1 class="text-center">Lemuria-Auswertung</h1>

	<p class="text-center">
		für die <?= $calendar->Week() ?>. Woche des Monats <?= $month ?> im <?= $season ?> des Jahres <?= $calendar->Year() ?><br>
		(Runde <?= $calendar->Round() ?>)
	</p>
</header>

<hr>

<?php if ($isPlayer): ?>
	<?= $this->template('header/player') ?>
<?php else: ?>
	<?= $this->template('header/other') ?>
<?php endif ?>

<h3>Ereignisse</h3>

<?= $this->template('report', $party) ?>
<?= $this->template('hostilities', $party) ?>

<?php if ($isPlayer): ?>
	<h3>Alle bekannten Völker</h3>

	<?= $this->template('acquaintances', $party) ?>
<?php endif ?>

<hr>

<?php if ($isPlayer): ?>
	<?= $this->template('continent/player', $continent) ?>
<?php else: ?>
	<?= $this->template('continent/other', $continent) ?>
<?php endif ?>

<?php foreach ($atlas as $region): ?>
	<?= $this->template('region', $region) ?>
<?php endforeach ?>

<hr>

<?= $this->template('footer') ?>
