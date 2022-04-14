<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

use JetBrains\PhpStorm\Pure;

use function Lemuria\sign;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\Statistics\Data\Number;

class HtmlMaterial extends HtmlCommodity implements \Stringable
{
	private string $translation;

	private int $direction;

	#[Pure] public function __construct(Number $number, string $class, Html $view) {
		parent::__construct($number, $class);
		$this->translation = $view->get('resource.' . $class, $number->value === 1 ? 0 : 1);
		$this->direction   = sign($number->change);
	}

	public function __toString(): string {
		$output = '<span>' . $this->value . '&nbsp;' . $this->translation . '</span>';
		if ($this->direction !== 0) {
			$class   = 'badge badge-inverse badge-' . ($this->direction > 0 ? 'success' : 'danger');
			$output .= '<span class="' . $class . '">' . $this->change . '</span>';
		}
		return $output;
	}
}
