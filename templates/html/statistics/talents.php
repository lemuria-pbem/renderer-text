<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use function Lemuria\number;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\Statistics\Talent\Talents;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party      = $this->party;
$statistics = new Talents($party);

$talents = [];
$headers = [];
$class   = [];
$modal   = [];
foreach ($statistics->Talents() as $name => $talent) {
	$rows   = ['Region'];
	$matrix = $statistics->getMatrix($talent);
	$atlas  = $matrix->Atlas();
	$max    = $matrix->Maximum();
	for ($i = 0; $i <= $max; $i++) {
		$rows[] = 'T' . $i;
	}
	$headers[$name] = $rows;
	$class[$name]   = strtolower(getClass($talent));

	$lines = [];
	foreach ($matrix->Regions() as $id => $levels) {
		/** @var Region $region */
		$region = $atlas[$id];
		$line   = ['<a href="" data-region="' . $region->Id() . '" data-bs-dismiss="modal">' . $region->Name() . '</a>'];
		foreach ($levels as $value) {
			$line[] = $value->count > 0 ? '<a href="" data-unit="' . $value->unit->Id() . '" data-bs-dismiss="modal">' . number($value->count) . '</a>' : '';
		}
		$lines[] = $line;
	}
	$talents[$name] = $lines;

	$modal[$name] = match (true) {
		$max >= 12 => 'modal modal-xl',
		$max >= 7  => 'modal modal-lg',
		default    => 'modal'
	};
}

?>
<?php foreach ($talents as $name => $rows): ?>
	<div class="talent-statistics <?= $modal[$name] ?> fade" id="talent-statistics-<?= $class[$name] ?>" tabindex="-1" aria-labelledby="talent-statistics-label-<?= $name ?>" aria-hidden="true">
		<div class="modal-dialog modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="talent-statistics-label-<?= $class[$name] ?>">Talentübersicht: <?= $name ?></h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<table class="table table-striped">
						<thead>
							<tr>
								<?php foreach ($headers[$name] as $column): ?>
									<th>
										<?= $column ?>
									</th>
								<?php endforeach ?>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($rows as $row): ?>
								<tr>
									<?php foreach ($row as $c => $column): ?>
										<?php if ($c): ?>
											<td><?= $column ?></td>
										<?php else: ?>
											<th><?= $column ?></th>
										<?php endif ?>
									<?php endforeach ?>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php endforeach ?>
