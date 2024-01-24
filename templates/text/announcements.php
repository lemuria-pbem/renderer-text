<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$announcements = $this->announcements();

?>
<?php if ($announcements): ?>

<?= hr() ?>

<?= center('Nachrichten und Botschaften') ?>
<?php foreach ($announcements as $announcement): ?>

<?php if ($announcement->From()): ?>
Von: <?= $announcement->Sender() ?> [<?= $announcement->From() ?>] · An: <?= $announcement->Recipient() ?> [<?= $announcement->To() ?>]
<?php else: ?>
Von: <?= $announcement->Sender() ?> · An: <?= $announcement->Recipient() ?> [<?= $announcement->To() ?>]
<?php endif ?>
<?= wrap('„' . $announcement->Message() . '“') ?>
<?php endforeach ?>
<?php endif ?>