<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

use JetBrains\PhpStorm\Pure;

use function Lemuria\number;
use Lemuria\Statistics\Data\Number;

class HtmlNumber
{
	public string $value;

	public string $change;

	public string $movement;

	#[Pure] public function __construct(Number $number) {
		$this->value    = number($number->value);
		$this->change   = $this->getChange($number);
		$this->movement = $this->getMovement($number);
	}

	#[Pure] private function getChange(Number $number): string {
		return match (true) {
			$number->change === null => '±' . number(is_float($number->value) ? 0.0 : 0),
			$number->change < 0      => number($number->change),
			$number->change > 0      => '+' . number($number->change),
			default                  => '±' . number($number->change)
		};
	}

	private function getMovement(Number $number): string {
		return match (true) {
			$number->change < 0 => 'change-less',
			$number->change > 0 => 'change-more',
			default             => 'change-equal'
		};
	}
}
