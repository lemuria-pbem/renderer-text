<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Model;

use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Lemuria;
use Lemuria\Model\Domain;
use Lemuria\Model\Fantasya\Continent;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\World\FantasyaAtlas;
use Lemuria\Model\Location;
use Lemuria\Model\World\Atlas;

class TravelLog implements \Iterator
{
	/**
	 * @var Continent[]
	 */
	private array $continents = [];

	/**
	 * @var Atlas[]
	 */
	private array $atlas = [];

	private readonly TravelAtlas $travelAtlas;

	private int $index;

	public function __construct(Party $party) {
		foreach (Lemuria::Catalog()->getAll(Domain::CONTINENT) as $continent) {
			$this->continents[] = $continent;
			$atlas              = new FantasyaAtlas();
			$this->atlas[]      = $atlas->forContinent($continent);
		}
		$this->travelAtlas = new TravelAtlas($party);
		$this->travelAtlas->forRound(Lemuria::Calendar()->Round() - 1);
	}

	public function current(): Atlas {
		return $this->atlas[$this->index];
	}

	public function key(): Continent {
		return $this->continents[$this->index];
	}

	public function next(): void {
		$this->index++;
	}

	public function rewind(): void {
		foreach ($this->travelAtlas as $location /* @var Location $location */) {
			foreach ($this->atlas as $atlas) {
				$atlas->add($location);
			}
		}
		$this->index = 0;
	}

	public function valid(): bool {
		return $this->index < count($this->atlas);
	}
}
