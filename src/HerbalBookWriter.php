<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Engine\Fantasya\Command\Explore;
use Lemuria\Engine\Fantasya\Event\Visit;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\World\Atlas;
use Lemuria\Model\World\SortMode;
use Lemuria\Renderer\Writer;

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
		$herbalBook = $party->HerbalBook();
		$atlas      = new Atlas();
		foreach ($herbalBook as $region /* @var Region $region */) {
			$atlas->add($region);
		}
		$atlas->sort(SortMode::NORTH_TO_SOUTH);

		$output     = '';
		$round      = Lemuria::Calendar()->Round() - 1;
		$dictionary = new Dictionary();
		foreach ($atlas as $region /* @var Region $region */) {
			$herbage = $herbalBook->getHerbage($region);
			$rounds  = $herbalBook->getVisit($region)->Round() - $round;

			$output .= $dictionary->get('landscape.' . $region->Landscape()) . ' ' . $region->Name() . ': ';
			$output .= $dictionary->get('amount.' . Explore::occurrence($herbage)) . ' ' . $dictionary->get('resource.' . $herbage->Herb(), 1) . ' ';
			$output .= '(' . str_replace('$rounds', (string)abs($rounds), $dictionary->get('visit.' . Visit::when($rounds)) ). ')';
			$output .= PHP_EOL;
		}
		return $output;
	}
}
