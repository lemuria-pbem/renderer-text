<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Message\Casus;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$isMaintained = $this->isMaintained($construction);

$vessels = [];
foreach (View::sortedPortFleet($construction) as $vessel):
	$vessels[] = $this->combineGrammar($vessel->Ship(), 'das', Casus::Nominative) . ' „' . $vessel->Name() . '“';
endforeach;
$v = count($vessels);

?>
<?php if ($isMaintained && $v > 0): ?>
	<?php if ($v === 1): ?>
		<p>Im Hafen ankert <?= $vessels[0] ?>.</p>
	<?php else: ?>
		<p>Im Hafen ankern <?= $this->toAndString($vessels) ?>.</p>
	<?php endif ?>
<?php else: ?>
	<p>Hier liegen derzeit keine Schiffe vor Anker.</p>
<?php endif ?>
