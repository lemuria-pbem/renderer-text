<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Unit $unit */
$unit = $this->variables[0];
$prefix    = $unit->Construction() || $unit->Vessel() ? '   * ' : '  -- ';
$aura      = $unit->Aura();
$disguised = $unit->Disguise();
$calculus  = new Calculus($unit);
$hitpoints = $calculus->hitpoints();
$health    = (int)floor($unit->Health() * $hitpoints);

?>
<?= $prefix . $unit ?>, <?= $this->number($unit->Size(), $unit->Race()) ?>
<?php if ($aura): ?>, Aura <?= $aura->Aura() ?>/<?= $aura->Maximum() ?><?php endif ?>
, <?= $this->battleRow($unit) ?>, <?= $this->health($unit) ?> (<?= $health ?>/<?= $hitpoints ?>)<?php if ($unit->IsHiding()): ?>, getarnt<?php endif ?>
<?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?>
<?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?>
<?php if (!$unit->IsLooting()): ?>, sammelt nicht<?php endif ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

Die Einheit ist mit dem Bau des Schiffes beschäftigt.
