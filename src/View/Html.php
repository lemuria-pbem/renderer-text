<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\View;

use Lemuria\Engine\Message;
use Lemuria\Renderer\Text\View;

/**
 * Replace email address with a mailto link.
 */
function linkEmail(string $input): string {
	if (preg_match('/\b([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,})\b/i', $input, $matches) === 1) {
		$n = count($matches);
		for ($i = 1; $i < $n; $i++) {
			$e     = $matches[$i];
			$input = str_replace($e, '<a href="mailto:' . $e . '">' . $e . '</a>', $input);
		}
	}
	return $input;
}

class Html extends View
{
	protected const BADGE_UNDEFINED = 'dark';

	protected const BADGE = [
		Message::DEBUG   => 'light',
		Message::ERROR   => 'danger',
		Message::EVENT   => 'info',
		Message::FAILURE => 'warning',
		Message::SUCCESS => 'success'
	];

	protected const LEVEL_UNDEFINED = 'U';

	protected const LEVEL = [
		Message::DEBUG => 'D', Message::ERROR => 'F', Message::EVENT => 'E', Message::FAILURE => 'W', Message::SUCCESS => 'M'
	];

	/**
	 * Render a template.
	 */
	public function template(string $name, ...$variables): string {
		$this->variables = $variables;
		return $this->generateContent($name);
	}

	/**
	 * Render a report message.
	 */
	public function message(Message $message): string {
		$level = $message->Level();
		$badge = self::BADGE[$level] ?? self::BADGE_UNDEFINED;
		$b     = self::LEVEL[$level] ?? self::LEVEL_UNDEFINED;
		return '<span class="badge badge-' . $badge . ' text-monospace">' . $b . '</span>&nbsp;' . $message;
	}

	/**
	 * Generate HTML output.
	 */
	protected function generateContent(string $template): string {
		ob_start();
		$result = @include __DIR__ . '/../../templates/html/' . $template . '.php';
		$output = ob_get_clean();
		if ($result) {
			return $output;
		}
		throw new \RuntimeException('Template error.');
	}
}
