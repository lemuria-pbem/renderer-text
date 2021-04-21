<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\View;

use JetBrains\PhpStorm\Pure;

use Lemuria\Entity;
use Lemuria\Engine\Message;
use Lemuria\Renderer\Text\View;

/**
 * Create a description line.
 */
#[Pure] function description(Entity $entity): string {
	if ($entity->Description()) {
		$description = ' ' . trim($entity->Description());
		if (substr($description, strlen($description) - 1) !== '.') {
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
#[Pure] function hr(): string {
	return line(str_pad('', 80, '-'));
}

/**
 * Create an output line terminated by EOL.
 */
#[Pure] function line(string $output): string {
	return $output . PHP_EOL;
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
#[Pure] function footer(): string {
	return str_pad('', 80, '-');
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
		return $this->generateContent($name);
	}

	/**
	 * Render a report message.
	 */
	public function message(Message $message): string {
		return wrap((string)$message);
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
		$result = @include __DIR__ . '/../../templates/text/' . $template . '.php';
		$output = ob_get_clean();
		if ($result) {
			return $output;
		}
		throw new \RuntimeException('Template error.');
	}
}
