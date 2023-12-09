<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Composition;

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Engine\Fantasya\Factory\Model\SpellDetails;
use Lemuria\Model\Fantasya\Composition\Spellbook as SpellbookModel;
use Lemuria\Model\Fantasya\Exception\JsonException;
use Lemuria\Model\Fantasya\Spell;

final class Spellbook extends AbstractComposition
{
	/**
	 * @throws JsonException
	 */
	public function getContent(): string {
		$spellbook = $this->getSpellbook();
		if ($spellbook->Spells()->isEmpty()) {
			return $this->noContent('Die Seiten dieses Zauberbuches sind leer.');
		}

		$content = PHP_EOL . hr() . PHP_EOL . center('Inhalt');
		$n       = 1;
		foreach ($spellbook->Spells() as $spell /** @var Spell $spell */) {
			$details  = new SpellDetails($spell);
			$title    = $details->Name() . ' (Stufe ' . $spell->Difficulty() . ')' . PHP_EOL;
			$content .= PHP_EOL . $n++ . '. ' . $title . PHP_EOL;
			foreach ($details->Description() as $description) {
				$content .= wrap($description);
			}
		}
		return $content;
	}

	private function getSpellbook(): SpellbookModel {
		/** @var SpellbookModel $spellbook */
		$spellbook = $this->unicum->Composition();
		return $spellbook;
	}
}
