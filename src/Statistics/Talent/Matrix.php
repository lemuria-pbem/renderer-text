<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Talent;

use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Model\Fantasya\Talent;
use Lemuria\Model\World\Atlas;
use Lemuria\SortMode;

class Matrix
{
	protected Atlas $atlas;

	/**
	 * @var array<int, array<int, array<array>>>
	 */
	protected array $levels = [];

	/**
	 * @var array<int, array<int, Value>>
	 */
	protected array $regions = [];

	protected int $maximum = 0;

	public function __construct(private readonly Talent $talent) {
		$this->atlas = new Atlas();
	}

	public function Atlas(): Atlas {
		return $this->atlas;
	}

	public function Talent(): Talent {
		return $this->talent;
	}

	public function Maximum(): int {
		return max(array_keys($this->levels));
	}

	/**
	 * @return array<int, array<int, Value>>
	 */
	public function Regions(): array {
		return $this->sort()->regions;
	}

	public function add(Calculus $calculus): static {
		$region = $calculus->Unit()->Region();
		$this->atlas->add($region);

		$knowledge = $calculus->knowledge($this->talent);
		$level     = $knowledge->Level();
		if (!isset($this->levels[$level])) {
			$this->levels[$level] = [];
		}

		$id = $region->Id()->Id();
		if (!isset($this->levels[$level][$id])) {
			$this->levels[$level][$id] = [];
		}
		$this->levels[$level][$id][] = [$calculus, $knowledge->Experience()];

		return $this;
	}

	protected function sort(): static {
		$this->atlas->sort(SortMode::NorthToSouth);
		$max = $this->Maximum();
		foreach ($this->atlas as $region) {
			for ($i = 0; $i <= $max; $i++) {
				$this->regions[$region->Id()->Id()][] = new Value();
			}
		}

		foreach ($this->levels as $level => $regions) {
			foreach ($regions as $id => $units) {
				foreach ($units as $calculus) {
					$this->regions[$id][$level]->add(...$calculus);
				}
			}
		}

		return $this;
	}
}
