<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Landscape\Lake;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region     = $this->variables[0];
$map        = $this->map;
$landscape  = $region->Landscape();
$name       = $region->Name();
$neighbours = $this->neighbours($region);

?>
<p>
	<?php if ($landscape instanceof Navigable): ?>
		<?php if ($landscape instanceof Ocean && $name === 'Ozean' || $landscape instanceof Lake && $name === 'See'): ?>
			Vom Leuchtturm gesehen.
			<br>
		<?php else: ?>
			<?= $this->translate($landscape) ?>, vom Leuchtturm gesehen.
			<br>
		<?php endif ?>
	<?php else: ?>
		<?= $this->translate($landscape) ?>, vom Leuchtturm gesehen.
		<br>
	<?php endif ?>
	<?= ucfirst(implode(', ', $neighbours)) ?>.
	<br>
	<?= $this->template('description', $region) ?>
</p>
