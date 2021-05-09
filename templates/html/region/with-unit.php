<?php
declare (strict_types = 1);

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
use Lemuria\Model\Fantasya\Landscape\Ocean;
use Lemuria\Model\Fantasya\Offer;
use Lemuria\Model\Fantasya\Quantity;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region     = $this->variables[0];
$party      = $this->party;
$atlas      = $this->atlas;
$map        = $this->map;
$landscape  = $region->Landscape();
$resources  = $region->Resources();
$neighbours = $this->neighbours($region);

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

$luxuries = null;
$castle   = $intelligence->getGovernment();
if ($castle?->Size() > Site::MAX_SIZE):
	$luxuries = $region->Luxuries();
	if ($luxuries):
		$offer  = $luxuries->Offer();
		$demand = [];
		foreach ($luxuries as $luxury /* @var Offer $luxury */):
			$demand[] = $this->get('resource', $luxury->Commodity()) . ' $' . $this->number($luxury->Price());
		endforeach;
	endif;
endif;

?>
<p>
	<?php if ($landscape instanceof Ocean): ?>
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
		<br>
	<?php endif ?>
	<?= ucfirst(implode(', ', $neighbours)) ?>.
	<?php if ($region->Description()): ?>
		<br>
		<?= $region->Description() ?>
	<?php endif ?>
</p>
<?php if ($luxuries || $g > 0): ?>
	<p>
		<?php if ($luxuries): ?>
			Die Bauern produzieren <?= $this->things($offer->Commodity()) ?> und verlangen pro Stück $<?= $this->number($offer->Price()) ?>.
			Marktpreise für andere Waren: <?= implode(', ', $demand) ?>.
		<?php endif ?>
		<?php if ($g > 0): ?>
			<?php if ($luxuries): ?>
				<br>
			<?php endif ?>
			Die Region wird bewacht von <?= ucfirst(implode(', ', $guardNames)) ?>.
		<?php endif ?>
	</p>
<?php endif ?>

