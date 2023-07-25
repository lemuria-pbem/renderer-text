<?php
declare(strict_types = 1);

use function Lemuria\number;
use Lemuria\Model\Fantasya\Herb;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

/** @var Region $region */
$region = $this->variables[0];
$quotas = $this->party->Regulation()->getQuotas($region);
$list   = [];

if ($quotas?->count() > 0) {
	foreach ($quotas as $quota) {
		$commodity   = $quota->Commodity();
		$item        = $commodity instanceof Herb ? $this->get('kind.Herb') : $this->translateSingleton($commodity);
		$threshold   = $quota->Threshold();
		$list[$item] = is_float($threshold) ? (int)(100 * $threshold) . ' %' : number($threshold);
	}
}

?>
<?php if (!empty($list)): ?>
	<h5>Grenzen</h5>

	<ul>
		<?php foreach ($list as $item => $threshold): ?>
			<li><?= $item ?>: <?= $threshold ?></li>
		<?php endforeach ?>
	</ul>
<?php endif ?>
