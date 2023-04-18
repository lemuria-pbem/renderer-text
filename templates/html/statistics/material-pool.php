<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party    = $this->party;
$pool     = $this->variables[0];
$items    = $this->variables[1];
$pCount   = count($pool);
$last     = $pCount - 1;
$rCount   = $pCount / $items;
$rowClass = 'td-' . 2 * $items;
$h        = 0;
$p        = 0;

?>
<table class="table">
	<caption>Materialpool</caption>
	<?php for ($r = 0; $r < $rCount; $r++): ?>
		<?php $n = min(($r + 1) * $items, $pCount) ?>
		<tr>
			<?php for (; $h < $n; $h++): ?>
				<?php $c = $h === $last ? ($items - $h % $items) * 2 : 2 ?>
				<th scope="col" colspan="<?= $c ?>"><?= $this->translate($pool[$h]->class) ?></th>
			<?php endfor ?>
		</tr>
		<tr class="<?= $rowClass ?>">
			<?php for (; $p < $n; $p++): ?>
				<td><?= $pool[$p]->value ?></td>
				<?php if ($p === $last && ($c = ($items - $p % $items) * 2 - 1) > 1): ?>
					<td class="more-is-good <?= $pool[$p]->movement ?>" colspan="<?= $c ?>"><?= $pool[$p]->change ?></td>
				<?php else: ?>
					<td class="more-is-good <?= $pool[$p]->movement ?>"><?= $pool[$p]->change ?></td>
				<?php endif ?>
			<?php endfor ?>
		</tr>
	<?php endfor ?>
</table>
