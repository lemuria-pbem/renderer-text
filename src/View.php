<?php
declare (strict_types = 1);
namespace Lemuria\Renderer\Text;

use JetBrains\PhpStorm\Pure;

use function Lemuria\getClass;
use function Lemuria\number as formatNumber;
use Lemuria\Engine\Message;
use Lemuria\Engine\Message\Filter;
use Lemuria\Entity;
use Lemuria\Identifiable;
use Lemuria\ItemSet;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Party\Census;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\World\PartyMap;
use Lemuria\Singleton;

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
 * A view object that contains variables and helper functions for view scripts.
 */
abstract class View
{
	public Census $census;

	public PartyMap $map;

	protected Dictionary $dictionary;

	public function __construct(public Party $party, private Filter $messageFilter) {
		$this->census     = new Census($this->party);
		$this->map        = new PartyMap(Lemuria::World(), $this->party);
		$this->dictionary = new Dictionary();
	}

	/**
	 * Get a string.
	 */
	#[Pure] public function get(string $keyPath, $index = null): string {
		return $this->dictionary->get($keyPath, $index);
	}

	/**
	 * Format a number with optional string.
	 */
	#[Pure] public function number(int|float $number, ?string $keyPath = null, ?Singleton $singleton = null, string $delimiter = ' '): string {
		if ($keyPath) {
			if ($singleton) {
				$keyPath .= '.' . getClass($singleton);
			}
			$index = $number == 1 ? 0 : 1;
			return formatNumber($number) . $delimiter . $this->get($keyPath, $index);
		}
		return formatNumber($number);
	}

	/**
	 * Get an Item from a set.
	 *
	 * @noinspection PhpPureFunctionMayProduceSideEffectsInspection
	 */
	#[Pure] public function item(string $class, ItemSet $set, string $keyPath = 'resource'): string {
		$item = $set[$class];
		return $this->number($item->Count(), $keyPath, $item->getObject());
	}

	/**
	 * Get a list of items from a set.
	 */
	public function items(array $classes, ItemSet $set, string $keyPath = 'resource'): string {
		$items = [];
		foreach ($classes as $class) {
			if (isset($set[$class])) {
				$items[] = $this->item($class, $set, $keyPath);
			}
		}
		switch (count($items)) {
			case 0 :
				return '';
			case 1 :
				return $items[0];
			default :
				$last = array_pop($items);
				return implode(', ', $items) . ' und ' . $last;
		}
	}

	/**
	 * Get a neighbour description.
	 */
	#[Pure] public function neighbour(?Region $region = null): string {
		if ($region) {
			$landscape = $region->Landscape();
			$text      = $this->get('article', $landscape) . ' ' . $this->get('landscape', $landscape);
			if ($region->Name()) {
				$text .= ' ' . $region->Name();
			}
			return $text;
		} else {
			return $this->get('world.null');
		}
	}

	/**
	 * @return Message[]
	 */
	public function messages(Identifiable $entity): array {
		$messages = [];
		foreach (Lemuria::Report()->getAll($entity) as $message) {
			if (!$this->messageFilter->retains($message)) {
				$messages[] = $message;
			}
		}
		return $messages;
	}

	/**
	 * Render a report message.
	 */
	abstract public function message(Message $message): string;

	/**
	 * Generate the template output.
	 */
	public function generate(): string {
		if (!ob_start()) {
			throw new \RuntimeException('Could not start output buffering.');
		}
		return $this->generateContent();
	}

	abstract protected function generateContent(): string;
}
