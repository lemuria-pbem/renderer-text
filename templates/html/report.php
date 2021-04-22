<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$report = $this->messages($this->variables[0]);

?>
<?php if ($report): ?>
	<ul class="report">
	<?php foreach ($report as $message): ?>
		<li><?= $this->message($message) ?></li>
	<?php endforeach ?>
	</ul>
<?php endif ?>
