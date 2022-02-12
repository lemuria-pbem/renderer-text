<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Composition;

use JetBrains\PhpStorm\Pure;

use Lemuria\Model\Fantasya\Composition\Spellbook as SpellbookModel;
use Lemuria\Model\Fantasya\Spell;

final class Spellbook extends AbstractComposition
{
	#[Pure] public function getContent(): string {
		$spellbook = $this->getSpellbook();
		if ($spellbook->Spells()->isEmpty()) {
			return $this->noContent('Dieses Zauberbuch ist noch nicht beschrieben worden.');
		}

		$output = $this->createContentHeader();
		$n      = 1;
		foreach ($spellbook->Spells() as $spell /* @var Spell $spell */) {
			$output .= $n++ . '. ' . $this->dictionary->get('spell', $spell) . PHP_EOL;
		}
		return $output;
	}

	/**
	 * @noinspection PhpUnnecessaryLocalVariableInspection
	 */
	private function getSpellbook(): SpellbookModel {
		/** @var SpellbookModel $spellbook */
		$spellbook = $this->composition;
		return $spellbook;
	}
}
