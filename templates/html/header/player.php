<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\linkEmail;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Lemuria;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party  = $this->party;
$census = $this->census;
$banner = $party->Banner() ? 'Unser Banner: ' . linkEmail($party->Banner()) : '(kein Banner gesetzt)';
$round  = Lemuria::Calendar()->Round();

$units  = $this->numberStatistics(Subject::Units, $party);
$people = $this->numberStatistics(Subject::People, $party);

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-lg-6 p-0 pe-lg-3">
			<h2><?= $party->Name() ?> <span class="badge text-bg-primary font-monospace"><?= $party->Id() ?></span></h2>

			<blockquote class="blockquote"><?= $party->Description() ?></blockquote>

			<p><?= $banner ?></p>

			<p>
				Dein Volk zählt <?= $this->number($census->count(), 'race', $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.<br>
				Deine Einheiten sammeln <?= $this->loot() ?>.<br>
				Vorgaben für neue Einheiten: <?= implode(', ', $this->presettings()) ?>.<br>
				Vorgaben für neue Handelsangebote: <?= $this->party->Presettings()->IsRepeat() ? 'WIEDERHOLEN' : 'WIEDERHOLEN Nicht' ?>.
			</p>

			<?= $this->template('party-links') ?>
		</div>
		<div class="col-12 col-lg-6 p-0 ps-lg-3">
			<h3>Ereignisse</h3>

			<?= $this->template('report', $party) ?>

			<?= $this->template('header/messages-button') ?>
		</div>
		<?= $this->template('announcements') ?>
		<?= $this->template('hostilities') ?>
	</div>
</div>

<h3 id="statistics" title="Taste: S">Statistik</h3>

<?= $this->template('statistics/table') ?>
