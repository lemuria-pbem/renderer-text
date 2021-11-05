<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Continent $continent */
$continent = $this->variables[0];

?>
<?= center($continent->Name()) ?>

<?= wordwrap($continent->Description()) ?>
