<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Renderer\Text\Model\HtmlMap;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$map = new HtmlMap($this);

?>
<div class="modal" id="modal-map" tabindex="-1" aria-labelledby="modal-map-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 id="modal-map-label" class="modal-title fs-5">Weltkarte</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
			</div>
			<div class="modal-body <?= $map->mapClass() ?>" <?= $map->offset() ?>>
				<?php foreach ($map as $region => $tile/* @var HtmlMap $tile */): ?>
					<a href="#<?= id($region) ?>">
						<div id="<?= $tile->id() ?>" class="<?= $tile->landscape() ?>" title="<?= $tile->title() ?>" <?= $tile->location() ?> <?= $tile->coordinates() ?>><?= $tile->name() ?></div>
					</a>
				<?php endforeach ?>
			</div>
		</div>
	</div>
</div>
