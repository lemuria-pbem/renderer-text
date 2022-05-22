<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Combat\BattleLog;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$hostilities = [];
$links       = [];
foreach ($this->hostilities() as $battle /* @var BattleLog $battle */):
	$participants = [];
	foreach ($battle->Participants() as $party):
		$participants[] = $party->Name() . ' <span class="badge badge-primary">' . $party->Id() . '</span>';
	endforeach;

	/** @var Region $location */
	$location = $battle->Location();
	$name     = $location->Name() . ' <span class="badge badge-secondary">' . $location->Id() . '</span>';

	$hostilities[$name] = $this->toAndString($participants);
	$links[]            = $this->battleLogPath($battle);
	$i                  = 0;
endforeach;

?>
<?php if (!empty($hostilities)): ?>
	<div class="col-12 col-lg-6 p-0 pr-lg-3">
		<h3>Kampfberichte</h3>

		<?php foreach ($hostilities as $location => $participants): ?>
		In <?= $location ?> gab es einen Kampf zwischen den Parteien <?= $participants ?>.
		<a href="<?= $links[$i++] ?>">Kampfbericht anzeigen</a>
		<br>
		<?php endforeach ?>
	</div>
<?php endif ?>
