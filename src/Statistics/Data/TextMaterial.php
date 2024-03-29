<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

use function Lemuria\number;
use Lemuria\Renderer\Text\View\Text;
use Lemuria\Statistics\Data\Number;

class TextMaterial implements \Stringable
{
	private string $translation;

	public function __construct(private readonly Number $number, string $class, Text $view) {
		$this->translation = $view->translate($class, $number->value === 1 ? 0 : 1);
	}

	public function __toString(): string {
		$output = number($this->number->value) . ' ' . $this->translation;
		if ($this->number->change > 0) {
			$output .= ' (+' . number($this->number->change) . ')';
		} elseif ($this->number->change < 0) {
			$output .= ' (' . number($this->number->change) . ')';
		}
		return $output;
	}
}
