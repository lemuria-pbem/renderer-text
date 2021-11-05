<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\linkEmail;
use Lemuria\Renderer\Text\View\Html;

/** @var Html $this */

$party  = $this->party;
$census = $this->census;
$count  = $census->count();
$banner = $party->Banner() ? 'Parteibanner: ' . linkEmail($party->Banner()) : '(kein Banner gesetzt)';

?>
<h2><?= $party->Name() ?> <span class="badge badge-primary"><?= $party->Id() ?></span></h2>

<blockquote class="blockquote"><?= $party->Description() ?></blockquote>

<p><?= $banner ?></p>

<p>Die Partei besteht aus insgesamt <?= $this->number($count) ?> Individuen in <?= $this->number($party->People()->count()) ?> Einheiten.</p>

<?php if ($count > 0): ?>
<ul>
	<?php foreach ($this->races($party) as $race): ?>
		<li><?= $this->number($race['persons'], 'race', $race['race']) ?> in <?= $this->number($race['units']) ?> Einheiten</li>
	<?php endforeach ?>
</ul>
<?php endif ?>
