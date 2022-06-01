<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$announcements = $this->announcements();

?>
<?php if ($announcements): ?>
	<div class="col-12 p-0">
		<h3>Botschaften</h3>

		<ul class="report">
			<?php foreach ($announcements as $message): ?>
				<li><?= $this->message($message) ?></li>
			<?php endforeach ?>
		</ul>
	</div>
<?php endif ?>
