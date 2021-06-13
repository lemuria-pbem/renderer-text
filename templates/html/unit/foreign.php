<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Factory\Model\Observables;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit         = $this->variables[0];
$census       = $this->census;
$foreign      = $census->getParty($unit);
$intelligence = new Intelligence($unit->Region());
$isGuarding   = false;
foreach ($intelligence->getGuards() as $guard /* @var Unit $guard */):
	if ($guard->Party() === $this->party):
		$isGuarding = true;
		break;
	endif;
endforeach;
$resources = [];
if ($isGuarding):
	foreach (new Observables($unit->Inventory()) as $quantity /* @var Quantity $quantity */):
		$resources[] = $this->number($quantity->Count(), 'observable', $quantity->Commodity());
	endforeach;
	$n = count($resources);
	if ($n > 1):
		$resources[$n - 2] .= ' und ' . $resources[$n - 1];
		unset($resources[$n - 1]);
	endif;
endif;

?>
<h6>
	<?= $unit->Name() ?> <span class="badge badge-primary"><?= $unit->Id() ?></span>
	<?php if ($foreign): ?>
		von <?= $foreign->Name() ?> <span class="badge badge-secondary"><?= $foreign->Id() ?></span>
	<?php else: ?>
		(unbekannte Partei)
	<?php endif ?>
</h6>
<p>
	<?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
	<?= $unit->Description() ?>
</p>
<?php if (count($resources) > 0): ?>
	<p>Reist mit <?= implode(', ', $resources) ?>.</p>
<?php endif ?>
