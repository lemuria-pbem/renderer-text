<?php
/** @noinspection PhpUndefinedVariableInspection */
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use function Lemuria\Renderer\Text\View\line;
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
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Region $region */
$region     = $this->variables[0];
$party      = $this->party;
$census     = $this->census;
$outlook    = $this->outlook;
$atlas      = $this->atlas;
$map        = $this->map;
$landscape  = $region->Landscape();
$resources  = $region->Resources();
$neighbours = $this->neighbours($region);
$treasury   = $region->Treasury();

$t       = $resources[Wood::class]->Count();
$g       = $resources[Stone::class]->Count();
$o       = $resources[Iron::class]->Count();
$m       = $g && $o;
$trees   = $this->item(Wood::class, $resources);
$granite = $this->item(Stone::class, $resources);
$ore     = $this->item(Iron::class, $resources);
$mining  = null;
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
	foreach ($guards as $unit /* @var Unit $unit */):
		$guardNames[] = $unit->Name();
	endforeach;
	if ($gs > 1):
		$guardNames[$gs - 2] .= ' und ' . $guardNames[$gs - 1];
		unset ($guardNames[$gs - 1]);
	endif;
endif;

$luxuries  = $region->Luxuries();
$offer     = $luxuries?->Offer();
$castle    = $intelligence->getGovernment();
$hasMarket = $luxuries && $castle?->Size() > Site::MAX_SIZE;
if ($hasMarket):
	$demand = [];
	foreach ($luxuries as $luxury /* @var Offer $luxury */):
		$demand[] = $this->get('resource', $luxury->Commodity()) . ' $' . $this->number($luxury->Price());
	endforeach;
endif;

?>
<?php if ($landscape instanceof Ocean): ?>
<?php if ($region->Name() === 'Ozean'): ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>.
<?php else: ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>, <?= $this->get('landscape', $region->Landscape()) ?>.
<?php endif ?>
<?php else: ?>
>> <?= $region ?> <?= $map->getCoordinates($region) ?>, <?= $this->get('landscape', $region->Landscape()) ?>, <?= $this->item(Peasant::class, $resources) ?>, <?= $this->item(Silver::class, $resources) ?>
.<?php if ($r > 0): ?> <?= $recruits ?> <?= $r === 1 ? 'kann' : 'können' ?> rekrutiert werden.<?php endif ?>
<?php if ($t && $m): ?>
 Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> geerntet sowie <?= $mining ?> abgebaut werden.<?php
elseif ($t): ?>
 Hier <?= $t === 1 ? 'kann' : 'können' ?> <?= $trees ?> geerntet werden.<?php
elseif ($g || $o): ?>
 Hier <?= $g + $o === 1 ? 'kann' : 'können' ?> <?= $mining ?> abgebaut werden.<?php
endif ?><?php if ($a): ?> <?= $animals ?> <?= $a === 1 ? 'streift' : 'streifen' ?> durch die Wildnis.<?php
endif ?><?php if ($gr): ?> <?= $griffin ?> <?= $gr === 1 ? ' nistet ' : 'nisten' ?> in den Bergen.<?php
endif ?>
<?php endif ?>

<?= ucfirst(implode(', ', $neighbours)) ?>
.<?= line(description($region)) ?>
<?php if ($hasMarket && $hasPeasants): ?>
Die Bauern produzieren <?= $this->things($offer->Commodity()) ?> und verlangen pro Stück $<?= $this->number($offer->Price()) ?>
. Marktpreise für andere Waren: <?= implode(', ', $demand) ?>.
<?php elseif ($offer && $hasPeasants): ?>
Die Bauern produzieren <?= $this->things($offer->Commodity()) ?>.
<?php endif ?>
<?php if (!$treasury->isEmpty()): ?><?= $this->template('treasury/region', $treasury) ?><?php endif ?>
<?php if ($gs > 0): ?>
Die Region wird bewacht von <?= ucfirst(implode(', ', $guardNames)) ?>.
<?php endif ?>
