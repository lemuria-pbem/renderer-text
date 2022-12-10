<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Factory\Model\Observables;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Market\Sales;
use Lemuria\Model\Fantasya\Market\Trade;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit = $this->variables[0];
/** @var Sales|null $sales */
$sales        = $this->variables[1];
$merchant     = $sales ? 'merchant-' . $unit->Id() : null;
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

$trades = [];
if ($sales) {
	foreach ($unit->Trades()->sort() as $trade/* @var Trade $trade */) {
		if ($sales->getStatus($trade) === Sales::AVAILABLE) {
			$trades[$trade->Id()->Id()] = $trade;
		}
	}
}

?>
<h6>
	<?= $unit->Name() ?> <span class="badge text-bg-primary"><?= $unit->Id() ?></span>
	<?php if ($foreign): ?>
		von <?= $foreign->Name() ?> <span class="badge text-bg-secondary"><?= $foreign->Id() ?></span>
	<?php else: ?>
		(unbekannte Partei)
	<?php endif ?>
</h6>
<p>
	<?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
	<?= $this->template('description', $unit) ?>
</p>
<?php if (count($resources) > 0): ?>
	<p>Reist mit <?= implode(', ', $resources) ?>.</p>
<?php endif ?>
<?php if ($sales): ?>
	<div class="market">
		<p class="h7">
			<a data-bs-toggle="collapse" href="#<?= $merchant ?>" role="button" aria-expanded="true" aria-controls="market">Marktangebote</a>
		</p>
		<?php if (count($trades) > 0): ?>
		<ol class="collapse" id="<?= $merchant ?>">
			<?php foreach ($trades as $trade): ?>
				<li class="active">
					<?= $this->template('trade/foreign', $trade) ?>
				</li>
			<?php endforeach ?>
		</ol>
		<?php else: ?>
			<p>Dieser Händler hat gerade nichts anzubieten.</p>
		<?php endif ?>
	</div>
<?php endif ?>
