<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

use JetBrains\PhpStorm\Pure;

use function Lemuria\number;
use Lemuria\Renderer\Text\Text\TableRow;
use Lemuria\Statistics\Data\Number;

class TextNumber extends TableRow
{
	public string $value;

	public string $change;

	#[Pure] public function __construct(Number $number, private readonly string $name) {
		parent::__construct($this->name, number($number->value), $this->getChange($number));
	}

	#[Pure] private function getChange(Number $number): string {
		return match (true) {
			$number->change === null => '±' . number(is_float($number->value) ? 0.0 : 0),
			$number->change < 0      => number($number->change),
			$number->change > 0      => '+' . number($number->change),
			default                  => '±' . number($number->change)
		};
	}
}
