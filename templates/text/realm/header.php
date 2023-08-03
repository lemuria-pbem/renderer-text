<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\description;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Region $region */
$region    = $this->variables[0];
$realm     = $region->Realm();
$isCentral = $region === $realm?->Territory()->Central();

?>
<?php if ($realm): ?>
, <?= $realm->Name() ?>
<?php if ($isCentral): ?>
 (Zentrale).<?= description($realm) ?><?php else: ?>,<?php endif ?>
<?php endif ?>
