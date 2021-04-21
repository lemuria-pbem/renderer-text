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
$neighbours = [];
foreach ($map->getNeighbours($region)->getAll() as $direction => $neighbour):
	$neighbours[] = 'im ' . $this->get('world', $direction) . ' liegt ' . $this->neighbour($neighbour);
endforeach;
$n = count($neighbours);
if ($n > 1):
	$neighbours[$n - 2] .= ' und ' . $neighbours[$n - 1];
	unset($neighbours[$n - 1]);
endif;

?>
<p>
	<?php if ($landscape instanceof Ocean): ?>
		<?php if ($region->Name() !== 'Ozean'): ?>
			<?= $this->get('landscape', $region->Landscape()) ?>.
			<br>
		<?php endif ?>
	<?php else: ?>
		<?= $this->get('landscape', $region->Landscape()) ?>.
		<br>
	<?php endif ?>
	<?= ucfirst(implode(', ', $neighbours)) ?>.
	<br>
	<?= $region->Description() ?>
</p>
