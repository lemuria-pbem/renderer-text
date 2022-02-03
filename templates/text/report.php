<?php
declare (strict_types = 1);

use Lemuria\Renderer\Text\View\Text;

/** @var Text $this */

$report = $this->messages($this->variables[0]);

?>
<?php if ($report): ?>

<?php foreach ($report as $message): ?>
<?= $this->message($message) ?>
<?php endforeach ?>
<?php endif ?>