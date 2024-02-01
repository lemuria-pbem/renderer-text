<?php
declare (strict_types = 1);

use Lemuria\Engine\Fantasya\Message\Casus;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Renderer\Text\View;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Construction $construction */
$construction = $this->variables[0];
$isMaintained = $this->isMaintained($construction);

$vessels = [];
foreach (View::sortedDockyardFleet($construction) as $vessel):
	$vessels[] = $this->combineGrammar($vessel->Ship(), 'ein', Casus::Nominative);
endforeach;
$v = count($vessels);

?>
<?php if ($isMaintained && $v > 0): ?>
<?php if ($v === 1): ?>
Hier ist <?= $vessels[0] ?> im Bau.
<?php else: ?>
Hier sind <?= $this->toAndString($vessels) ?> im Bau.
<?php endif ?>
<?php endif ?>
