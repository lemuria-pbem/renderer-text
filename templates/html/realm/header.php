<?php
declare (strict_types = 1);

use function Lemuria\number;
use function Lemuria\Renderer\Text\View\id;
use Lemuria\Engine\Fantasya\State;
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
		<p>
			Zentralregion des Reiches <?= $realm->Name() ?> <span class="badge text-bg-light font-monospace"><?= $realm->Identifier() ?></span>. Maximale Transportkapazität: <?= number(State::getInstance()->getRealmFleet($realm)->Incoming()) ?> GE.
			<?= $this->template('description', $realm) ?>
		</p>
	<?php else: ?>
		<p>
			Die Region gehört zum Reich
			<a href="#<?= id($central) ?>"><?= $realm->Name() ?></a> <span class="badge text-bg-light font-monospace"><?= $realm->Identifier() ?></span>.
		</p>
	<?php endif ?>
<?php endif ?>
