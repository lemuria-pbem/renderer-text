<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Model\World;

use Lemuria\Engine\Fantasya\Census;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Sorting\Region\ByRealm;
use Lemuria\Model\World\Atlas;

class SortedAtlas extends Atlas
{
	/**
	 * @var array<int, true>
	 */
	private array $emptyRegions = [];

	public function __construct(Census $census) {
		parent::__construct();
		// Add all regions from the census.
		foreach ($census->getAtlas() as $region) {
			$this->add($region);
		}

		$party = $census->Party();
		// Add all realm regions even if they are empty.
		foreach ($party->Possessions() as $realm) {
			foreach ($realm->Territory() as $region) {
				$id = $region->Id();
				if (!$this->has($id)) {
					$this->add($region);
					$this->emptyRegions[$id->Id()] = true;
				}
			}
		}

		$this->sortUsing(new ByRealm($party));
	}

	public function isEmptyRegion(Region $region): bool {
		return isset($this->emptyRegions[$region->Id()->Id()]);
	}
}
