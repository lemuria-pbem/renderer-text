<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Factory\Model\Trades;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit = $this->variables[0];
/** @var Trades|null $trades */
$trades    = $this->variables[1];
$merchant  = $trades && $trades->HasMarket() ? 'merchant-' . $unit->Id() : null;
$census    = $this->census;
$foreign   = $census->getParty($unit);
$disguised = $unit->Disguise();
$calculus  = new Calculus($unit);
$talents   = [];
foreach ($unit->Knowledge() as $ability/* @var Ability $ability */):
	$experience = $ability->Experience();
	$ability    = $calculus->knowledge($ability->Talent());
	$talents[]  = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . Ability::getLevel($experience) . '/' . $this->number($experience) . ')';
endforeach;
$inventory = [];
$payload   = 0;
foreach ($unit->Inventory() as $quantity/* @var Quantity $quantity */):
	$inventory[] = $this->number($quantity->Count(), $quantity->Commodity());
	$payload     += $quantity->Weight();
endforeach;
$n = count($inventory);
if ($n > 1):
	$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
	unset($inventory[$n - 1]);
endif;
$weight = (int)ceil($payload / 100);
$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);

?>
<h6>
	<?= $unit->Name() ?> <span class="badge text-bg-primary font-monospace"><?= $unit->Id() ?></span>
	<?php if ($foreign): ?>
		von <?= $foreign->Name() ?> <span class="badge text-bg-secondary font-monospace"><?= $foreign->Id() ?></span>
	<?php else: ?>
		(unbekannte Partei)
	<?php endif ?>
</h6>
<p>
	<?= $this->number($unit->Size(), $unit->Race()) ?><?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?><?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?><?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
	<?= $this->template('description', $unit) ?>
	<br>
	Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>.
	<br>
	Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>,
	Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
</p>
<?php if ($trades && $trades->HasMarket()): ?>
	<div class="market">
		<p class="h7">
			<a data-bs-toggle="collapse" href="#<?= $merchant ?>" role="button" aria-expanded="true" aria-controls="market">Marktangebote</a>
		</p>
		<?php if (count($trades->Available()) > 0): ?>
			<ol class="collapse" id="<?= $merchant ?>">
				<?php foreach ($trades->Available() as $trade): ?>
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
