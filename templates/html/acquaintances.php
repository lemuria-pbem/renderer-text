<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\linkEmail;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party            = $this->variables[0];
$diplomacy        = $party->Diplomacy();
$acquaintances    = $diplomacy->Acquaintances();
$generalRelations = $diplomacy->search($party);

?>
<?php if ($acquaintances->count()): ?>
	<ul class="diplomacy">
	<?php foreach ($acquaintances as $acquaintance /* @var Party $acquaintance */): ?>
		<li>
		<?= $acquaintance->Name() ?> <span class="badge badge-primary"><?= $acquaintance->Id() ?></span>
		<?php if ($acquaintance->Banner()): ?> - <?= linkEmail($acquaintance->Banner()) ?><?php endif ?>
		<br>
		<small><?= $acquaintance->Description() ?></small>
		<?php $relations = $diplomacy->search($acquaintance) ?>
		<?php if ($relations): ?>
			<ul>
			<?php foreach ($relations as $relation /* @var Relation $relation */): ?>
				<li>
				<?php if ($relation->Region()): ?>
					Allianzrechte in Region <?= $relation->Region() ?>:
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
		</li>
	<?php endforeach ?>
	<?php foreach ($generalRelations as $relation /* @var Relation $relation */): ?>
		<li>
		<?php if ($relation->Region()): ?>
			Allgemein vergebene Rechte in Region <?= $relation->Region() ?>:
			<?= $this->relation($relation) ?>
		<?php else: ?>
			Allgemein vergebene Rechte:
			<?= $this->relation($relation) ?>
		<?php endif ?>
		</li>
	<?php endforeach ?>
	</ul>
<?php endif ?>
