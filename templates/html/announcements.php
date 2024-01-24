<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$announcements = $this->announcements();

?>
<?php if ($announcements): ?>
	<h3>Nachrichten und Botschaften</h3>

	<?php foreach ($announcements as $announcement): ?>
		<div class="announcement">
			<?php if ($announcement->From()): ?>
				<span class="sender">Von: <strong><?= $announcement->Sender() ?></strong></span>&nbsp;<span class="badge text-bg-primary font-monospace"><?= $announcement->From() ?></span>&nbsp;·&nbsp;<span class="recipient">An: <strong><?= $announcement->Recipient() ?></strong></span>&nbsp;<span class="badge text-bg-primary font-monospace"><?= $announcement->To() ?></span>
			<?php else: ?>
				<span class="sender">Von: <strong><?= $announcement->Sender() ?></strong></span>&nbsp;·&nbsp;<span class="recipient">An: <strong><?= $announcement->Recipient() ?></strong></span>&nbsp;<span class="badge text-bg-primary font-monospace"><?= $announcement->To() ?></span>
			<?php endif ?>
			<blockquote>„<?= $announcement->Message() ?>“</blockquote>
		</div>
	<?php endforeach ?>
<?php endif ?>
