<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$announcements = $this->announcements();

?>
<?php if ($announcements): ?>
	<h3 id="announcements" title="Taste: N">Nachrichten, Gerüchte und Botschaften</h3>

	<?php foreach ($announcements as $announcement): ?>
		<div class="announcement">
			<?php if ($announcement->From()): ?>
				Von: <a href="#unit-<?= $announcement->From() ?>"><?= $announcement->Sender() ?></a>&nbsp;<span class="badge text-bg-primary font-monospace"><a href="#unit-<?= $announcement->From() ?>"><?= $announcement->From() ?></a></span>&nbsp;·&nbsp;An: <a href="<?= $announcement->LinkAnchor() . $announcement->To() ?>"><?= $announcement->Recipient() ?></a>&nbsp;<span class="badge text-bg-primary font-monospace"><a href="<?= $announcement->LinkAnchor() . $announcement->To() ?>"><?= $announcement->To() ?></a></span>
			<?php else: ?>
				Von: <strong><?= $announcement->Sender() ?></strong>&nbsp;·&nbsp;An: <a href="<?= $announcement->LinkAnchor() . $announcement->To() ?>"><?= $announcement->Recipient() ?></a>&nbsp;<span class="badge text-bg-primary font-monospace"><a href="<?= $announcement->LinkAnchor() . $announcement->To() ?>"><?= $announcement->To() ?></a></span>
			<?php endif ?>
			<blockquote>„<?= $announcement->Message() ?>“</blockquote>
		</div>
	<?php endforeach ?>
<?php endif ?>
