<?php
declare (strict_types = 1);

use Lemuria\Lemuria;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\Version;

/** @var Html $this */

$version  = Lemuria::Version();
$game     = $version[Version::GAME][0] ?? null;
$versions = $this->gameVersions();

?>
<footer>
	<?php if ($game): ?>
		<p>
			<em>Version: <?= $game->name ?> <?= $game->version ?> (<?= implode(', ', $versions) ?>) | <?= date('d.m.Y H:i:s') ?></em>
		</p>
	<?php endif ?>
</footer>
