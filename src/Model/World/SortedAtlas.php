<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Model\World;

use Lemuria\Engine\Fantasya\Census;
use Lemuria\Model\Fantasya\Sorting\Region\ByRealm;
use Lemuria\Model\World\Atlas;

class SortedAtlas extends Atlas
{
	public function __construct(Census $census) {
		parent::__construct();
		foreach ($census->getAtlas() as $region) {
			$this->add($region);
		}
		$this->sortUsing(new ByRealm($census->Party()));
	}
}
