<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Factory\PartyUnica;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party    = $this->party;
$spells   = $party->SpellBook()->count();
$herbs    = $party->HerbalBook()->count();
$unica    = new PartyUnica($party);
$treasury = $unica->Treasury();
$hasLinks = $spells + $herbs + $treasury->count();
$i        = $spells;

?>
<?php if ($hasLinks): ?>
	<p>
		<?php if ($spells > 0): ?>
			<a id="spell-book" href="<?= $this->spellBookPath() ?>" title="Taste: Z" target="spell-book">Zauberbuch anzeigen</a>
		<?php endif ?>
		<?php if ($herbs > 0): ?>
			<?= $i++ > 0 ? '·' : '' ?>
			<a id="herbal-book" href="<?= $this->herbalBookPath() ?>" title="Taste: K" target="herbal-book">Kräutervorkommen anzeigen</a>
		<?php endif ?>
		<?php foreach ($treasury as $unicum): ?>
			<?= $i++ > 0 ? '·' : '' ?>
			<a href="<?= $this->unicumPath($unicum) ?>" target="unicum-<?= $unicum->Id() ?>"><?= $this->composition($unicum->Composition()) ?> „<?= $unicum->Name() ?>”</a>
		<?php endforeach ?>
	</p>
<?php endif ?>

