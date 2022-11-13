<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\linkEmail;
use function Lemuria\Renderer\Text\View\p3;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party            = $this->variables[0];
$diplomacy        = $party->Diplomacy();
$acquaintances    = $diplomacy->Acquaintances();
$generalRelations = $diplomacy->search($party);

$a = 0;
$g = 0;

?>
<h3 id="alliances" title="Taste: A">Bekannte Völker und Allianzen</h3>

<?php if ($acquaintances->count()): ?>
	<div id="acquaintances" class="container-fluid">
		<div class="row">
			<?php foreach ($acquaintances as $acquaintance /* @var Party $acquaintance */): ?>
				<div class="col-12 col-lg-6 col-xl-4 <?= p3(++$a, 'lg') ?>">
					<?= $acquaintance->Name() ?> <span class="badge text-bg-primary"><?= $acquaintance->Id() ?></span>
					<?php if ($acquaintance->Banner()): ?> - <?= linkEmail($acquaintance->Banner()) ?><?php endif ?>
					<br>
					<small><?= $acquaintance->Description() ?></small>
					<?php $relations = $diplomacy->search($acquaintance) ?>
					<?php if ($relations): ?>
						<ul>
						<?php foreach ($relations as $relation /* @var Relation $relation */): ?>
							<li>
							<?php if ($relation->Region()): ?>
								Allianzrechte in <?= $relation->Region() ?>:
								<?= $this->relation($relation) ?>
							<?php else: ?>
								Allianzrechte:
								<?= $this->relation($relation) ?>
							<?php endif ?>
							</li>
						<?php endforeach ?>
						</ul>
					<?php else: ?>
						<ul>
							<li>Allianzrechte: keine</li>
						</ul>
					<?php endif ?>
				</div>
			<?php endforeach ?>
		</div>
		<?php if (count($generalRelations) > 0): ?>
			<div class="row">
				<div class="col-12 p-0">
					<h4>Diplomatische Grundhaltung</h4>
				</div>
				<?php foreach ($generalRelations as $relation /* @var Relation $relation */): ?>
					<div class="col-12 col-lg-6 col-xl-4 <?= p3(++$g, 'lg') ?>">
						<?php if ($relation->Region()): ?>
							Allgemeine Rechte in <?= $relation->Region() ?>:
							<?= $this->relation($relation) ?>
						<?php else: ?>
							Allgemeine Rechte:
							<?= $this->relation($relation) ?>
						<?php endif ?>
					</div>
				<?php endforeach ?>
			</div>
		<?php endif ?>
	</div>
<?php else: ?>
	<p>Du hattest bisher keinen Kontakt zu anderen Völkern.</p>
<?php endif ?>
