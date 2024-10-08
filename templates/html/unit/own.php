<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\id;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Factory\Model\Deals;
use Lemuria\Engine\Fantasya\Factory\Model\Trades;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Model\Fantasya\Extension\Valuables;
use Lemuria\Model\Fantasya\Market\Sales;
use Lemuria\Model\Fantasya\Transport;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\Model\Orders;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Unit $unit */
$unit = $this->variables[0];
/** @var Trades|null $trades */
$trades    = $this->variables[1];
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
$transport = 0;
$orders    = new Orders($unit);

$talents    = [];
$statistics = $this->talentStatistics(Subject::Talents, $unit);
foreach ($unit->Knowledge() as $ability):
	$experience = $ability->Experience();
	$talent     = $ability->Talent();
	$ability    = $calculus->knowledge($talent);
	$level      = $ability->Level();
	$knowledge  = '<span>' . $this->get('talent', $talent) . '&nbsp;' . $level . '</span>';
	$change     = $statistics[getClass($talent)] ?? 0;
	if ($change > 0) {
		$knowledge .= '<span class="badge badge-inverse badge-success">+' . $change . '</span>';
	} elseif ($change < 0) {
		$knowledge .= '<span class="badge badge-inverse badge-danger">' . $change . '</span>';
	}
	$rawLevel = $calculus->ability($talent)->Level();
	if ($level === $rawLevel) {
		$knowledge .= '&nbsp;<span>(' . $this->number($experience) . ')</span>';
	} else {
		$knowledge .= '&nbsp;<span>(' . $rawLevel . '/' . $this->number($experience) . ')</span>';
	}
	$talents[] = $knowledge;
endforeach;

$inventory = [];
foreach ($this->inventory($unit) as $quantity):
	$commodity   = $quantity->Commodity();
	$inventory[] = $this->number($quantity->Count(), $commodity);
	if ($commodity instanceof Transport):
		$transport += $quantity->Weight();
	else:
		$payload += $quantity->Weight();
	endif;
endforeach;
$n = count($inventory);
if ($n > 1):
	$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
	unset($inventory[$n - 1]);
endif;

$treasury = $unit->Treasury();
foreach ($treasury as $unicum):
	$payload += $unicum->Composition()->Weight();
endforeach;

$weight  = (int)ceil($payload / 100);
$total   = (int)ceil(($payload + $transport + $unit->Size() * $unit->Race()->Weight()) / 100);
$payload = $calculus->payload() / 100;

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

$deals = null;
if (!$trades):
	$deals = new Deals($unit);
endif;
/** @var Valuables|null $valuables */
$valuables = $unit->Extensions()?->offsetExists(Valuables::class) ? $unit->Extensions()->offsetGet(Valuables::class) : null;

?>
<h6>
	<?= $unit->Name() ?> <span class="badge text-bg-primary font-monospace"><?= $unit->Id() ?></span>
	<?php if ($mark): ?>
		<span class="badge text-bg-danger"><?= $mark ?></span>
	<?php endif ?>
</h6>
<p>
	<?= $this->number($unit->Size(), $unit->Race()) ?><?php if ($aura): ?>, Aura <?= $aura->Aura()?>/<?= $aura->Maximum() ?><?php endif ?>, <?= $this->battleRow($unit) ?>,
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
	Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE, Tragkraft <?= $this->number($payload) ?> GE.
	<?php if (!empty($spells)): ?>
		<br>
		Eingesetzte Kampfzauber: <?= implode(', ', $spells) ?>.
	<?php endif ?>
</p>
<?php if ($trades && $trades->HasMarket() && ($trades->count() || $valuables?->count())): ?>
	<div class="market">
		<p class="h7">
			<a data-bs-toggle="collapse" href="#<?= $merchant ?>" role="button" aria-expanded="true" aria-controls="market">Aktuelle Marktangebote</a>
		</p>
		<ol class="collapse" id="<?= $merchant ?>">
			<?php foreach ($trades->sort()->Available() as $trade): ?>
				<li>
					<?= $this->template('trade/own', $trade, Sales::AVAILABLE) ?>
				</li>
			<?php endforeach ?>
			<?php foreach ($trades->Impossible() as $trade): ?>
				<li>
					<?= $this->template('trade/own', $trade, Sales::UNSATISFIABLE) ?>
				</li>
			<?php endforeach ?>
			<?php foreach ($trades->Forbidden() as $trade): ?>
				<li>
					<?= $this->template('trade/own', $trade, Sales::FORBIDDEN) ?>
				</li>
			<?php endforeach ?>
			<?php if ($valuables): ?>
				<?php foreach ($valuables as $unicum): ?>
					<li>
						<?= $this->template('valuable/own', $unicum, $valuables->getPrice($unicum)) ?>
					</li>
				<?php endforeach ?>
			<?php endif ?>
		</ol>
	</div>
<?php elseif ($trades && ($trades->count() || $valuables?->count())): ?>
	<div class="market">
		<p class="h7">
			<a data-bs-toggle="collapse" href="#<?= $merchant ?>" role="button" aria-expanded="true" aria-controls="market">Angebote für den Markthandel</a>
		</p>
		<ol class="collapse" id="<?= $merchant ?>">
			<?php foreach ($trades->sort()->Available() as $trade): ?>
				<li>
					<?= $this->template('trade/own', $trade, Sales::AVAILABLE) ?>
				</li>
			<?php endforeach ?>
			<?php foreach ($trades->Impossible() as $trade): ?>
				<li>
					<?= $this->template('trade/own', $trade, Sales::UNSATISFIABLE) ?>
				</li>
			<?php endforeach ?>
			<?php if ($valuables): ?>
				<?php foreach ($valuables as $unicum): ?>
					<li>
						<?= $this->template('valuable/own', $unicum, $valuables->getPrice($unicum)) ?>
					</li>
				<?php endforeach ?>
			<?php endif ?>
		</ol>
	</div>
<?php elseif ($trades && $trades->HasMarket() && !$valuables?->count()): ?>
	<div class="market">
		<p class="h7">
			<a data-bs-toggle="collapse" href="#<?= $merchant ?>" role="button" aria-expanded="true" aria-controls="market">Aktuelle Marktangebote</a>
		</p>
		<p>Wir haben aktuell nichts anzubieten.</p>
	</div>
<?php elseif ($deals?->count() || $valuables?->count()): ?>
<div class="market">
	<p class="h7">
		<a data-bs-toggle="collapse" href="#<?= $merchant ?>" role="button" aria-expanded="true" aria-controls="market">Handelsangebote</a>
	</p>
	<ol class="collapse" id="<?= $merchant ?>">
		<?php foreach ($deals->sort()->Trades() as $trade): ?>
			<li>
				<?= $this->template('trade/own', $trade, Sales::AVAILABLE) ?>
			</li>
		<?php endforeach ?>
		<?php foreach ($deals->Unsatisfiable() as $trade): ?>
			<li>
				<?= $this->template('trade/own', $trade, Sales::UNSATISFIABLE) ?>
			</li>
		<?php endforeach ?>
		<?php if ($valuables): ?>
			<?php foreach ($valuables as $unicum): ?>
				<li>
					<?= $this->template('valuable/own', $unicum, $valuables->getPrice($unicum)) ?>
				</li>
			<?php endforeach ?>
		<?php endif ?>
	</ol>
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
		<a data-bs-toggle="collapse" href="#<?= id($unit, 'orders') ?>" role="button" aria-expanded="false" aria-controls="orders-<?= $unit->Id()->Id() ?>">Befehle</a>
	</p>
	<ol id="<?= id($unit, 'orders') ?>" class="small collapse mb-2">
		<?php foreach ($orders->orders as $order): ?>
			<li><?= $order ?></li>
		<?php endforeach ?>
		<?php foreach ($orders->acts as $act): ?>
			<li><?= $act ?></li>
		<?php endforeach ?>
	</ol>
<?php endif ?>
<?php if (count($this->messages($unit))): ?>
	<?= $this->template('report/with-filter', $unit) ?>
<?php endif ?>
