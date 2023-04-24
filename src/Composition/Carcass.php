<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Composition;

use function Lemuria\number;
use function Lemuria\Renderer\Text\View\hr;
use Lemuria\Engine\Fantasya\Message\Casus;
use Lemuria\Engine\Fantasya\Factory\GrammarTrait;
use Lemuria\Model\Fantasya\Composition;
use Lemuria\Model\Fantasya\Composition\Carcass as CarcassModel;

final class Carcass extends AbstractComposition
{
	use GrammarTrait;

	public function __construct(Composition $composition) {
		parent::__construct($composition);
		$this->initDictionary();
	}

	public function getContent(): string {
		$carcass  = $this->getCarcass();
		$animal   = $this->combineGrammar($carcass->Creature(), 'ein', Casus::Genitive);
		$content = PHP_EOL . hr() . $this->noContent('Es scheint sich um den Kadaver ' . $animal . ' zu handeln.');

		$loot = $this->getCarcass()->Inventory();
		if ($loot->isEmpty()) {
			return $content . $this->noContent('Die Untersuchung dieses Kadavers hat nichts besonderes ergeben.');
		}

		$items = [];
		foreach ($loot as $quantity) {
			$items[] = number($quantity->Count()) . ' ' . $this->translateItem($quantity);
		}
		$content .= $this->noContent('Folgende Gegenstände befinden sich zwischen den Überresten: ' . implode(', ', $items));
		return $content;
	}

	private function getCarcass(): CarcassModel {
		/** @var CarcassModel $carcass */
		$carcass = $this->composition;
		return $carcass;
	}
}
