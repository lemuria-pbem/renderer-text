<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use function Lemuria\Renderer\Text\View\underline;
use Lemuria\Engine\Fantasya\Command\Explore;
use Lemuria\Engine\Fantasya\Event\Visit;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\HerbalBook;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\World\FantasyaAtlas;
use Lemuria\Renderer\Writer;
use Lemuria\SortMode;

class HerbalBookWriter extends AbstractWriter
{
	use VersionTrait;

	public function render(Id $entity): Writer {
		$party = Party::get($entity);
		$path  = $this->pathFactory->getPath($this, $party);
		if (!file_put_contents($path, $this->generate($party))) {
			throw new \RuntimeException('Could not create herbage.');
		}
		return $this;
	}

	protected function generate(Party $party): string {
		$output     = '';
		$round      = Lemuria::Calendar()->Round() - 1;
		$dictionary = new Dictionary();
		$herbalBook = $party->HerbalBook();
		$continents = $this->getContinents($herbalBook);
		foreach ($continents as $id => $atlas) {
			if (!$atlas->isEmpty()) {
				$continent = Continent::get(new Id($id));
				if (!empty($output)) {
					$output .= PHP_EOL;
				}
				$output .= underline('Kräutervorkommen auf ' . $continent->Name());

				$landscapes = $this->sortByLandscape($atlas, $dictionary);
				foreach ($landscapes as $regions) {
					$output .= PHP_EOL;
					foreach ($regions as $region) {
						$herbage = $herbalBook->getHerbage($region);
						$rounds  = $herbalBook->getVisit($region)->Round() - $round;

						$output .= $dictionary->get('landscape.' . $region->Landscape()) . ' ' . $region->Name() . ': ';
						$output .= $dictionary->get('amount.' . Explore::occurrence($herbage)) . ' ' . $dictionary->get('resource.' . $herbage->Herb(), 1) . ' ';
						$output .= '(' . str_replace('$rounds', (string)abs($rounds), $dictionary->get('visit.' . Visit::when($rounds)) ). ')';
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
	private function sortByLandscape(FantasyaAtlas $atlas, Dictionary $dictionary): array {
		$landscapes = [];
		foreach ($atlas as $region) {
			$landscape = $dictionary->get('landscape.' . $region->Landscape());
			if (!isset($landscapes[$landscape])) {
				$landscapes[$landscape] = [];
			}
			$landscapes[$landscape][] = $region;
		}
		ksort($landscapes);
		return $landscapes;
	}
}
