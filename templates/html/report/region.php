<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$entity = $this->variables[0];
$class  = strtolower(getClass($entity));
$report = $this->messages($entity);

?>
<?php if ($report): ?>
	<ul class="<?= $class ?> report">
	<?php foreach ($report as $message): ?>
		<li><?= $this->messageWithSection($message) ?></li>
	<?php endforeach ?>
	</ul>
<?php endif ?>
