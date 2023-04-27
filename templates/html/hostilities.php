<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$hostilities = [];
$locations   = [];
$links       = [];
$i           = 0;
foreach ($this->hostilities() as $battle):
	$participants = [];
	foreach ($battle->Participants() as $party):
		$participants[] = $party->Name() . ' <span class="badge text-bg-primary font-monospace">' . $party->Id() . '</span>';
	endforeach;

	/** @var Region $location */
	$location = $battle->Location();
	$name     = $location->Name() . ' <span class="badge text-bg-secondary font-monospace">' . $location->Id() . '</span>';

	$hostilities[$name] = $this->toAndString($participants);
	$locations[$name]   = $location;
	$links[]            = $this->battleLogPath($battle);
endforeach;

?>
<?php if (!empty($hostilities)): ?>
	<div class="col-12 p-0">
		<h3>Kampfberichte</h3>

		<?php foreach ($hostilities as $location => $participants): ?>
			In <a href="#<?= id($locations[$location]) ?>"><?= $location ?></a> gab es einen Kampf zwischen den Parteien <?= $participants ?>.
			<a href="<?= $links[$i++] ?>">Kampfbericht anzeigen</a>
			<br>
		<?php endforeach ?>
	</div>
<?php endif ?>
