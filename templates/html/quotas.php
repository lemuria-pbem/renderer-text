<?php
declare(strict_types = 1);

use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region = $this->variables[0];
$quotas = $this->quotas($region);

?>
<?php if (!empty($quotas)): ?>
	<h5>Grenzen</h5>

	<ul>
		<?php foreach ($quotas as $item => $threshold): ?>
			<li><?= $item ?>: <?= $threshold ?></li>
		<?php endforeach ?>
	</ul>
<?php endif ?>
