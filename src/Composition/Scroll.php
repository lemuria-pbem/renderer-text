<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Composition;

use JetBrains\PhpStorm\Pure;

use Lemuria\Model\Fantasya\Composition\Scroll as ScrollModel;

final class Scroll extends AbstractComposition
{
	#[Pure] public function getContent(): string {
		$scroll = $this->getScroll();
		$spell  = $scroll->Spell();
		if ($spell) {
			return $this->createContentHeader($this->dictionary->get('spell', $spell));
		}
		return $this->noContent('Auf dieser Schriftrolle steht nichts geschrieben.');
	}

	/**
	 * @noinspection PhpUnnecessaryLocalVariableInspection
	 */
	private function getScroll(): ScrollModel {
		/** @var ScrollModel $scroll */
		$scroll = $this->composition;
		return $scroll;
	}
}
