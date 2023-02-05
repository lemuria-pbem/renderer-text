<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Continent $continent */
$continent         = $this->variables[0];
$party             = $this->party;
$name              = $continent->getNameFor($party);
$description       = $continent->getDescriptionFor($party);
$hasOwnName        = $continent->hasNameFor($party);
$hasOwnDescription = $continent->hasDescriptionFor($party);

if ($hasOwnName):
	$name .= ' [' . $party->Id() . ']';
endif;

$acquaintances = $party->Diplomacy()->Acquaintances();
$other         = [];
foreach ($acquaintances as $acquaintance):
	$id  = (string)$acquaintance->Id();
	$has = false;
	if ($continent->hasNameFor($acquaintance)):
		$other[$id]['name'] = $continent->getNameFor($acquaintance);
		$has                = true;
	endif;
	if ($continent->hasDescriptionFor($acquaintance)):
		$other[$id]['description'] = $continent->getDescriptionFor($acquaintance);
		$has                       = true;
	endif;
	if ($has):
		$other[$id]['party'] = $acquaintance->Name();
	endif;
endforeach;

?>
<?= center($name) ?>

<?= wordwrap($description) ?>

<?php foreach ($other as $id => $info): ?>
<?php if (isset($info['name'])): ?>
<?php if (isset($info['description'])): ?>
Von der Partei <?= $info['party'] ?> [<?= $id ?>] wird dieser Teil der Welt <?= $info['name'] ?> genannt. „<?= $info['description'] ?>“
<?php else: ?>
Von der Partei <?= $info['party'] ?> [<?= $id ?>] wird dieser Teil der Welt <?= $info['name'] ?> genannt.
<?php endif ?>
<?php else: ?>
Das Volk der Partei <?= $info['party'] ?> [<?= $id ?>] sagt über dieses Land: „<?= $info['description'] ?>“
<?php endif ?>
<?php endforeach ?>
<?php if ($hasOwnName): ?>
<?php if ($hasOwnDescription): ?>
Ursprünglich war dieses Land bekannt als <?= $continent->Name() ?> – <?= $continent->Description() ?>

<?php else: ?>
Der ursprüngliche Name dieses Landes ist <?= $continent->Name() ?>.
<?php endif ?>
<?php endif ?>
