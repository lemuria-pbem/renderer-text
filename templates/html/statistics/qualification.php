<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party         = $this->party;
$qualification = $this->variables[0];
$items         = $this->variables[1];
$eCount        = count($qualification);
$last          = $eCount - 1;
$rCount        = $eCount / $items;
$rowClass      = 'td-' . 9 * $items;
$h             = 0;
$e             = 0;

?>
<h5>Talentstatistik (Stufe, Anzahl, Prognose)</h5>
<table class="statistics table table-sm table-striped table-bordered"">
	<tbody>
		<?php for ($r = 0; $r < $rCount; $r++): ?>
			<?php $n = min(($r + 1) * $items, $eCount) ?>
			<tr>
				<?php for (; $h < $n; $h++): ?>
					<?php $c = $h === $last ? ($items - $h % $items) * 9 : 9 ?>
					<th scope="col" colspan="<?= $c ?>"><?= $this->get('talent.' . $qualification[$h]->class) ?></th>
				<?php endfor ?>
			</tr>
			<tr class="<?= $rowClass ?>">
				<?php for (; $e < $n; $e++): ?>
					<?php for ($i = 0; $i < 3; $i++): ?>
						<?php if ($qualification[$e]->prognosis[$i]): ?>
							<td><?= ($i >= 2 ? '≤ ' : '') . $qualification[$e]->level[$i] ?>: <?= $qualification[$e]->prognosis[$i]->value ?></td>
							<td class="more-is-good <?= $qualification[$e]->prognosis[$i]->movement ?>"><?= $qualification[$e]->prognosis[$i]->change ?></td>
							<td><?= $qualification[$e]->prognosis[$i]->prognosis ?></td>
						<?php else: ?>
							<td colspan="3"></td>
						<?php endif ?>
					<?php endfor ?>
				<?php endfor ?>
			</tr>
		<?php endfor ?>
	</tbody>
</table>
