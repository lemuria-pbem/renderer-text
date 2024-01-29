<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\line;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$party  = $this->party;
$census = $this->census;
$banner = $party->Banner() ? 'Unser Banner: ' . $party->Banner() : '(kein Banner gesetzt)';

?>
Dein Volk: <?= $party->Name() ?> [<?= $party->Id() ?>]

<?= line($party->Description()) ?>

<?= line($banner) ?>

Dein Volk zählt <?= $this->number($census->count(), $party->Race()) ?> in <?= $this->number($party->People()->count()) ?> Einheiten.
<?= wrap('Deine Einheiten sammeln ' . $this->loot() . '.') ?>
<?= wrap('Vorgaben für neue Einheiten: ' . implode(', ', $this->presettings()) . '.') ?>
<?= wrap('Vorgaben für neue Handelsangebote: ' . ($party->Presettings()->IsRepeat() ? 'WIEDERHOLEN' : 'WIEDERHOLEN Nicht') . '.') ?>
<?= wrap('Vorgabe für Kundschafter/Kapitäne: ' . $this->dictionary->get('presetting.exploring', $this->party->Presettings()->Exploring()->name) . '.') ?>
