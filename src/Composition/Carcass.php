<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Composition;

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
		$carcass = $this->getCarcass();
		$animal  = $this->combineGrammar($carcass->Creature(), 'ein', Casus::Genitive);
		$content = $this->noContent('Es scheint sich um den Kadaver ' . $animal . ' zu handeln.');

		$loot = $this->getCarcass()->Inventory();
		if ($loot->isEmpty()) {
			return $content . PHP_EOL .
				   $this->noContent('Die Untersuchung dieses Kadavers hat nichts besonderes ergeben.');
		}

		$content .= PHP_EOL .
					$this->noContent('Folgende Gegenstände befinden sich zwischen den Überresten:') .
					PHP_EOL . PHP_EOL;
		foreach ($loot as $quantity) {
			$content .= $this->translateItem($quantity) . PHP_EOL;
		}
		return $content;
	}

	private function getCarcass(): CarcassModel {
		/** @var CarcassModel $carcass */
		$carcass = $this->composition;
		return $carcass;
	}
}
