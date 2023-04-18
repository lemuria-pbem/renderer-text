<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Unit $unit */
$unit   = $this->variables[0];
$prefix = $unit->Construction() || $unit->Vessel() ? '   * ' : '  -- ';

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
<?= $prefix . $unit ?>, <?= $this->number($unit->Size(), $unit->Race()) ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

<?php if (!empty($inventory)): ?>
Hat <?= implode(', ', $inventory) ?>.
<?php endif ?>
