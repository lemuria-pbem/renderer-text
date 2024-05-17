<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Scenario\Quest;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\Scenario\Fantasya\Quest\Controller\Instructor;

/** @var Html $this */

/** @var Quest $quest */
$quest = $this->variables[0];
$unit  = $quest->Owner();
/** @var Instructor $controller */
$controller = $quest->Controller()->setPayload($quest);
$knowledge  = $controller->Knowledge();
$talents    = [];
foreach ($knowledge as $ability):
	$talents[] = $this->get('talent', $ability->Talent()) . ' (Stufe ' . $ability->Level() . ')';
endforeach;

?>
<strong>Lehrer</strong> von <a href="#unit-<?= $unit->Id() ?>"><?= $unit->Name() ?></a>&nbsp;<span class="badge text-bg-primary font-monospace"><a href="#unit-<?= $unit->Id() ?>"><?= $unit->Id() ?></a></span>
<br>
<?= $unit->Name() ?> kann uns in <?= $this->toAndString($talents) ?> lehren.
