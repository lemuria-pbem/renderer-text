<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Continent $continent */
$continent = $this->variables[0];

?>
<h3 id="<?= id($continent) ?>">
	<?= $continent->Name() ?>
</h3>

<blockquote class="blockquote"><?= $continent->Description() ?></blockquote>
