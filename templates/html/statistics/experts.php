<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party    = $this->party;
$experts  = $this->variables[0];
$items    = $this->variables[1];
$eCount   = count($experts);
$last     = $eCount - 1;
$rCount   = $eCount / $items;
$rowClass = 'td-' . 2 * $items;
$h        = 0;
$e        = 0;

?>
<table class="table">
	<caption>Talent-Experten (Prognose)</caption>
	<?php for ($r = 0; $r < $rCount; $r++): ?>
		<?php $n = min(($r + 1) * $items, $eCount) ?>
		<tr>
			<?php for (; $h < $n; $h++): ?>
				<?php $c = $h === $last ? ($items - $h % $items) * 2 : 2 ?>
				<th scope="col" colspan="<?= $c ?>"><?= $this->get('talent.' . $experts[$h]->class) ?></th>
			<?php endfor ?>
		</tr>
		<tr class="<?= $rowClass ?>">
			<?php for (; $e < $n; $e++): ?>
				<td><?= $experts[$e]->value ?></td>
				<?php if ($e === $last && ($c = ($items - $e % $items) * 2 - 1) > 1): ?>
					<td class="more-is-good <?= $experts[$e]->movement ?>" colspan="<?= $c ?>"><?= $experts[$e]->change ?> (<?= $experts[$e]->prognosis ?>)</td>
				<?php else: ?>
					<td class="more-is-good <?= $experts[$e]->movement ?>"><?= $experts[$e]->change ?> (<?= $experts[$e]->prognosis ?>)</td>
				<?php endif ?>
			<?php endfor ?>
		</tr>
	<?php endfor ?>
</table>
