<?php
declare(strict_types = 1);

use function Lemuria\number;
use Lemuria\Model\Fantasya\Herb;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

/** @var Region $region */
$region = $this->variables[0];
$quotas = $this->party->Regulation()->getQuotas($region);
$list   = [];

if ($quotas?->count() > 0) {
	foreach ($quotas as $quota) {
		$commodity = $quota->Commodity();
		$item      = $commodity instanceof Herb ? $this->get('kind.Herb') : $this->translateSingleton($commodity);
		$threshold = $quota->Threshold();
		$list[]    = $item . ' ' . (is_float($threshold) ? (int)(100 * $threshold) . '%' : number($threshold));
	}
}

?>
<?php if (!empty($list)): ?>
 Grenzen: <?= implode(', ', $list) ?>
.<?php endif ?>
<?php if (isset($this->variables[1])): ?>

<?php endif ?>
