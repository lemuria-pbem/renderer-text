<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region    = $this->variables[0];
$realm     = $region->Realm();
$central   = $realm?->Territory()->Central();
$isCentral = $region === $central;

?>
<?php if ($realm): ?>
	<?php if ($isCentral): ?>
		<p>Zentralregion des Reiches <?= $realm->Name() ?>.</p>
	<?php else: ?>
		<p>
			Die Region gehört zum Reich
			<a href="#<?= id($central) ?>"><?= $realm->Name() ?></a>.
		</p>
	<?php endif ?>
<?php endif ?>
