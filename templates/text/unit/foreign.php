<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/* @var Text $this */

/** @var Unit $unit */
$unit    = $this->variables[0];
$census  = $this->census;
$prefix  = $unit->Construction() || $unit->Vessel() ? '   * ' : '  -- ';
$foreign = $census->getParty($unit);
if (!$foreign):
	$foreign = 'unbekannte Partei';
endif;
$disguised = $unit->Disguise();

?>
<?= $prefix . $unit ?> von <?= $foreign ?>, <?= $this->number($unit->Size(), 'race', $unit->Race()) ?>
<?php if ($unit->IsHiding()): ?>, getarnt<?php if ($disguised): ?>, gibt sich als Angehöriger der Partei <?= $disguised->Name() ?> aus<?php endif ?><?php if ($disguised === null): ?>, verheimlicht die Parteizugehörigkeit<?php endif ?><?php endif ?>
<?php if ($unit->IsGuarding()): ?>, bewacht die Region<?php endif ?>
.<?= description($unit) ?>

