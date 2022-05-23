<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\line;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$party  = $this->party;
$banner = $this->party->Banner() ? 'Parteibanner: ' . $this->party->Banner() : '(kein Banner gesetzt)';
$census = $this->census;

?>
Partei: <?= $party->Name() ?> [<?= $party->Id() ?>]

<?= line($party->Description()) ?>

<?= line($banner) ?>

Die Partei besteht aus insgesamt <?= $this->number($census->count()) ?> Individuen in <?= $this->number($party->People()->count()) ?> Einheiten.
