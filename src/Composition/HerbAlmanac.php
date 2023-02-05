<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Composition;

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use Lemuria\Engine\Fantasya\Command\Explore;
use Lemuria\Engine\Fantasya\Event\Visit;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Composition\HerbAlmanac as Almanac;

final class HerbAlmanac extends AbstractComposition
{
	public function getContent(): string {
		$herbalBook = $this->getAlmanac()->HerbalBook();
		if ($herbalBook->isEmpty()) {
			return $this->noContent('Die Seiten dieses Kräuteralmanachs sind leer.');
		}

		$content    = PHP_EOL . hr() . PHP_EOL . center('Inhalt') . PHP_EOL;
		$dictionary = new Dictionary();
		$round      = Lemuria::Calendar()->Round() - 1;
		foreach ($herbalBook as $region) {
			$herbage  = $herbalBook->getHerbage($region);
			$rounds   = $herbalBook->getVisit($region)->Round() - $round;
			$content .= $dictionary->get('landscape.' . $region->Landscape()) . ' ' . $region->Name() . ': ';
			$content .= $dictionary->get('amount.' . Explore::occurrence($herbage)) . ' ' . $dictionary->get('resource.' . $herbage->Herb(), 1) . ' ';
			$content .= '(' . str_replace('$rounds', (string)abs($rounds), $dictionary->get('visit.' . Visit::when($rounds)) ). ')';
			$content .= PHP_EOL;
		}
		return $content;
	}

	private function getAlmanac(): Almanac {
		/** @var Almanac $almanac */
		$almanac = $this->composition;
		return $almanac;
	}
}
