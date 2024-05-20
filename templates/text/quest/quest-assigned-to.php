<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Scenario\Quest;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Quest $quest */
$quest = $this->variables[0];
/** @var Unit|null $person */
$person = $this->variables[1];

?>
<?php if ($person && $quest->isAssignedTo($person)): ?>
<?= $person ?> hat den Auftrag übernommen.
<?php endif ?>

