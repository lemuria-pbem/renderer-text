<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use Lemuria\Engine\Fantasya\Factory\Model\Observables;
use Lemuria\Engine\Fantasya\Message\Casus;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Unit $unit */
$unit    = $this->variables[0];
$census  = $this->census;
$prefix  = $unit->Construction() || $unit->Vessel() ? '   * ' : '  -- ';
$foreign = $census->getParty($unit);
if (!$foreign):
	$foreign = 'unbekannte Partei';
endif;
$intelligence = new Intelligence($unit->Region());
$isGuarding   = false;
foreach ($intelligence->getGuards() as $guard):
	if ($guard->Party() === $this->party):
		$isGuarding = true;
		break;
	endif;
endforeach;
$resources = [];
if ($isGuarding):
	foreach (new Observables($unit->Inventory()) as $quantity):
		$resources[] = $this->number($quantity->Count(), $quantity->Commodity(), Casus::Dative);
	endforeach;
	$n = count($resources);
	if ($n > 1):
		$resources[$n - 2] .= ' und ' . $resources[$n - 1];
		unset($resources[$n - 1]);
	endif;
endif;

?>
<?= $prefix . $unit ?> von <?= $foreign ?>, <?= $this->number($unit->Size(), $unit->Race()) ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

<?php if (count($resources) > 0): ?>
Reist mit <?= implode(', ', $resources) ?>.
<?php endif ?>
Die Einheit ist mit dem Bau des Schiffes beschäftigt.
