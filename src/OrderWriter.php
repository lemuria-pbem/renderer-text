<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use JetBrains\PhpStorm\Pure;

use function Lemuria\getClass;
use Lemuria\Engine\Fantasya\Census;
use Lemuria\Engine\Message\Filter;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Writer;

class OrderWriter implements Writer
{
	use VersionTrait;

	protected const SEPARATOR_LENGTH = 30;

	protected Dictionary $dictionary;

	public function __construct(private string $path) {
		$this->dictionary = new Dictionary();
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
			$inConstruction = null;
			$inVessel       = null;

			$template .= $this->createRegion($region);
			foreach ($region->Estate() as $construction /* @var Construction $construction */) {
				$inConstruction = true;
				$template .= $this->createConstruction($construction);
				foreach ($construction->Inhabitants() as $unit /* @var Unit $unit */) {
					if ($unit->Party() === $party) {
						$template .= $this->createUnit($unit);
					}
				}
			}

			foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */) {
				$inVessel  = true;
				$template .= $this->createVessel($vessel);
				foreach ($vessel->Passengers() as $unit /* @var Unit $unit */) {
					if ($unit->Party() === $party) {
						$template .= $this->createUnit($unit);
					}
				}
			}

			$writeSeparator = $inConstruction || $inVessel;
			foreach ($census->getPeople($region) as $unit /* @var Unit $unit */) {
				if (!$unit->Construction() && !$unit->Vessel()) {
					if ($writeSeparator) {
						$template .= $this->createSeparator();
						$writeSeparator = false;
					}
					$template .= $this->createUnit($unit);
				}
			}
		}
		$template .= $this->createFooter();
		return $template;
	}

	#[Pure] private function createHeader(Party $party): string {
		$round = Lemuria::Calendar()->Round() + 1;
		return $this->createBlock([
			'PARTEI ' . $party->Id() . '; Befehle für Runde ' . $round
		]);
	}

	#[Pure] private function createRegion(Region $region): string {
		return $this->createBlock(['; Region ' . $region]);
	}

	#[Pure] private function createConstruction(Construction $construction): string {
		$building = $this->dictionary->get('building', getClass($construction->Building()));
		return $this->createBlock(['; ' . $building . ' ' . $construction]);
	}

	#[Pure] private function createVessel(Vessel $vessel): string {
		$ship = $this->dictionary->get('ship', getClass($vessel->Ship()));
		return $this->createBlock(['; ' . $ship . ' ' . $vessel]);
	}

	private function createUnit(Unit $unit): string {
		$id     = $unit->Id();
		$lines  = ['EINHEIT ' . $id . '; ' . $unit->Name()];
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

	#[Pure] private function createSeparator(): string {
		return $this->createBlock([str_pad('; ', self::SEPARATOR_LENGTH, '-')]);
	}

	private function createFooter(): string {
		return 'NÄCHSTER' . PHP_EOL;
	}

	#[Pure] private function createBlock(array $lines): string {
		return implode(PHP_EOL, $lines) . PHP_EOL . PHP_EOL;
	}
}
