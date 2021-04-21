<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Model\Fantasya\Ability;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region   = $this->variables[0];
$party    = $this->party;
$census   = $this->census;
$outlook  = $this->outlook;
$atlas    = $this->atlas;
$map      = $this->map;
$hasUnits = $atlas->getVisibility($region) === TravelAtlas::WITH_UNIT;

if ($hasUnits):
	$report       = $this->messages($region);
	$intelligence = new Intelligence($region);
	$materialPool = [];
	foreach ($intelligence->getMaterialPool($party) as $quantity /* @var Quantity $quantity */):
		$materialPool[] = $this->number($quantity->Count(), 'resource', $quantity->Commodity());
	endforeach;
endif;

?>
<h4>
	<?= $region->Name() ?>
	<span class="badge badge-light"><?= $map->getCoordinates($region) ?></span>
	<span class="badge badge-secondary"><?= $region->Id() ?></span>
</h4>

<?php if ($hasUnits): ?>
	<?= $this->template('region/with-unit', $region) ?>

	<?php if (count($report)): ?>
		<h5>Ereignisse</h5>
		<?= $this->template('report', $region) ?>
	<?php endif ?>

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
			<?= $this->template('report', $construction) ?>
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
					<?= $this->template('report', $unit) ?>
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
			<?= $this->template('report', $vessel) ?>
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
					<?= $this->template('report', $unit) ?>
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
				<?= $this->template('report', $unit) ?>
			<?php endif ?>
		</div>
	<?php endforeach ?>
<?php else: ?>
	<?= $this->template('region/neighbour', $region) ?>
<?php endif ?>
