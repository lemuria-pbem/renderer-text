<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use Lemuria\Model\Fantasya\Extension\QuestsWithPerson;
use Lemuria\Model\Fantasya\Scenario\Quest;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$extensions = $this->party->Extensions();
/** @var QuestsWithPerson $quests */
$quests = $extensions[QuestsWithPerson::class] ?? [];

?>
<?php if (!empty($quests)): ?>
<?= center('Aufträge') ?>

<?php foreach ($quests as $quest /** @var Quest $quest */): ?>
<?= $this->template($this->controller($quest), $quest, $quests->getPerson($quest)) ?>
<?php endforeach ?>
<?php endif ?>
