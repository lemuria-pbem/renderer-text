<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$atlas = $this->atlas;
$map   = $this->map;

?>
<div style="position: relative; width: 600px; height: 600px;">
	<?php foreach ($atlas as $region /* @var Region $region */): ?>
		<?php $pos = $map->getCoordinates($region) ?>
		<img
			src="/img/<?= strtolower((string)$region->Landscape()) ?>.png"
			title="<?= $pos . ' ' . $region ?>"
			alt="<?= $this->get('landscape', $region->Landscape()) ?>"
			style="position: absolute; left: <?= $pos->X() * 64 + $pos->Y() * 32 + 300 ?>px; top: <?= 300 - $pos->Y() * 48 ?>px;"
		/>
	<?php endforeach ?>
</div>
