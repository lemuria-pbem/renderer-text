<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\View;

use Lemuria\Renderer\Text\View;

class Html extends View
{
	/**
	 * Generate HTML output.
	 *
	 * @return string
	 */
	protected function generateContent(): string {
		ob_start();
		$result = @include __DIR__ . '/../../templates/html.php';
		$output = ob_get_clean();
		if ($result && $output) {
			return $output;
		}
		throw new \RuntimeException('Template error.');
	}
}
