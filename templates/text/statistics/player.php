<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\line;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party  = $this->party;
$census = $this->census;
$banner = $party->Banner() ? 'Unser Banner: ' . $party->Banner() : '(kein Banner gesetzt)';

?>
Dein Volk: <?= $party->Name() ?> [<?= $party->Id() ?>]

<?= line($party->Description()) ?>

<?= line($banner) ?>

Dein Volk zählt <?= $this->number($census->count(), 'race', $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.
Deine Einheiten sammeln <?= $this->loot() ?>.