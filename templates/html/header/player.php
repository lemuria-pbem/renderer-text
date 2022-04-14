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
		<div class="col-12 col-lg-6 pl-0">
			<h2><?= $party->Name() ?> <span class="badge badge-primary"><?= $party->Id() ?></span></h2>

			<blockquote class="blockquote"><?= $party->Description() ?></blockquote>

			<p><?= $banner ?></p>

			<p>
				Dein Volk zählt <?= $this->number($census->count(), 'race', $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.<br>
				Deine Einheiten sammeln <?= $this->loot() ?>.<br>
				Vorgaben für neue Einheiten: <?= implode(', ', $this->presettings()) ?>.
			</p>
		</div>
		<div class="col-12 col-lg-6 pr-0">
			<h3>Ereignisse</h3>

			<?= $this->template('report', $party) ?>
			<?= $this->template('hostilities', $party) ?>
		</div>
	</div>
</div>

<?php if ($this->isDevelopment()): ?>
<h3>Statistik</h3>

<?= $this->template('statistics/table') ?>
<?php endif ?>
