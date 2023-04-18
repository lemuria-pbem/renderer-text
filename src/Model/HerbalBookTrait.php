<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Model;

use function Lemuria\Renderer\Text\View\underline;
use Lemuria\Engine\Fantasya\Command\Explore;
use Lemuria\Engine\Fantasya\Event\Visit;
use Lemuria\Engine\Fantasya\Factory\GrammarTrait;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\HerbalBook;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\World\FantasyaAtlas;
use Lemuria\SortMode;

trait HerbalBookTrait
{
	use GrammarTrait;

	protected function sortAndGenerate(HerbalBook $herbalBook): string {
		$this->initDictionary();
		$output     = '';
		$round      = Lemuria::Calendar()->Round() - 1;
		$continents = $this->getContinents($herbalBook);
		foreach ($continents as $id => $atlas) {
			if (!$atlas->isEmpty()) {
				$continent = Continent::get(new Id($id));
				if (!empty($output)) {
					$output .= PHP_EOL;
				}
				$output .= underline('Kräutervorkommen auf ' . $continent->Name());

				$landscapes = $this->sortByLandscape($atlas);
				foreach ($landscapes as $regions) {
					$output .= PHP_EOL;
					foreach ($regions as $region) {
						$herbage = $herbalBook->getHerbage($region);
						$rounds  = $herbalBook->getVisit($region)->Round() - $round;

						$output .= $this->translateSingleton($region->Landscape()) . ' ' . $region->Name() . ': ';
						$output .= $this->dictionary->get('amount.' . Explore::occurrence($herbage)) . ' ' . $this->translateSingleton($herbage->Herb(), 1) . ' ';
						$output .= '(' . str_replace('$rounds', (string)abs($rounds), $this->dictionary->get('visit.' . Visit::when($rounds)) ). ')';
						$output .= PHP_EOL;
					}
				}
			}
		}
		return $output;
	}

	/**
	 * @return array<int, FantasyaAtlas>
	 */
	private function getContinents(HerbalBook $herbalBook): array {
		$continents = [];
		foreach (Continent::all() as $continent) {
			$atlas = new FantasyaAtlas();
			$atlas->forContinent($continent);
			foreach ($herbalBook as $region) {
				$atlas->add($region);
			}
			$atlas->sort(SortMode::NorthToSouth);
			$continents[$continent->Id()->Id()] = $atlas;
		}
		return $continents;
	}

	/**
	 * @return array<string, array<Region>>
	 */
	private function sortByLandscape(FantasyaAtlas $atlas): array {
		$landscapes = [];
		foreach ($atlas as $region) {
			$landscape = $this->translateSingleton($region->Landscape());
			if (!isset($landscapes[$landscape])) {
				$landscapes[$landscape] = [];
			}
			$landscapes[$landscape][] = $region;
		}
		ksort($landscapes);
		return $landscapes;
	}
}
