<?php
declare (strict_types = 1);

use Lemuria\Entity;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Entity $entity */
$entity      = $this->variables[0];
$description = $entity->Description();

?>
<?php if ($description): ?>
	<mark>
		<q><?= $entity->Description() ?></q>
	</mark>
<?php endif ?>
