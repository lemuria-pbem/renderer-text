<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Scenario\Quest;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\Scenario\Fantasya\Quest\Controller\SellUnicum;

/** @var Html $this */

/** @var Quest $quest */
$quest = $this->variables[0];
$unit  = $quest->Unit();
/** @var SellUnicum $controller */
$controller  = $quest->Controller()->setPayload($quest);
$unicum      = $controller->Unicum();
$composition = $unicum->Composition();

?>
<strong>Ankauf eines Unikats</strong> von <a href="#unit-<?= $unit->Id() ?>"><?= $unit->Name() ?></a>&nbsp;<span class="badge text-bg-primary font-monospace"><a href="#unit-<?= $unit->Id() ?>"><?= $unit->Id() ?></a></span>
<br>
<?= $unit->Name() ?> bietet uns <strong><?= $this->resource($controller->Payment()) ?></strong> für <?= $this->combineGrammar($composition, 'unser') ?> „<?= $unicum->Name() ?>“ <span class="badge text-bg-magic font-monospace"><?= $unicum->Id() ?></span>.
