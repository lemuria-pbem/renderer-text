<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Combat\BattleLog;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$hostilities = [];
foreach ($this->hostilities() as $battle /* @var BattleLog $battle */):
	$participants = [];
	foreach ($battle->Participants() as $party):
		$participants[] = $party->Name() . ' <span class="badge badge-primary">' . $party->Id() . '</span>';
	endforeach;

	/** @var Region $location */
	$location = $battle->Location();
	$name     = $location->Name() . ' <span class="badge badge-secondary">' . $location->Id() . '</span>';

	$hostilities[$name] = $this->toAndString($participants);
endforeach;

?>
<?php if (!empty($hostilities)): ?>
<h3>Kampfberichte</h3>

<?php foreach ($hostilities as $location => $participants): ?>
In <?= $location ?> gab es einen Kampf zwischen den Parteien <?= $participants ?>.<br>
<?php endforeach ?>
<?php endif ?>
