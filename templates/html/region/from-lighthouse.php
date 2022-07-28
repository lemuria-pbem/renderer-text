<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region     = $this->variables[0];
$map        = $this->map;
$landscape  = $region->Landscape();
$neighbours = $this->neighbours($region);

?>
<p>
	<?php if ($landscape instanceof Ocean): ?>
		<?php if ($region->Name() === 'Ozean'): ?>
			Vom Leuchtturm gesehen.
			<br>
		<?php else: ?>
			<?= $this->get('landscape', $region->Landscape()) ?>, vom Leuchtturm gesehen.
			<br>
		<?php endif ?>
	<?php else: ?>
		<?= $this->get('landscape', $region->Landscape()) ?>, vom Leuchtturm gesehen.
		<br>
	<?php endif ?>
	<?= ucfirst(implode(', ', $neighbours)) ?>.
	<br>
	<?= $this->template('description', $region) ?>
</p>
