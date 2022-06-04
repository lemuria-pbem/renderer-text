<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

use function Lemuria\number;
use Lemuria\Statistics\Data\Number;

class HtmlNumber
{
	public string $value;

	public string $change;

	public string $movement;

	public function __construct(Number $number) {
		$this->value    = number(is_float($number->value) ? round($number->value, 2) : $number->value);
		$this->change   = $this->getChange($number);
		$this->movement = $this->getMovement($number);
	}

	private function getChange(Number $number): string {
		$change = is_float($number->change) ? round($number->change, 2) : $number->change;
		return match (true) {
			$change === null => '± ' . number(is_float($number->value) ? 0.0 : 0),
			$change < 0      => number($change),
			$change > 0      => '+ ' . number($change),
			default          => '± ' . number($change)
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
