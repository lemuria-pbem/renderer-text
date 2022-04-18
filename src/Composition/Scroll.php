<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Composition;

use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Engine\Fantasya\Factory\Model\SpellDetails;
use Lemuria\Model\Fantasya\Composition\Scroll as ScrollModel;
use Lemuria\Model\Fantasya\Exception\JsonException;

final class Scroll extends AbstractComposition
{
	/**
	 * @throws JsonException
	 */
	public function getContent(): string {
		$scroll = $this->getScroll();
		$spell  = $scroll->Spell();
		if ($spell) {
			$details = new SpellDetails($spell);
			$content = $this->createContentHeader($details->Name() . ' (Stufe ' . $spell->Difficulty() . ')' . PHP_EOL);
			foreach ($details->Description() as $description) {
				$content .= wrap($description);
			}
			return $content;
		}
		return $this->noContent('Auf dieser Schriftrolle steht nichts geschrieben.');
	}

	private function getScroll(): ScrollModel {
		/** @var ScrollModel $scroll */
		$scroll = $this->composition;
		return $scroll;
	}
}
