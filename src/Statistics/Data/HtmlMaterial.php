<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

use function Lemuria\direction;
use Lemuria\Engine\Fantasya\Message\Casus;
use Lemuria\Renderer\Text\View\Html;
use Lemuria\Statistics\Data\Number;

class HtmlMaterial extends HtmlCommodity implements \Stringable
{
	private string $translation;

	private int $direction;

	public function __construct(Number $number, string $class, Html $view) {
		parent::__construct($number, $class);
		$this->translation = $view->translate($class, $number->value === 1 ? 0 : 1, Casus::Adjective);
		$this->direction   = direction($number->change);
	}

	public function __toString(): string {
		$output = '<span style="white-space: nowrap;"><span>' . $this->value . '&nbsp;' . $this->translation . '</span>';
		if ($this->direction !== 0) {
			$class   = 'badge badge-inverse badge-' . ($this->direction > 0 ? 'success' : 'danger');
			$output .= '<span class="' . $class . '">' . $this->change . '</span>';
		}
		return $output . '</span>';
	}
}
