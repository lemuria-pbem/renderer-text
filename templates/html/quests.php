<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Html;
use Lemuria\Model\Fantasya\Extension\QuestsWithPerson;
use Lemuria\Model\Fantasya\Scenario\Quest;

/** @var Html $this */

$extensions = $this->party->Extensions();
/** @var QuestsWithPerson $quests */
$quests = $extensions[QuestsWithPerson::class] ?? [];

?>
<?php if (!empty($quests)): ?>
	<h3 id="quests" title="Taste: Q">Aufträge</h3>

	<div class="quests">
		<?php foreach ($quests as $quest /** @var Quest $quest */): ?>
			<p>
				<span class="badge text-bg-quest font-monospace"><?= $quest->Id() ?></span>
				<?= $this->template($this->controller($quest), $quest, $quests->getPerson($quest)) ?>
			</p>
		<?php endforeach ?>
	</div>
<?php endif ?>
