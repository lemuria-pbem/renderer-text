<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

use JetBrains\PhpStorm\Pure;

use function Lemuria\number;
use Lemuria\Renderer\Text\View\Text;
use Lemuria\Statistics\Data\Number;

class TextMaterial implements \Stringable
{
	private string $translation;

	#[Pure] public function __construct(private Number $number, string $class, Text $view) {
		$this->translation = $view->get('resource.' . $class, $number->value === 1 ? 0 : 1);
	}

	#[Pure] public function __toString(): string {
		$output = number($this->number->value) . ' ' . $this->translation;
		if ($this->number->change > 0) {
			$output .= ' (+' . number($this->number->change) . ')';
		} elseif ($this->number->change < 0) {
			$output .= ' (' . number($this->number->change) . ')';
		}
		return $output;
	}
}
