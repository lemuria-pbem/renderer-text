<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$announcements = $this->announcements();

?>
<?php if ($announcements): ?>
	<div class="col-12 p-0">
		<h3>Botschaften</h3>

		<?php foreach ($announcements as $announcement): ?>
			<div class="announcement">
				<span class="sender">Von: <strong><?= $announcement->Sender() ?></strong></span>&nbsp;·&nbsp;<span class="recipient">An: <strong><?= $announcement->Recipient() ?></strong></span>
				<blockquote>„<?= $announcement->Message() ?>“</blockquote>
			</div>
		<?php endforeach ?>
	</div>
<?php endif ?>
