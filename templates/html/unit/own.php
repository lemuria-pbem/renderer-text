<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Factory\Model\Orders;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Market\Sales;
use Lemuria\Model\Fantasya\Market\Trade;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit = $this->variables[0];
/** @var Sales|null $sales */
$sales     = $this->variables[1];
$merchant  = 'merchant-' . $unit->Id();
$party     = $this->party;
$census    = $this->census;
$aura      = $unit->Aura();
$disguised = $unit->Disguise();
$calculus  = new Calculus($unit);
$hitpoints = $calculus->hitpoints();
$health    = (int)floor($unit->Health() * $hitpoints);
$mark      = $this->healthMark($unit);
$payload   = 0;
$orders    = new Orders($unit);

$talents    = [];
$statistics = $this->talentStatistics(Subject::Talents, $unit);
foreach ($unit->Knowledge() as $ability /* @var Ability $ability */):
	$experience = $ability->Experience();
	$talent     = $ability->Talent();
	$ability    = $calculus->knowledge($talent);
	$knowledge  = '<span>' . $this->get('talent', $talent) . '&nbsp;' . $ability->Level() . '</span>';
	$change     = $statistics[getClass($talent)] ?? 0;
	if ($change > 0) {
		$knowledge .= '<span class="badge text-bg-inverse badge-success">+' . $change . '</span>';
	} elseif ($change < 0) {
		$knowledge .= '<span class="badge text-bg-inverse badge-danger">' . $change . '</span>';
	}
	$knowledge .= '&nbsp;<span>(' . $this->number($experience) . ')</span>';
	$talents[]  = $knowledge;
endforeach;

$inventory = [];
foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */):
	$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
	$payload    += $quantity->Weight();
endforeach;
$n = count($inventory);
if ($n > 1):
	$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
	unset($inventory[$n - 1]);
endif;

$treasury = $unit->Treasury();
foreach ($treasury as $unicum /* @var Unicum $unicum */):
	$payload += $unicum->Composition()->Weight();
endforeach;

$weight = (int)ceil($payload / 100);
$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);

$spells       = [];
$battleSpells = $unit->BattleSpells();
if ($battleSpells):
	$preparation = $battleSpells->Preparation();
	if ($preparation):
		$spells[] = $this->get('spell', $preparation->Spell()) . ' (' . $preparation->Level() . ')';
	endif;
	$combat = $battleSpells->Combat();
	if ($combat):
		$spells[] = $this->get('spell', $combat->Spell()) . ' (' . $combat->Level() . ')';
	endif;
endif;

$trades     = [];
$impossible = [];
$forbidden  = [];
$allTrades  = $unit->Trades();
foreach ($allTrades as $trade /* @var Trade $trade */) {
	$id = $trade->Id()->Id();
	if ($sales) {
		match ($sales->getStatus($trade)) {
			Sales::FORBIDDEN     => $forbidden[$id] = $trade,
			Sales::UNSATISFIABLE => $impossible[$id] = $trade,
			default              => $trades[$id] = $trade
		};
	} else {
		if ($trade->IsSatisfiable()) {
			$trades[$id] = $trade;
		} else {
			$impossible[$id] = $trade;
		}
	}
}

?>
<h6>
	<?= $unit->Name() ?> <span class="badge text-bg-primary"><?= $unit->Id() ?></span>
	<?php if ($mark): ?>
		<span class="badge text-bg-danger"><?= $mark ?></span>
	<?php endif ?>
</h6>
<p>
	<?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($aura): ?>, Aura <?= $aura->Aura()?>/<?= $aura->Maximum() ?><?php endif ?>, <?= $this->battleRow($unit) ?>,
	<?= $this->health($unit) ?> (<?= $health ?>/<?= $hitpoints ?>)<?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?><?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?><?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?><?php if (!$unit->IsLooting()): ?>, sammelt nicht<?php endif ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
	<?= $this->template('description', $unit) ?>
	<br>
	Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>.
	<br>
	<?php if (!$treasury->isEmpty()): ?>
		<?= $this->template('treasury/unit', $treasury) ?>
		<br>
	<?php endif ?>
	Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>,
	Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
	<?php if (!empty($spells)): ?>
		<br>
		Eingesetzte Kampfzauber: <?= implode(', ', $spells) ?>.
	<?php endif ?>
</p>
<?php if ($sales && $allTrades->count() > 0): ?>
	<div class="market">
		<p class="h7">
			<a data-bs-toggle="collapse" href="#<?= $merchant ?>" role="button" aria-expanded="true" aria-controls="market">Aktuelle Marktangebote</a>
		</p>
		<ol class="collapse" id="<?= $merchant ?>">
			<?php foreach ($trades as $trade): ?>
				<li>
					<?= $this->template('trade/own', $trade, Sales::AVAILABLE) ?>
				</li>
			<?php endforeach ?>
			<?php foreach ($impossible as $trade): ?>
				<li>
					<?= $this->template('trade/own', $trade, Sales::UNSATISFIABLE) ?>
				</li>
			<?php endforeach ?>
			<?php foreach ($forbidden as $trade): ?>
				<li>
					<?= $this->template('trade/own', $trade, Sales::FORBIDDEN) ?>
				</li>
			<?php endforeach ?>
		</ol>
	</div>
<?php elseif ($allTrades->count() > 0): ?>
	<div class="market">
		<p class="h7">
			<a data-bs-toggle="collapse" href="#<?= $merchant ?>" role="button" aria-expanded="true" aria-controls="market">Angebote für den Markthandel</a>
		</p>
		<ol class="collapse" id="<?= $merchant ?>">
			<?php foreach ($trades as $trade): ?>
				<li>
					<?= $this->template('trade/own', $trade, Sales::AVAILABLE) ?>
				</li>
			<?php endforeach ?>
			<?php foreach ($impossible as $trade): ?>
				<li>
					<?= $this->template('trade/own', $trade, Sales::UNSATISFIABLE) ?>
				</li>
			<?php endforeach ?>
		</ol>
	</div>
<?php elseif ($sales): ?>
	<div class="market">
		<p class="h7">
			<a data-bs-toggle="collapse" href="#<?= $merchant ?>" role="button" aria-expanded="true" aria-controls="market">Aktuelle Marktangebote</a>
		</p>
		<p>Wir haben aktuell nichts anzubieten.</p>
	</div>
<?php endif ?>
<?php if (!empty($orders->comments)): ?>
	<p class="h7">Notizen:</p>
	<blockquote class="blockquote">
		<ol>
			<?php foreach ($orders->comments as $line): ?>
				<li>
					<q><?= $line ?></q>
				</li>
			<?php endforeach ?>
		</ol>
	</blockquote>
<?php endif ?>
<?php if (!empty($orders->orders)): ?>
	<p class="h8">
		<a data-bs-toggle="collapse" href="#orders-<?= $unit->Id()->Id() ?>" role="button" aria-expanded="false" aria-controls="orders-<?= $unit->Id()->Id() ?>">Befehle</a>
	</p>
	<ol id="orders-<?= $unit->Id()->Id() ?>" class="small collapse mb-2">
		<?php foreach ($orders->orders as $order): ?>
			<li><?= $order ?></li>
		<?php endforeach ?>
	</ol>
<?php endif ?>
<?php if (count($this->messages($unit))): ?>
	<?= $this->template('report', $unit) ?>
<?php endif ?>
