<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\linkEmail;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party     = $this->party;
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

<h2><?= $party->Name() ?> <span class="badge badge-primary"><?= $party->Id() ?></span></h2>

<blockquote class="blockquote"><?= $party->Description() ?></blockquote>

<p><?= $banner ?></p>

<p>Dein Volk zählt <?= $this->number($census->count(), 'race', $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.</p>

<h3>Ereignisse</h3>

<?= $this->template('report', $party) ?>

<h3>Alle bekannten Völker</h3>

<?= $this->template('acquaintances', $party) ?>

<hr>

<?= $this->template('continent', $continent) ?>

<?php foreach ($atlas as $region): ?>
	<?= $this->template('region', $region) ?>
<?php endforeach ?>

<hr>

<?= $this->template('footer') ?>
