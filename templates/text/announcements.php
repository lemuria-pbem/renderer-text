<?php
declare (strict_types = 1);

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$announcements = $this->announcements();

?>
<?php if ($announcements): ?>

<?= hr() ?>

<?= center('Botschaften') ?>

<?php foreach ($announcements as $message): ?>
<?= $this->message($message) ?>
<?php endforeach ?>
<?php endif ?>