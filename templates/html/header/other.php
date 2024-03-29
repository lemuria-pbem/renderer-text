<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\linkEmail;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party  = $this->party;
$census = $this->census;
$count  = $census->count();
$banner = $party->Banner() ? 'Parteibanner: ' . linkEmail($party->Banner()) : '(kein Banner gesetzt)';

?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-lg-6 p-0 pe-lg-3">
			<h2><?= $party->Name() ?> <span class="badge text-bg-primary font-monospace"><?= $party->Id() ?></span></h2>

			<blockquote class="blockquote"><?= $party->Description() ?></blockquote>

			<p><?= $banner ?></p>

			<p>Die Partei besteht aus insgesamt <?= $this->number($count) ?> Individuen in <?= $this->number($party->People()->count()) ?> Einheiten.</p>

			<?= $this->template('hostilities', $party) ?>
		</div>
		<div class="col-12 col-lg-6 p-0 ps-lg-3">
			<h3>Ereignisse</h3>

			<?= $this->template('report/default', $party) ?>

			<?= $this->template('header/messages-button') ?>
		</div>
	</div>
</div>

<h3 id="statistics" title="Taste: S">Statistik</h3>

<?= $this->template('statistics/other') ?>
