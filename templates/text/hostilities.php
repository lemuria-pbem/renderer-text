<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$hostilities = [];
foreach ($this->hostilities() as $battle):
	$participants = [];
	foreach ($battle->Participants() as $party):
		$participants[] = (string)$party;
	endforeach;
	$hostilities[(string)$battle->Location()] = $this->toAndString($participants);
endforeach;

?>
<?php if (!empty($hostilities)): ?>

<?= hr() ?>

<?= center('Kampfberichte') ?>

<?php foreach ($hostilities as $location => $participants): ?>
In <?= $location ?> gab es einen Kampf zwischen den Parteien <?= $participants ?>.
<?php endforeach ?>
<?php endif ?>
