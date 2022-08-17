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

<?= center('Botschaften') ?>
<?php foreach ($announcements as $announcement): ?>

Von: <?= $announcement->Sender() ?> · An: <?= $announcement->Recipient() ?>

<?= wrap('„' . $announcement->Message() . '“') ?>
<?php endforeach ?>
<?php endif ?>