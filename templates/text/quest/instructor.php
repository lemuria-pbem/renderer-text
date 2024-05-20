<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Scenario\Quest;
use Lemuria\Renderer\Text\View\Text;
use Lemuria\Scenario\Fantasya\Quest\Controller\Instructor;

/** @var Text $this */

/** @var Quest $quest */
$quest  = $this->variables[0];
$person = $this->variables[1];
$unit   = $quest->Owner();
/** @var Instructor $controller */
$controller = $quest->Controller()->setPayload($quest);
$knowledge  = $controller->Knowledge();
$talents    = [];
foreach ($knowledge as $ability):
	$talents[] = $this->get('talent', $ability->Talent()) . ' (Stufe ' . $ability->Level() . ')';
endforeach;

?>
[<?= $quest->Id() ?>] Lehrer - von <?= $unit ?>

<?= $unit->Name() ?> kann uns in <?= $this->toAndString($talents) ?> lehren.
<?= $this->template('quest/quest-assigned-to', $quest, $person) ?>
