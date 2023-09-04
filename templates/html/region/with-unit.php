<?php
/** @noinspection PhpUndefinedVariableInspection */
declare (strict_types = 1);

use function Lemuria\number;
use Lemuria\Engine\Fantasya\Availability;
use Lemuria\Model\Fantasya\Building\Site;
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
use Lemuria\Model\Fantasya\Intelligence;
use Lemuria\Model\Fantasya\Landscape\Lake;
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Navigable;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region     = $this->variables[0];
$party      = $this->party;
$atlas      = $this->atlas;
$map        = $this->map;
$landscape  = $region->Landscape();
$name       = $region->Name();
$wage       = $this->wage($region);
$resources  = $region->Resources();
$neighbours = $this->neighbours($region);
$treasury   = $region->Treasury();

$tr      = $treasury->count();
$t       = $resources[Wood::class]->Count();
$g       = $resources[Stone::class]->Count();
$o       = $resources[Iron::class]->Count();
$m       = $g && $o;
$trees   = $t === 1 ? '1 Baum' : number($t) . ' Bäume';
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
$hasPeasants  = $resources[Peasant::class]->Count() > 0;

$intelligence = new Intelligence($region);
$guards       = $intelligence->getGuards();
$gs           = count($guards);
if ($gs > 0):
	$guardNames = [];
	foreach ($guards as $unit):
		$guardNames[] = (string)$unit;
	endforeach;
	if ($gs > 1):
		$guardNames[$gs - 2] .= ' und ' . $guardNames[$gs - 1];
		unset ($guardNames[$gs - 1]);
	endif;
endif;

$luxuries  = $region->Luxuries();
$offer     = $luxuries?->Offer();
$castle    = $intelligence->getCastle();
$hasMarket = $luxuries && $castle?->Size() > Site::MAX_SIZE;
if ($hasMarket):
	$demand = [];
	foreach ($luxuries as $luxury):
		$demand[] = $this->translate($luxury->Commodity()) . ' $' . $this->number($luxury->Price());
	endforeach;
endif;

?>
<p>
	<?php if ($landscape instanceof Navigable): ?>
		<?php if ($landscape instanceof Ocean && $name !== 'Ozean' || $landscape instanceof Lake && $name !== 'See'): ?>
			<?= $this->translate($landscape) ?>.
			<br>
		<?php endif ?>
	<?php else: ?>
		<?= $this->translate($landscape) ?>,
		<?= $this->item(Peasant::class, $resources) ?>,
		<?= $this->item(Silver::class, $resources) ?>. Der Arbeitslohn beträgt <?= $wage ?> Silber.
		<?php if ($r > 0): ?>
			<?= $recruits ?> <?= $r === 1 ? 'kann' : 'können' ?> rekrutiert werden.
		<?php endif ?>
		<?php if ($t && $m): ?>
			Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> gefällt sowie <?= $mining ?> abgebaut werden.
		<?php elseif ($t): ?>
			Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> gefällt werden.
		<?php elseif ($g || $o): ?>
			Hier <?= $g + $o === 1 ? 'kann' : 'können' ?> <?= $mining ?> abgebaut werden.
		<?php endif ?>
		<?php if ($a): ?>
			<?= $animals ?> <?= $a === 1 ? 'streift' : 'streifen' ?> durch die Wildnis.
		<?php endif ?>
		<?php if ($gr): ?>
			<?= $griffin ?> <?= $gr === 1 ? ' nistet ' : 'nisten' ?> in den Bergen.
		<?php endif ?>
		<br>
	<?php endif ?>
	<?= ucfirst(implode(', ', $neighbours)) ?>.
	<?= $this->template('description', $region) ?>
</p>
<?php if ($luxuries && $hasPeasants || $tr > 0 || $gs > 0): ?>
	<p>
		<?php if ($hasMarket && $hasPeasants): ?>
			Die Bauern produzieren <?= $this->things($offer->Commodity()) ?> und verlangen pro Stück $<?= $this->number($offer->Price()) ?>.
			Marktpreise für andere Waren: <?= implode(', ', $demand) ?>.<br>
		<?php elseif ($offer && $hasPeasants): ?>
			Die Bauern produzieren <?= $this->things($offer->Commodity()) ?>.<br>
		<?php endif ?>
		<?php if ($tr > 0): ?>
			<?= $this->template('treasury/region', $treasury) ?>
			<br>
		<?php endif ?>
		<?php if ($gs > 0): ?>
			Die Region wird bewacht von <?= ucfirst(implode(', ', $guardNames)) ?>.
		<?php endif ?>
	</p>
<?php endif ?>
