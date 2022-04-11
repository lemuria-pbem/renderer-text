<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\View;

use JetBrains\PhpStorm\Pure;

use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Entity;
use Lemuria\Engine\Message;
use Lemuria\Identifiable;
use Lemuria\Lemuria;
use Lemuria\Renderer\Text\Statistics\Data\TextNumber;
use Lemuria\Renderer\Text\View;
use Lemuria\Statistics\Data\Number;
use Lemuria\Version;

/**
 * Create a description line.
 */
function description(Entity $entity): string {
	if ($entity->Description()) {
		$description = ' ' . trim($entity->Description());
		if (!str_ends_with($description, '.')) {
			$description .= '.';
		}
		return $description;
	}
	return '';
}

/**
 * Create a centered line.
 */
#[Pure] function center(string $output): string {
	$columns = 80;
	$length  = mb_strlen($output);
	if ($length >= $columns) {
		return line($output);
	}
	$padding = (int)(($columns - $length) / 2);
	return line(str_repeat(' ', $padding) . $output);
}

/**
 * Create a horizontal line.
 */
#[Pure] function hr(int $length = 80): string {
	return line(str_pad('', $length, '-'));
}

/**
 * Create an output line terminated by EOL.
 */
#[Pure] function line(string $output): string {
	return $output . PHP_EOL;
}

/**
 * Output a line and underline it.
 */
#[Pure] function underline(string $line): string {
	return $line . PHP_EOL . hr(mb_strlen($line));
}

/**
 * Take text and wrap lines that are too long.
 */
#[Pure] function wrap(string $output): string {
	$wrapped = '';
	foreach (explode(PHP_EOL, $output) as $line) {
		$wrapped .= wordwrap($line, 80) . PHP_EOL;
	}
	return $wrapped;
}

/**
 * Create the footer.
 */
function footer(array $versions): string {
	$footer  = str_pad('', 80, '-');
	$version = Lemuria::Version();
	if (isset($version[Version::GAME])) {
		$game    = $version[Version::GAME][0];
		$footer .= PHP_EOL . 'Version: ' . $game->name . ' ' . $game->version . ' (';
		$footer .= implode(', ', $versions);
		$footer .= ') | ' . date('d.m.Y H:i:s');
	}
	return $footer;
}

class Text extends View
{
	/**
	 * Render a template.
	 */
	public function template(string $name, mixed ...$variables): string {
		$this->variables = $variables;
		return $this->generateUnwrappedContent($name);
	}

	public function wrappedTemplate(string $name, ...$variables): string {
		$this->variables = $variables;
		$content = $this->generateContent($name);
		if (strlen($content) < 5 && empty(trim($content))) {
			return PHP_EOL;
		}
		return $content;
	}

	/**
	 * Render a report message.
	 */
	#[Pure] public function message(Message $message): string {
		return wrap((string)$message);
	}

	public function numberStatistics(Subject $subject, Identifiable $entity): TextNumber {
		$data = $this->statistics($subject, $entity);
		if (!($data instanceof Number)) {
			$data = new Number();
		}
		return new TextNumber($data);
	}

	/**
	 * Generate text output.
	 */
	protected function generateContent(string $template): string {
		$output = $this->generateUnwrappedContent($template);
		return $output ? wrap($output) : $output;
	}

	/**
	 * Generate text output.
	 */
	private function generateUnwrappedContent(string $template): string {
		ob_start();
		/** @noinspection PhpIncludeInspection */
		$result = @include __DIR__ . '/../../templates/text/' . $template . '.php';
		$output = ob_get_clean();
		if ($result) {
			return $output;
		}
		throw new \RuntimeException('Template error.' . ($output ? PHP_EOL . $output : ''));
	}
}
