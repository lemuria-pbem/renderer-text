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
<body class="non-responsive" data-spy="scroll" data-offset="250" data-target="#navbar">
	<header>
		<h1 class="text-center">Lemuria-Auswertung</h1>

		<p class="text-center">
			für die <?= $calendar->Week() ?>. Woche des Monats <?= $month ?> im <?= $season ?> des Jahres <?= $calendar->Year() ?><br>
			(Runde <?= $calendar->Round() ?>)
		</p>

		<button id="toggle-responsive" class="btn btn-light" title="Taste: #">Ansicht umschalten</button>
		<?= $this->template('goto'); ?>
		<button id="toggle-goto" class="btn btn-light" data-toggle="modal" data-target="#modal-goto" title="Taste: G">Gehe zu…</button>
		<?= $this->template('navigation') ?>
	</header>

	<hr>

	<?php if ($isPlayer): ?>
		<?= $this->template('header/player') ?>
	<?php else: ?>
		<?= $this->template('header/other') ?>
	<?php endif ?>

	<?php if ($isPlayer): ?>
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
</body>
