<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Talent;

use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Model\Fantasya\Unit;

class Value
{
	public int $count = 0;

	public Unit $unit;

	private int $best = -1;

	public function add(Calculus $calculus, int $experience): Value {
		$unit         = $calculus->Unit();
		$this->count += $unit->Size();
		if ($experience > $this->best) {
			$this->unit = $unit;
			$this->best = $experience;
		}
		return $this;
	}
}
