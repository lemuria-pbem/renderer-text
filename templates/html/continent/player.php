<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Continent $continent */
$continent         = $this->variables[0];
$party             = $this->party;
$name              = $continent->getNameFor($party);
$description       = $continent->getDescriptionFor($party);
$hasOwnName        = $continent->hasNameFor($party);
$hasOwnDescription = $continent->hasDescriptionFor($party);

$acquaintances = $party->Diplomacy()->Acquaintances();
$other         = [];
foreach ($acquaintances as $acquaintance /* @var Party $acquaintance */):
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
<h3 id="<?= id($continent) ?>">
	<?= $name ?>
	<span class="badge text-bg-primary font-monospace"><?= $party->Id() ?></span>
</h3>

<blockquote class="blockquote"><?= $description ?></blockquote>

<?php foreach ($other as $id => $info): ?>
	<?php if (isset($info['name'])): ?>
		<?php if (isset($info['description'])): ?>
			<p>
				Von der Partei <?= $info['party'] ?> <span class="badge text-bg-primary font-monospace"><?= $id ?></span> wird dieser Teil der Welt <em><?= $info['name'] ?></em> genannt.
				<em>„<?= $info['description'] ?>“</em>
			</p>
		<?php else: ?>
			<p>Von der Partei <?= $info['party'] ?> <span class="badge text-bg-primary font-monospace"><?= $id ?></span> wird dieser Teil der Welt <em><?= $info['name'] ?></em> genannt.</p>
		<?php endif ?>
	<?php else: ?>
		<p>Das Volk der Partei <?= $info['party'] ?> <span class="badge text-bg-primary font-monospace"><?= $id ?></span> sagt über dieses Land: <em>„<?= $info['description'] ?>“</em></p>
	<?php endif ?>
<?php endforeach ?>
<?php if ($hasOwnName): ?>
	<?php if ($hasOwnDescription): ?>
		<p>Ursprünglich war dieses Land bekannt als <em><?= $continent->Name() ?></em>. <em>„<?= $continent->Description() ?>“</em></p>
	<?php else: ?>
		<p>Der ursprüngliche Name dieses Landes ist <em><?= $continent->Name() ?></em>.</p>
	<?php endif ?>
<?php endif ?>
