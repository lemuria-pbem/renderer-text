<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit = $this->variables[0];

$inventory = [];
foreach ($unit->Inventory() as $quantity):
	$inventory[] = $this->quantity($quantity, $unit);
endforeach;
$n = count($inventory);
if ($n > 1):
	$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
	unset($inventory[$n - 1]);
endif;

?>
<h6>
	<?= $unit->Name() ?> <span class="badge text-bg-danger font-monospace"><?= $unit->Id() ?></span>
</h6>
<p>
	<?= $this->number($unit->Size(), $unit->Race()) ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
	<?= $this->template('description', $unit) ?>
	<?php if (!empty($inventory)): ?>
		<br>
		Hat <?= implode(', ', $inventory) ?>.
	<?php endif ?>
</p>
