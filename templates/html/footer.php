<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\dateTimeString;
use Lemuria\Lemuria;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\Version\Module;

/** @var Html $this */

$version  = Lemuria::Version();
$game     = $version[Module::Game][0] ?? null;
$versions = $this->gameVersions();

?>
<footer>
	<?php if ($game): ?>
		<p>
			<em>Version: <?= $game->name ?> <?= $game->version ?> (<?= implode(', ', $versions) ?>) | <?= dateTimeString($this->getCreatedTimestamp()) ?></em>
			<br>
			<em>Grafiken der Kartenansicht entnommen/bearbeitet aus Magellan (magellan.narabi.de)</em>
		</p>
	<?php endif ?>
</footer>
