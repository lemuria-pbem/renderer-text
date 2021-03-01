<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\View;

use Lemuria\Engine\Message;
use Lemuria\Renderer\Text\View;

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
