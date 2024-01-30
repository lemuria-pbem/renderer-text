<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Scenario\Quest;
use Lemuria\Renderer\Text\View\Text;
use Lemuria\Scenario\Fantasya\Quest\Controller\SellUnicum;

/** @var Text $this */

/** @var Quest $quest */
$quest = $this->variables[0];
$unit  = $quest->Unit();
/** @var SellUnicum $controller */
$controller  = $quest->Controller()->setPayload($quest);
$unicum      = $controller->Unicum();
$composition = $unicum->Composition();

?>
[<?= $quest->Id() ?>] Ankauf eines Unikats - von <?= $unit ?>

<?= $unit->Name() ?> bietet uns <?= $this->resource($controller->Payment()) ?> für <?= $this->combineGrammar($composition, 'unser') ?> „<?= $unicum->Name() ?>“ [<?= $unicum->Id() ?>].

