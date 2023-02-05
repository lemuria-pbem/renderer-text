<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Talent;

use function Lemuria\getClass;
use Lemuria\Engine\Fantasya\Calculus;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Talent;

class Talents
{
	/**
	 * @var array<string, Talent>
	 */
	protected array $talents = [];

	/**
	 * @var array<string, Matrix>
	 */
	protected array $matrices = [];

	private Dictionary $dictionary;

	public function __construct(Party $party) {
		$this->dictionary = new Dictionary();
		foreach ($party->People() as $unit) {
			$calculus = new Calculus($unit);
			foreach ($unit->Knowledge() as $ability) {
				$talent                           = $ability->Talent();
				$this->talents[getClass($talent)] = $talent;
				$this->getMatrix($talent)->add($calculus);
			}
		}
	}

	/**
	 * @return array<string, Talent>
	 */
	public function Talents(): array {
		$talents = [];
		foreach ($this->talents as $class => $talent) {
			$translation           = $this->dictionary->get('talent.' . $class);
			$talents[$translation] = $talent;
		}
		ksort($talents);
		return $talents;
	}

	public function getMatrix(Talent $talent): Matrix {
		$class = getClass($talent);
		if (!isset($this->matrices[$class])) {
			$this->matrices[$class] = new Matrix($talent);
		}
		return $this->matrices[$class];
	}
}
