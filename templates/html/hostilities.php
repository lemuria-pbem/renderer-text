<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$hostilities = [];
$names       = [];
$locations   = [];
$links       = [];
$i           = 0;
foreach ($this->hostilities() as $battle):
	$key          = [(string)$battle->Location()->Id(), (string)$battle->Counter()];
	$participants = [];
	foreach ($battle->Participants() as $party):
		$key[]          = (string)$party->Id();
		$participants[] = $party->Name() . ' <span class="badge text-bg-primary font-monospace">' . $party->Id() . '</span>';
	endforeach;

	/** @var Region $location */
	$location = $battle->Location();
	$name     = $location->Name() . ' <span class="badge text-bg-secondary font-monospace">' . $location->Id() . '</span>';

	$key               = implode('-', $key);
	$names[$key]       = $name;
	$hostilities[$key] = $this->toAndString($participants);
	$locations[$key]   = $location;
	$links[]           = $this->battleLogPath($battle);
endforeach;

?>
<?php if (!empty($hostilities)): ?>
	<div class="col-12 p-0">
		<h3>Kampfberichte</h3>

		<?php foreach ($hostilities as $key => $participants): ?>
			In <a href="#<?= id($locations[$key]) ?>"><?= $names[$key] ?></a> gab es einen Kampf zwischen den Parteien <?= $participants ?>.
			<a href="<?= $links[$i] ?>" target="battle-<?= $i++ ?>">Kampfbericht anzeigen</a>
			<br>
		<?php endforeach ?>
	</div>
<?php endif ?>
