<?php
declare (strict_types = 1);

use function Lemuria\getClass;

use Lemuria\Lemuria;
use Lemuria\Model\Lemuria\Ability;
use Lemuria\Model\Lemuria\Commodity\Granite;
use Lemuria\Model\Lemuria\Commodity\Ore;
use Lemuria\Model\Lemuria\Commodity\Peasant;
use Lemuria\Model\Lemuria\Commodity\Silver;
use Lemuria\Model\Lemuria\Commodity\Tree;
use Lemuria\Model\Lemuria\Construction;
use Lemuria\Model\Lemuria\Quantity;
use Lemuria\Model\Lemuria\Region;
use Lemuria\Model\Lemuria\Unit;
use Lemuria\Model\Lemuria\Vessel;
use Lemuria\Renderer\Text\Intelligence;
use Lemuria\Renderer\Text\View;

/**
 * A parties' report in HTML.
 */

/* @var View $this */

$party	  = $this->party;
$census   = $this->census;
$world	  = $this->world;
$race	  = getClass($party->Race());
$calendar = Lemuria::Calendar();
$season   = $this->get('calendar.season', $calendar->Season());
$month	  = $this->get('calendar.month', $calendar->Month());
$week	  = $calendar->Week();

?>
<h1 class="text-center">Lemuria-Auswertung</h1>

<p class="text-center">
	für die <?= $week ?>. Woche des Monats <?= $month?> im <?= $season ?> des Jahres <?= $calendar->Year() ?><br>
	(Runde <?= $calendar->Round() ?>)
</p>

<h2><?= $party->Name() ?> <span class="badge badge-primary"><?= $party->Id() ?></span></h2>

<blockquote class="blockquote"><?= $party->Description() ?></blockquote>

<p>Dein Volk zählt <?= $this->number(1872, 'race', $party->Race()) ?> in <?= $this->number(96) ?> Einheiten.</p>

<h3>Alle bekannten Völker</h3>

<ul>
	<li><?= $party->Name() ?> <span class="badge badge-primary"><?= $party->Id() ?></span></li>
</ul>

<h3>Kontinent Lemuria<span class="badge badge-primary"><?= $party->Id() ?></span></h3>

<blockquote class="blockquote">Dies ist der Hauptkontinent Lemuria.</blockquote>

<?php
foreach ($census->getAtlas() as $region /* @var Region $region */):
	$resources = $region->Resources();
	$t		 = $resources[Tree::class]->Count();
	$g		 = $resources[Granite::class]->Count();
	$o		 = $resources[Ore::class]->Count();
	$m		 = $g && $o;
	$trees	 = $this->item(Tree::class, $resources);
	$granite   = $this->item(Granite::class, $resources);
	$ore	   = $this->item(Ore::class, $resources);
	$mining	= null;
	if ($m):
		$mining = $granite . ' und ' . $ore;
	elseif ($g):
		$mining = $granite;
	elseif ($o):
		$mining = $ore;
	endif;
	?>

	<h4><?= $region->Name() ?> <span class="badge badge-light"><?= $world->getCoordinates($region) ?></span></h4>
	<?php
	$neighbours = [];
	foreach ($world->getNeighbours($region)->getAll() as $direction => $neighbour):
		$neighbours[] = 'im ' . $this->get('world', $direction) . ' liegt ' . $this->neighbour($neighbour);
	endforeach;
	$n = count($neighbours);
	if ($n > 1):
		$neighbours[$n - 2] .= ' und ' . $neighbours[$n - 1];
		unset($neighbours[$n - 1]);
	endif;
	$intelligence = new Intelligence($region);
	$guards       = $intelligence->getGuards();
	$g            = count($guards);
	if ($g > 0):
		$guardNames = [];
		foreach ($guards as $unit /* @var Unit $unit */):
			$guardNames[] = $unit->Name();
		endforeach;
		if ($g > 1):
			$guardNames[$g - 2] .= ' und ' . $guardNames[$g - 1];
			unset ($guardNames[$g - 1]);
		endif;
	endif;
	?>
	<p>
		<?= $this->get('landscape', $region->Landscape()) ?>,
		<?= $this->item(Peasant::class, $resources) ?>,
		<?= $this->item(Silver::class, $resources) ?>.
		<?php if ($t && $m): ?>
			Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> gefällt sowie <?= $mining ?> abgebaut werden.
		<?php elseif ($t): ?>
			Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> gefällt werden.
		<?php elseif ($g || $o): ?>
			Hier <?= $g + $o === 1 ? 'kann' : 'können' ?> <?= $mining ?> abgebaut werden.
		<?php endif ?>
		<?php if ($g > 0): ?>
			Die Region wird bewacht von <?= ucfirst(implode(', ', $guardNames)) ?>.
		<?php endif ?>
		<br>
		<?= ucfirst(implode(', ', $neighbours)) ?>.
		<br>
		<?= $region->Description() ?>
	</p>

	<?php foreach ($region->Estate() as $construction /* @var Construction $construction */): ?>
		<h5><?= $construction->Name() ?> <span class="badge badge-secondary"><?= $construction->Id() ?></span></h5>
		<p>
			<?= $this->get('building', $construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?>.
			Besitzer ist
			<?php if (count($construction->Inhabitants())): ?>
				<?= $construction->Inhabitants()->Owner()->Name() ?> <span class="badge badge-primary"><?= $construction->Inhabitants()->Owner()->Id() ?></span>.
			<?php else: ?>
				niemand.
			<?php endif ?>
			<?= $construction->Description() ?>
		</p>
		<?php foreach ($construction->Inhabitants() as $unit /* @var Unit $unit */): ?>
			<h6><?= $unit->Name() ?> <span class="badge badge-primary"><?= $unit->Id() ?></span></h6>
			<p>
				<?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsGuarding()) echo ', bewacht die Region'; ?>.
				<?= $unit->Description() ?><br>
				<?php
				$talents = [];
				foreach ($unit->Knowledge() as $ability /* @var Ability $ability */) {
					$talents[] = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($ability->Experience()) . ')';
				}
				$inventory = [];
				$payload   = 0;
				foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */) {
					$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
					$payload += $quantity->Weight();
				}
				$n = count($inventory);
				if ($n > 1) {
					$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
					unset($inventory[$n - 1]);
				}
				$weight = (int)ceil($payload / 100);
				$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);
				?>
				Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>.
				Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>,
				Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
			</p>
		<?php endforeach ?>
	<?php endforeach ?>

	<?php foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */): ?>
		<h5><?= $vessel->Name() ?> <span class="badge badge-info"><?= $vessel->Id() ?></span></h5>
		<p>
			<?= $this->get('ship', $vessel->Ship()) ?>, freier Platz <?= $this->number((int)ceil($vessel->Space() / 100)) ?> GE.
			Kapitän ist
			<?php if (count($vessel->Passengers())): ?>
				<?= $vessel->Passengers()->Owner()->Name() ?> <span class="badge badge-primary"><?= $vessel->Passengers()->Owner()->Id() ?></span>.
			<?php else: ?>
				niemand.
			<?php endif ?>
			<?= $vessel->Description() ?>
		</p>
		<?php foreach ($vessel->Passengers() as $unit /* @var Unit $unit */): ?>
			<h6><?= $unit->Name() ?> <span class="badge badge-primary"><?= $unit->Id() ?></span></h6>
			<p>
				<?= $this->number($unit->Size(), 'race', $unit->Race()) ?>.
				<?= $unit->Description() ?><br>
				<?php
				$talents = [];
				foreach ($unit->Knowledge() as $ability /* @var Ability $ability */) {
					$talents[] = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($ability->Experience()) . ')';
				}
				$inventory = [];
				$payload   = 0;
				foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */) {
					$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
					$payload += $quantity->Weight();
				}
				$n = count($inventory);
				if ($n > 1) {
					$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
					unset($inventory[$n - 1]);
				}
				$weight = (int)ceil($payload / 100);
				$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);
				?>
				Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>.
				Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>,
				Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
			</p>
		<?php endforeach ?>
	<?php endforeach ?>

	<?php $unitsInRegions = 0 ?>
	<?php foreach ($census->getPeople($region) as $unit /* @var Unit $unit */): ?>
		<?php if (!$unit->Construction() && !$unit->Vessel()): ?>
			<?php if ($unitsInRegions++ === 0): ?>
				<h5>Weitere Einheiten</h5>
			<?php endif ?>
			<h6><?= $unit->Name() ?> <span class="badge badge-primary"><?= $unit->Id() ?></span></h6>
			<p>
				<?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsGuarding()) echo ', bewacht die Region'; ?>.
				<?= $unit->Description() ?><br>
				<?php
				$talents = [];
				foreach ($unit->Knowledge() as $ability /* @var Ability $ability */) {
					$talents[] = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($ability->Experience()) . ')';
				}
				$inventory = [];
				$payload   = 0;
				foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */) {
					$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
					$payload += $quantity->Weight();
				}
				$n = count($inventory);
				if ($n > 1) {
					$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
					unset($inventory[$n - 1]);
				}
				$weight = (int)ceil($payload / 100);
				$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);
				?>
				Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>.
				Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>,
				Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
			</p>
		<?php endif ?>
	<?php endforeach ?>
<?php endforeach ?>
