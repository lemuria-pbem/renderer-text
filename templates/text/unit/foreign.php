<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\description;
use Lemuria\Engine\Fantasya\Factory\Model\Observables;
use Lemuria\Engine\Fantasya\Factory\Model\Trades;
use Lemuria\Engine\Fantasya\Message\Casus;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Unit $unit */
$unit    = $this->variables[0];
/** @var Trades|null $trades */
$trades  = $this->variables[1] ?? null;
$census  = $this->census;
$prefix  = $unit->Construction() || $unit->Vessel() ? '   * ' : '  -- ';
$foreign = $census->getParty($unit);
if (!$foreign):
	$foreign = 'unbekannte Partei';
endif;
$intelligence = new Intelligence($unit->Region());
$isGuarding   = false;
$unitIsGuard  = $unit->IsGuarding();
foreach ($intelligence->getGuards() as $guard):
	if ($guard->Party() === $this->party):
		$isGuarding = true;
		break;
	endif;
endforeach;
$resources = [];
if ($isGuarding || $unitIsGuard):
	$casus = $unitIsGuard ? Casus::Accusative : Casus::Dative;
	foreach (new Observables($unit->Inventory()) as $quantity):
		$resources[] = $this->number($quantity->Count(), $quantity->Commodity(), $casus);
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
<?php if ($unitIsGuard): ?>
Besitzt <?= implode(', ', $resources) ?>.
<?php else: ?>
Reist mit <?= implode(', ', $resources) ?>.
<?php endif ?>
<?php endif ?>
<?php if ($trades && $trades->HasMarket()): ?>

<?= center('Marktangebote') ?>

<?php if (count($trades->Available()) > 0): ?>
<?php foreach ($trades->Available() as $trade): ?>
<?= $this->template('trade/foreign', $trade) ?>

<?php endforeach ?>
<?php else: ?>
Dieser Händler hat gerade nichts anzubieten.
<?php endif ?>
<?php endif ?>
