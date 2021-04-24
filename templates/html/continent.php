<?php
declare (strict_types = 1);

use Lemuria\Model\Fantasya\Continent;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Continent $continent */
$continent = $this->variables[0];
$party     = $this->party;

?>
<h3><?= $continent->Name() ?> <span class="badge badge-primary"><?= $party->Id() ?></span></h3>

<blockquote class="blockquote"><?= $continent->Description() ?></blockquote>
