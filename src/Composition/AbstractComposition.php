<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Composition;

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Engine\Fantasya\Factory\PartyUnica;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Renderer\Text\Composition;

abstract class AbstractComposition implements Composition
{
	protected PartyUnica $context;

	public function __construct(protected Unicum $unicum) {
	}

	public function setContext(PartyUnica $context): static {
		$this->context = $context;
		return $this;
	}

	protected function createContentHeader(?string $content = null): string {
		$header = PHP_EOL . hr() . PHP_EOL . center('Inhalt') . PHP_EOL;
		return $content ? $header . wrap($content) : $header;
	}

	protected function noContent(string $text): string {
		return PHP_EOL . wrap($text);
	}
}
