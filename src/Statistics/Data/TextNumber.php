<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

use function Lemuria\number;
use Lemuria\Renderer\Text\Text\TableRow;
use Lemuria\Statistics\Data\Number;

class TextNumber extends TableRow
{
	public string $value;

	public string $change;

	public function __construct(Number $number, private readonly string $name, string $unit = '') {
		$value = number(is_float($number->value) ? round($number->value, 2) : $number->value);
		parent::__construct($this->name, $value, $this->getChange($number), $unit);
	}

	private function getChange(Number $number): string {
		$change = is_float($number->change) ? round($number->change, 2) : $number->change;
		return match (true) {
			$change === null => '±' . number(is_float($number->value) ? 0.0 : 0),
			$change < 0      => number($change),
			$change > 0      => '+' . number($change),
			default          => '±' . number($change)
		};
	}
}
