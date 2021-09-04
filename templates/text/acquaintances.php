<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Relation;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$party            = $this->variables[0];
$diplomacy        = $party->Diplomacy();
$acquaintances    = $diplomacy->Acquaintances();
$generalRelations = $diplomacy->search($party);

$i = 0

?>
<?php if ($acquaintances->count()): ?>
<?php foreach ($acquaintances as $acquaintance /* @var Party $acquaintance */): ?>

<?= $acquaintance ?><?php if ($acquaintance->Banner()): ?> - <?= $acquaintance->Banner() ?><?php endif ?>

<?= $acquaintance->Description() ?>

<?php $relations = $diplomacy->search($acquaintance) ?>
<?php if ($relations): ?>
<?php foreach ($relations as $relation /* @var Relation $relation */): ?>
<?php if ($relation->Region()): ?>
Allianzrechte in Region <?= $relation->Region() ?>: <?= $this->relation($relation) ?>
<?php else: ?>
Allianzrechte: <?= $this->relation($relation) ?>
<?php endif ?>

<?php endforeach ?>
<?php else: ?>Allianzrechte: keine<?php endif ?>

<?php endforeach ?>
<?php else: ?>

<?php endif ?>
<?php if ($generalRelations): ?>
<?php foreach ($generalRelations as $relation /* @var Relation $relation */): ?>

<?php if ($relation->Region()): ?>
Allgemein vergebene Rechte in Region <?= $relation->Region() ?>: <?= $this->relation($relation) ?>
<?php else: ?>
Allgemein vergebene Rechte: <?= $this->relation($relation) ?>
<?php endif ?>

<?php endforeach ?>
<?php endif ?>
