<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\View;

use function Lemuria\endsWith;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Entity;
use Lemuria\Engine\Message;
use Lemuria\Identifiable;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Resources;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Text\Statistics\Data\TextMaterial;
use Lemuria\Renderer\Text\Statistics\Data\TextNumber;
use Lemuria\Renderer\Text\View;
use Lemuria\Statistics\Data\Number;
use Lemuria\Version\Module;

/**
 * Create a description line.
 */
function description(Entity $entity): string {
	if ($entity->Description()) {
		$description = ' ' . trim($entity->Description());
		if (!endsWith($description, ['.', '!', '?'])) {
			$description .= '.';
		}
		return $description;
	}
	return '';
}

/**
 * Create a centered line.
 */
function center(string $output): string {
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
function hr(int $length = 80): string {
	return line(str_pad('', $length, '-'));
}

/**
 * Create an output line terminated by EOL.
 */
function line(string $output): string {
	return $output . PHP_EOL;
}

/**
 * Output a line and underline it.
 */
function underline(string $line): string {
	return $line . PHP_EOL . hr(mb_strlen($line));
}

/**
 * Take text and wrap lines that are too long.
 */
function wrap(string $output): string {
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
	if (isset($version[Module::Game->value])) {
		$game    = $version[Module::Game->value][0];
		$footer .= PHP_EOL . 'Version: ' . $game->name->value . ' ' . $game->version . ' (';
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
	public function message(Message $message): string {
		return wrap((string)$message);
	}

	public function numberStatistics(Subject $subject, Identifiable $entity, string $name, string $unit = ''): TextNumber {
		$data = $this->statistics($subject, $entity);
		if (!($data instanceof Number)) {
			$data = new Number();
		}
		return new TextNumber($data, $name, $unit);
	}

	public function numberStatisticsOrNull(Subject $subject, Identifiable $entity, string $name): ?TextNumber {
		$data = $this->statistics($subject, $entity);
		if (!($data instanceof Number) || !$data->value && !$data->change) {
			return null;
		}
		return new TextNumber($data, $name);
	}

	/**
	 * @return array(string=>TextNumber)
	 */
	public function materialPoolStatistics(Subject $subject, Party $party): array {
		$statistics  = array_fill_keys(Resources::getAll(), null);
		$commodities = $this->statistics($subject, $party);
		if ($commodities) {
			foreach ($commodities as $class => $number) {
				$name               = $this->get('resource.' . $class, 1);
				$statistics[$class] = new TextNumber($number, $name);
			}
		}
		foreach (array_keys($statistics) as $class) {
			if (!$statistics[$class]) {
				unset($statistics[$class]);
			}
		}
		return $statistics;
	}

	/**
	 * @return TextMaterial[]
	 */
	public function regionPoolStatistics(Subject $subject, Unit $unit): array {
		$statistics  = array_fill_keys(Resources::getAll(), null);
		$commodities = $this->statistics($subject, $unit);
		if ($commodities) {
			foreach ($commodities as $class => $number /* @var Number $number */) {
				if ($number->value > 0) {
					$statistics[$class] = new TextMaterial($number, $class, $this);
				}
			}
		}
		foreach (array_keys($statistics) as $class) {
			if (!$statistics[$class]) {
				unset($statistics[$class]);
			}
		}
		return array_values($statistics);
	}

	/**
	 * @return array(string=>TextNumber)
	 */
	public function expertsStatistics(Subject $subject, Party $party): array {
		$statistics = [];
		$experts    = $this->statistics($subject, $party);
		if ($experts) {
			foreach ($experts as $class => $number) {
				$name              = $this->get('talent.' . $class);
				$statistics[$name] = new TextNumber($number, $name);
			}
		}
		ksort($statistics);
		return $statistics;
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
		throw new \RuntimeException('Template error.' . ($output ? PHP_EOL . $output : ''));
	}
}
