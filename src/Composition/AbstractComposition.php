<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Composition;

use JetBrains\PhpStorm\Pure;

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Composition as CompositionModel;
use Lemuria\Renderer\Text\Composition;

abstract class AbstractComposition implements Composition
{
	protected Dictionary $dictionary;

	public function __construct(protected CompositionModel $composition) {
		$this->dictionary = new Dictionary();
	}

	#[Pure] protected function createContentHeader(string $content = null): string {
		$header = PHP_EOL . hr() . PHP_EOL . center('Inhalt') . PHP_EOL;
		return $content ? $header . wrap($content) : $header;
	}

	#[Pure] protected function noContent(string $text): string {
		return PHP_EOL . wrap($text);
	}
}