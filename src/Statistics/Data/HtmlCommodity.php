<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

use JetBrains\PhpStorm\Pure;

use Lemuria\Statistics\Data\Number;

class HtmlCommodity extends HtmlNumber
{
	public string $key;

	#[Pure] public function __construct(Number $number, public string $class = '') {
		parent::__construct($number);
		$this->key = strtolower($this->class);
	}
}
