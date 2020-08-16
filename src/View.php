<?php
declare (strict_types = 1);
namespace Lemuria\Renderer\Text;

use function Lemuria\getClass;
use Lemuria\Entity;
use Lemuria\ItemSet;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Lemuria\Party;
use Lemuria\Model\Lemuria\Party\Census;
use Lemuria\Model\Lemuria\Region;
use Lemuria\Model\World;
use Lemuria\Singleton;

/**
 * Create a description line.
 *
 * @param Entity $entity
 * @return string
 */
function description(Entity $entity): string {
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
 *
 * @param string $output
 * @return string
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
 *
 * @return string
 */
function hr(): string {
	return line(str_pad('', 80, '-'));
}

/**
 * Create an output line terminated by EOL.
 *
 * @param string $output
 * @return string
 */
function line(string $output): string {
	return $output . PHP_EOL;
}

/**
 * Take text and wrap lines that are too long.
 *
 * @param string $output
 * @return string
 */
function wrap(string $output): string {
	$wrapped = '';
	foreach (explode('\n', $output) as $line) {
		$wrapped .= wordwrap($line, 80);
	}
	return $wrapped;
}

/**
 * A view object that contains variables and helper functions for view scripts.
 */
abstract class View
{
	/**
	 * @var Party
	 */
	public Party $party;

	/**
	 * @var Census
	 */
	public Census $census;

	/**
	 * @var World
	 */
	public World $world;

	protected Dictionary $dictionary;

	/**
	 * @param Party $party
	 */
	public function __construct(Party $party) {
		$this->party      = $party;
		$this->census     = new Census($party);
		$this->world      = Lemuria::World();
		$this->dictionary = new Dictionary();
	}

	/**
	 * Get a string.
	 *
	 * @param string $keyPath
	 * @param mixed|null $index
	 * @return string
	 */
	public function get(string $keyPath, $index = null): string {
		return $this->dictionary->get($keyPath, $index);
	}

	/**
	 * Format a number with optional string.
	 *
	 * @param int|float $number
	 * @param string|null $keyPath
	 * @param Singleton|null $singleton
	 * @param string $delimiter
	 * @return string
	 */
	public function number($number, ?string $keyPath = null, ?Singleton $singleton = null, string $delimiter = ' '): string {
		$formattedNumber = $number < 0 ? '-' : '';
		$integer         = (int)abs($number);
		$string          = (string)$integer;
		$n               = strlen($string);
		$c               = $n;
		for ($i = 0; $i < $n; $i++) {
			if ($c-- % 3 === 0 && $i > 0) {
				$formattedNumber .= '.';
			}
			$formattedNumber .= $string[$i];
		}
		if (is_float($number)) {
			$string   = (string)$number;
			$i        = strpos($string, '.');
			$n        = strlen($string);
			$decimals = '0';
			if ($i !== false && ++$i < $n) {
				$decimals = substr($string, $i);
			}
			$formattedNumber .= ',' . $decimals;
		}
		if ($keyPath) {
			if ($singleton) {
				$keyPath .= '.' . getClass($singleton);
			}
			$index = $number == 1 ? 0 : 1;
			return $formattedNumber . $delimiter . $this->get($keyPath, $index);
		}
		return $formattedNumber;
	}

	/**
	 * Get an Item from a set.
	 *
	 * @param string $keyPath
	 * @param string $class
	 * @param ItemSet $set
	 * @return string
	 */
	public function item(string $class, ItemSet $set, string $keyPath = 'resource'): string {
		$item = $set[$class];
		return $this->number($item->Count(), $keyPath, $item->getObject());
	}

	/**
	 * Get a neighbour description.
	 *
	 * @param Region|null $region
	 * @return string
	 */
	public function neighbour(Region $region = null): string {
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
	 * Generate the template output.
	 *
	 * @return string
	 */
	public function generate(): string {
		if (!ob_start()) {
			throw new \RuntimeException('Could not start output buffering.');
		}
		return $this->generateContent();
	}

	/**
	 * @return string
	 */
	abstract protected function generateContent(): string;
}
