<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\View;

use function Lemuria\Renderer\Text\wrap;
use Lemuria\Engine\Message;
use Lemuria\Renderer\Text\View;

class Text extends View
{
	/**
	 * Render a report message.
	 */
	public function message(Message $message): string {
		return (string)$message;
	}

	/**
	 * Generate text output.
	 */
	protected function generateContent(): string {
		ob_start();
		$result = @include __DIR__ . '/../../templates/text.php';
		$output = ob_get_clean();
		if ($result && $output) {
			return wrap($output);
		}
		throw new \RuntimeException('Template error.');
	}
}
