<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use JetBrains\PhpStorm\Pure;

use Lemuria\Engine\Fantasya\Census;
use Lemuria\Engine\Message\Filter;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Writer;

class OrderWriter implements Writer
{
	#[Pure] public function __construct(private string $path) {
	}

	public function setFilter(Filter $filter): Writer {
		return $this;
	}

	public function render(Id $party): Writer {
		if (!file_put_contents($this->path, $this->generate($party))) {
			throw new \RuntimeException('Could not create template.');
		}
		return $this;
	}

	protected function generate(Id $id): string {
		$party    = Party::get($id);
		$census   = new Census($party);
		$template = $this->createHeader($party);
		foreach ($census->getAtlas() as $region /* @var Region $region */) {
			foreach ($region->Estate() as $construction /* @var Construction $construction */) {
				foreach ($construction->Inhabitants() as $unit /* @var Unit $unit */) {
					if ($unit->Party() === $party) {
						$template .= $this->createUnit($unit, $region);
					}
				}
			}
			foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */) {
				foreach ($vessel->Passengers() as $unit /* @var Unit $unit */) {
					if ($unit->Party() === $party) {
						$template .= $this->createUnit($unit, $region);
					}
				}
			}
			foreach ($census->getPeople($region) as $unit /* @var Unit $unit */) {
				if (!$unit->Construction() && !$unit->Vessel()) {
					$template .= $this->createUnit($unit, $region);
				}
			}
		}
		$template .= $this->createFooter();
		return $template;
	}

	private function createHeader(Party $party): string {
		return $this->createBlock([
			'PARTEI ' . $party->Id()
		]);
	}

	private function createUnit(Unit $unit, Region $region): string {
		$id     = $unit->Id();
		$lines  = ['EINHEIT ' . $id . '; ' . $unit->Name() . ' in ' . $region->Name()];
		$orders = Lemuria::Orders()->getDefault($id);
		if (count($orders)) {
			foreach ($orders as $order) {
				$lines[] = $order;
			}
		} else {
			$lines[] = '# Faulenze';
		}
		return $this->createBlock($lines);
	}

	private function createFooter(): string {
		return 'NÄCHSTER' . PHP_EOL;
	}

	#[Pure] private function createBlock(array $lines): string {
		return implode(PHP_EOL, $lines) . PHP_EOL . PHP_EOL;
	}
}
