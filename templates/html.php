<?php
declare (strict_types = 1);

use function Lemuria\getClass;
use Lemuria\Engine\Fantasya\Availability;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Commodity\Camel;
use Lemuria\Model\Fantasya\Commodity\Elephant;
use Lemuria\Model\Fantasya\Commodity\Griffin;
use Lemuria\Model\Fantasya\Commodity\Griffinegg;
use Lemuria\Model\Fantasya\Commodity\Horse;
use Lemuria\Model\Fantasya\Commodity\Iron;
use Lemuria\Model\Fantasya\Commodity\Peasant;
use Lemuria\Model\Fantasya\Commodity\Silver;
use Lemuria\Model\Fantasya\Commodity\Stone;
use Lemuria\Model\Fantasya\Commodity\Wood;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\View;

/**
 * A parties' report in HTML.
 */

/* @var View $this */

$party            = $this->party;
$report           = $this->messages($party);
$diplomacy        = $party->Diplomacy();
$acquaintances    = $diplomacy->Acquaintances();
$generalRelations = $diplomacy->search($party);
$census           = $this->census;
$outlook          = $this->outlook;
$atlas            = $this->atlas;
$map              = $this->map;
$race             = getClass($party->Race());
$calendar         = Lemuria::Calendar();
$season           = $this->get('calendar.season', $calendar->Season() - 1);
$month            = $this->get('calendar.month', $calendar->Month() - 1);
$week             = $calendar->Week();

?>
<h1 class="text-center">Lemuria-Auswertung</h1>

<p class="text-center">
	für die <?= $week ?>. Woche des Monats <?= $month ?> im <?= $season ?> des Jahres <?= $calendar->Year() ?><br>
	(Runde <?= $calendar->Round() ?>)
</p>

<h2><?= $party->Name() ?> <span class="badge badge-primary"><?= $party->Id() ?></span></h2>

<blockquote class="blockquote"><?= $party->Description() ?></blockquote>

<p>Dein Volk zählt <?= $this->number($census->count(), 'race', $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.</p>

<h3>Ereignisse</h3>

<?php if (count($report)): ?>
	<ul class="report">
		<?php foreach ($report as $message): ?>
			<li><?= $this->message($message) ?></li>
		<?php endforeach ?>
	</ul>
<?php endif ?>

<h3>Alle bekannten Völker</h3>

<?php if ($acquaintances->count()): ?>
	<ul class="diplomacy">
		<?php foreach ($acquaintances as $acquaintance /* @var Party $acquaintance */): ?>
			<li>
				<?= $acquaintance->Name() ?> <span class="badge badge-primary"><?= $acquaintance->Id() ?></span>
				<?php $relations = $diplomacy->search($acquaintance) ?>
				<?php if ($relations): ?>
					<ul>
						<?php foreach ($relations as $relation /* @var Relation $relation */): ?>
						<li>
							<?php if ($relation->Region()): ?>
								Beziehungen in Region <?= $relation->Region() ?>:
								<?= $this->relation($relation) ?>
							<?php else: ?>
								Beziehungen:
								<?= $this->relation($relation) ?>
							<?php endif ?>
						</li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
			</li>
		<?php endforeach ?>
		<?php foreach ($generalRelations as $relation /* @var Relation $relation */): ?>
			<li>
				<?php if ($relation->Region()): ?>
					Allgemeine Beziehungen in Region <?= $relation->Region() ?>:
					<?= $this->relation($relation) ?>
				<?php else: ?>
					Allgemeine Beziehungen:
					<?= $this->relation($relation) ?>
				<?php endif ?>
			</li>
		<?php endforeach ?>
	</ul>
<?php endif ?>

<h3>Kontinent Lemuria <span class="badge badge-primary"><?= $party->Id() ?></span></h3>

<blockquote class="blockquote">Dies ist der Hauptkontinent Lemuria.</blockquote>

<?php
foreach ($atlas as $region /* @var Region $region */):
	$landscape  = $region->Landscape();
	$isOcean    = $landscape instanceof Ocean;
	$hasUnits   = $atlas->getVisibility($region) === TravelAtlas::WITH_UNIT;
	$resources  = $region->Resources();
	$neighbours = [];
	foreach ($map->getNeighbours($region)->getAll() as $direction => $neighbour):
		$neighbours[] = 'im ' . $this->get('world', $direction) . ' liegt ' . $this->neighbour($neighbour);
	endforeach;
	$n = count($neighbours);
	if ($n > 1):
		$neighbours[$n - 2] .= ' und ' . $neighbours[$n - 1];
		unset($neighbours[$n - 1]);
	endif;

	if ($hasUnits):
		$report  = $this->messages($region);
		$t       = $resources[Wood::class]->Count();
		$g       = $resources[Stone::class]->Count();
		$o       = $resources[Iron::class]->Count();
		$m       = $g && $o;
		$trees   = $this->item(Wood::class, $resources);
		$granite = $this->item(Stone::class, $resources);
		$ore     = $this->item(Iron::class, $resources);
		$mining	 = null;
		if ($m):
			$mining = $granite . ' und ' . $ore;
		elseif ($g):
			$mining = $granite;
		elseif ($o):
			$mining = $ore;
		endif;

		$a       = $resources[Horse::class]->Count() + $resources[Camel::class]->Count() + $resources[Elephant::class]->Count();
		$animals = $this->items([Horse::class, Camel::class, Elephant::class], $resources);
		$gr      = $resources[Griffin::class]->Count();
		$egg     = $resources[Griffinegg::class]->Count();
		$griffin = null;
		if ($gr):
			$griffin = $this->item(Griffin::class, $resources);
			if ($egg):
				$griffin .= ' mit ' . $this->item(Griffinegg::class, $resources);
			endif;
		endif;

		$availability = new Availability($region);
		$peasants     = $availability->getResource(Peasant::class);
		$recruits     = $this->resource($peasants);
		$r            = $peasants->Count();

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
		$materialPool = [];
		foreach ($intelligence->getMaterialPool($party) as $quantity /* @var Quantity $quantity */):
			$materialPool[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
		endforeach;
	endif;
	?>

	<h4><?= $region->Name() ?> <span class="badge badge-light"><?= $map->getCoordinates($region) ?></span> <span class="badge badge-secondary"><?= $region->Id() ?></span></h4>

	<?php if ($hasUnits): ?>
		<p>
			<?php if ($isOcean): ?>
				<?php if ($region->Name() !== 'Ozean'): ?>
					<?= $this->get('landscape', $region->Landscape()) ?>.
					<br>
				<?php endif ?>
			<?php else: ?>
				<?= $this->get('landscape', $region->Landscape()) ?>,
				<?= $this->item(Peasant::class, $resources) ?>,
				<?= $this->item(Silver::class, $resources) ?>.
				<?php if ($r > 0): ?>
					<?= $recruits ?> <?= $r === 1 ? 'kann' : 'können' ?> rekrutiert werden.
				<?php endif ?>
				<?php if ($t && $m): ?>
					Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> geerntet sowie <?= $mining ?> abgebaut werden.
				<?php elseif ($t): ?>
					Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> geerntet werden.
				<?php elseif ($g || $o): ?>
					Hier <?= $g + $o === 1 ? 'kann' : 'können' ?> <?= $mining ?> abgebaut werden.
				<?php endif ?>
				<?php if ($a): ?>
					<?= $animals ?> <?= $a === 1 ? 'streift' : 'streifen' ?> durch die Wildnis.
				<?php endif ?>
				<?php if ($gr): ?>
					<?= $griffin ?> <?= $gr === 1 ? ' nistet ' : 'nisten' ?> in den Bergen.
				<?php endif ?>
				<?php if ($g > 0): ?>
					Die Region wird bewacht von <?= ucfirst(implode(', ', $guardNames)) ?>.
				<?php endif ?>
				<br>
			<?php endif ?>
			<?= ucfirst(implode(', ', $neighbours)) ?>.
			<br>
			<?= $region->Description() ?>
		</p>
	<?php else: ?>
		<p>
			<?php if ($isOcean): ?>
				<?php if ($region->Name() !== 'Ozean'): ?>
					<?= $this->get('landscape', $region->Landscape()) ?>.
					<br>
				<?php endif ?>
			<?php else: ?>
				<?= $this->get('landscape', $region->Landscape()) ?>.
				<br>
			<?php endif ?>
			<?= ucfirst(implode(', ', $neighbours)) ?>.
			<br>
			<?= $region->Description() ?>
		</p>
	<?php endif ?>

	<?php if ($hasUnits && count($report)): ?>
		<h5>Ereignisse</h5>
		<ul class="report">
			<?php foreach ($report as $message): ?>
				<li><?= $this->message($message) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>

	<?php if ($hasUnits): ?>
		<h5>Materialpool</h5>

		<?php if (count($materialPool) > 0): ?>
			<p><?= implode(', ', $materialPool) ?>.</p>
		<?php else: ?>
			<p>Der Materialpool ist leer.</p>
		<?php endif ?>

		<?php foreach ($region->Estate() as $construction /* @var Construction $construction */): ?>
			<h5><?= $construction->Name() ?> <span class="badge badge-secondary"><?= $construction->Id() ?></span></h5>
			<p>
				<?= $this->get('building', $construction->Building()) ?> der Größe <?= $this->number($construction->Size()) ?> mit <?= $this->number($this->people($construction)) ?> Bewohnern.
				Besitzer ist
				<?php if (count($construction->Inhabitants())): ?>
					<?= $construction->Inhabitants()->Owner()->Name() ?> <span class="badge badge-primary"><?= $construction->Inhabitants()->Owner()->Id() ?></span>.
				<?php else: ?>
					niemand.
				<?php endif ?>
				<?= $construction->Description() ?>
			</p>

			<?php if (count($report = $this->messages($construction))): ?>
				<h6>Ereignisse</h6>
				<ul class="report">
					<?php foreach ($report as $message): ?>
						<li><?= $this->message($message) ?></li>
					<?php endforeach ?>
				</ul>
			<?php endif ?>

			<?php foreach ($construction->Inhabitants() as $unit /* @var Unit $unit */): ?>
				<div class="unit">
					<?php
					$isOwn = $unit->Party() === $party;
					if ($isOwn):
						$disguised = $unit->Disguise();
						$calculus  = new Calculus($unit);
						$talents   = [];
						foreach ($unit->Knowledge() as $ability /* @var Ability $ability */):
							$experience = $ability->Experience();
							$ability    = $calculus->knowledge($ability->Talent());
							$talents[]  = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($experience) . ')';
						endforeach;
						$inventory = [];
						$payload   = 0;
						foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */):
							$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
							$payload += $quantity->Weight();
						endforeach;
						$n = count($inventory);
						if ($n > 1):
							$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
							unset($inventory[$n - 1]);
						endif;
						$weight = (int)ceil($payload / 100);
						$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);
					else:
						$foreign = $census->getParty($unit);
					endif;
					?>
					<h6>
						<?= $unit->Name() ?> <span class="badge badge-primary"><?= $unit->Id() ?></span>
						<?php if (!$isOwn): ?>
							<?php if ($foreign): ?>
								von <?= $foreign->Name() ?> <span class="badge badge-secondary"><?= $foreign->Id() ?></span>
							<?php else: ?>
								(unbekannte Partei)
							<?php endif ?>
						<?php endif ?>
					</h6>
					<p>
						<?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsHiding()): ?>, getarnt<?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?><?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?><?php endif ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
						<?= $unit->Description() ?>
						<?php if ($isOwn): ?>
							<br>
							Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>.
							<br>
							Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>,
							Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
						<?php endif ?>
					</p>
					<?php if ($isOwn && count($report = $this->messages($unit))): ?>
						<ul class="report">
							<?php foreach ($report as $message): ?>
								<li><?= $this->message($message) ?></li>
							<?php endforeach ?>
						</ul>
					<?php endif ?>
				</div>
			<?php endforeach ?>
		<?php endforeach ?>

		<?php foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */): ?>
			<?php $passengers = $this->people($vessel) ?>
			<h5><?= $vessel->Name() ?> <span class="badge badge-info"><?= $vessel->Id() ?></span></h5>
			<p>
				<?= $this->get('ship', $vessel->Ship()) ?> mit <?= $this->number($passengers) ?> <?php if ($passengers === 1): ?>Passagier<?php else: ?>Passagieren<?php endif ?>, Zustand <?= $this->number((int)round(100.0 * $vessel->Completion())) ?>%, <?php if ($vessel->Space() < 0): ?>überladen mit<?php else: ?>freier Platz<?php endif ?> <?= $this->number((int)ceil(abs($vessel->Space()) / 100)) ?> GE.
				Kapitän ist
				<?php if (count($vessel->Passengers())): ?>
					<?= $vessel->Passengers()->Owner()->Name() ?> <span class="badge badge-primary"><?= $vessel->Passengers()->Owner()->Id() ?></span>.
				<?php else: ?>
					niemand.
				<?php endif ?>
				<?php if (!($vessel->Region()->Landscape() instanceof Ocean)): ?>
					<?php if ($vessel->Anchor() === Vessel::IN_DOCK): ?>
						Das Schiff liegt im Dock.
					<?php else: ?>
						Das Schiff ankert im <?= $this->get('world', $vessel->Anchor()) ?>.
					<?php endif ?>
				<?php endif ?>
				<?= $vessel->Description() ?>
			</p>

			<?php if (count($report = $this->messages($vessel))): ?>
				<h6>Ereignisse</h6>
				<ul class="report">
					<?php foreach ($report as $message): ?>
						<li><?= $this->message($message) ?></li>
					<?php endforeach ?>
				</ul>
			<?php endif ?>

			<?php foreach ($vessel->Passengers() as $unit /* @var Unit $unit */): ?>
				<div class="unit">
					<?php
					$isOwn = $unit->Party() === $party;
					if ($isOwn):
						$disguised = $unit->Disguise();
						$calculus  = new Calculus($unit);
						$talents   = [];
						foreach ($unit->Knowledge() as $ability /* @var Ability $ability */):
							$experience = $ability->Experience();
							$ability    = $calculus->knowledge($ability->Talent());
							$talents[]  = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($experience) . ')';
						endforeach;
						$inventory = [];
						$payload   = 0;
						foreach ($unit->Inventory() as $quantity /* @var Quantity $quantity */):
							$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
							$payload += $quantity->Weight();
						endforeach;
						$n = count($inventory);
						if ($n > 1):
							$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
							unset($inventory[$n - 1]);
						endif;
						$weight = (int)ceil($payload / 100);
						$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);
					else:
						$foreign = $census->getParty($unit);
					endif;
					?>
					<h6>
						<?= $unit->Name() ?> <span class="badge badge-primary"><?= $unit->Id() ?></span>
						<?php if (!$isOwn): ?>
							<?php if ($foreign): ?>
								von <?= $foreign->Name() ?> <span class="badge badge-secondary"><?= $foreign->Id() ?></span>
							<?php else: ?>
								(unbekannte Partei)
							<?php endif ?>
						<?php endif ?>
					</h6>
					<p>
						<?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsHiding()): ?>, getarnt<?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?><?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?><?php endif ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
						<?= $unit->Description() ?>
						<?php if ($isOwn): ?>
							<br>
							Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>.
							<br>
							Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>,
							Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
						<?php endif ?>
					</p>
					<?php if ($isOwn && count($report = $this->messages($unit))): ?>
						<ul class="report">
							<?php foreach ($report as $message): ?>
								<li><?= $this->message($message) ?></li>
							<?php endforeach ?>
						</ul>
					<?php endif ?>
				</div>
			<?php endforeach ?>
		<?php endforeach ?>

		<?php $unitsInRegions = 0 ?>
		<?php foreach ($outlook->Apparitions($region) as $unit /* @var Unit $unit */): ?>
			<?php if ($unitsInRegions++ === 0): ?>
				<h5>Einheiten in der Region</h5>
				<br>
			<?php endif ?>
			<div class="unit">
				<?php
				$isOwn = $unit->Party() === $party;
				if ($isOwn):
					$disguised = $unit->Disguise();
					$calculus  = new Calculus($unit);
					$talents   = [];
					foreach ($unit->Knowledge() as $ability/* @var Ability $ability */):
						$experience = $ability->Experience();
						$ability    = $calculus->knowledge($ability->Talent());
						$talents[]  = $this->get('talent', $ability->Talent()) . ' ' . $ability->Level() . ' (' . $this->number($experience) . ')';
					endforeach;
					$inventory = [];
					$payload   = 0;
					foreach ($unit->Inventory() as $quantity/* @var Quantity $quantity */):
						$inventory[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
						$payload     += $quantity->Weight();
					endforeach;
					$n = count($inventory);
					if ($n > 1):
						$inventory[$n - 2] .= ' und ' . $inventory[$n - 1];
						unset($inventory[$n - 1]);
					endif;
					$weight = (int)ceil($payload / 100);
					$total  = (int)ceil(($payload + $unit->Size() * $unit->Race()->Weight()) / 100);
				else:
					$foreign = $census->getParty($unit);
				endif;
				?>
				<h6>
					<?= $unit->Name() ?> <span class="badge badge-primary"><?= $unit->Id() ?></span>
					<?php if (!$isOwn): ?>
						<?php if ($foreign): ?>
							von <?= $foreign->Name() ?> <span class="badge badge-secondary"><?= $foreign->Id() ?></span>
						<?php else: ?>
							(unbekannte Partei)
						<?php endif ?>
					<?php endif ?>
				</h6>
				<p>
					<?= $this->number($unit->Size(), 'race', $unit->Race()) ?><?php if ($unit->IsHiding()): ?>, getarnt<?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?><?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?><?php endif ?><?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>.
					<?= $unit->Description() ?>
					<?php if ($isOwn): ?>
						<br>
						Talente: <?= empty($talents) ? 'keine' : implode(', ', $talents) ?>.
						<br>
						Hat <?= empty($inventory) ? 'nichts' : implode(', ', $inventory) ?>,
						Last <?= $this->number($weight) ?> GE, zusammen <?= $this->number($total) ?> GE.
					<?php endif ?>
				</p>
				<?php if ($isOwn && count($report = $this->messages($unit))): ?>
					<ul class="report">
						<?php foreach ($report as $message): ?>
							<li><?= $this->message($message) ?></li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
			</div>
		<?php endforeach ?>
	<?php endif ?>
<?php endforeach ?>
