<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use JetBrains\PhpStorm\Pure;

use function Lemuria\getClass;
use function Lemuria\mbStrPad;
use Lemuria\Engine\Fantasya\Census;
use Lemuria\Engine\Message\Filter;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\People;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\Writer;

class OrderWriter implements Writer
{
	use VersionTrait;

	protected final const SEPARATOR_LENGTH = 78;

	protected readonly Dictionary $dictionary;

	public function __construct(private readonly string $path) {
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
		$party     = Party::get($id);
		$census    = new Census($party);
		$template  = $this->createHeader($party);
		$template .= $this->createRegionDivider();
		foreach ($census->getAtlas() as $region /* @var Region $region */) {
			$inConstruction = null;
			$inVessel       = null;

			$template .= $this->createRegion($region);
			foreach ($region->Estate() as $construction /* @var Construction $construction */) {
				$units = new People();
				foreach ($construction->Inhabitants() as $unit /* @var Unit $unit */) {
					if ($unit->Party() === $party) {
						$units->add($unit);
					}
				}
				if ($units->count()) {
					$inConstruction = true;
					$template      .= $this->createConstruction($construction);
					foreach ($units as $unit /* @var Unit $unit */) {
						$template .= $this->createUnit($unit);
					}
				}
			}

			foreach ($region->Fleet() as $vessel /* @var Vessel $vessel */) {
				$units = new People();
				foreach ($vessel->Passengers() as $unit /* @var Unit $unit */) {
					if ($unit->Party() === $party) {
						$units->add($unit);
					}
				}
				if ($units->count()) {
					$inVessel  = true;
					$template .= $this->createVessel($vessel);
					foreach ($vessel->Passengers() as $unit /* @var Unit $unit */) {
						$template .= $this->createUnit($unit);
					}
				}
			}

			$writeSeparator = $inConstruction || $inVessel;
			foreach ($region->Residents() as $unit /* @var Unit $unit */) {
				if ($unit->Party() === $party && !$unit->Construction() && !$unit->Vessel()) {
					if ($writeSeparator) {
						$template .= $this->createSeparator();
						$writeSeparator = false;
					}
					$template .= $this->createUnit($unit);
				}
			}

			$template .= $this->createRegionDivider();
		}
		$template .= $this->createFooter();
		return $template;
	}

	private function createHeader(Party $party): string {
		$round = Lemuria::Calendar()->Round() + 1;
		return $this->createBlock([
			'PARTEI ' . $party->Id() . '; Befehle für Runde ' . $round
		]);
	}

	#[Pure] private function createRegion(Region $region): string {
		return PHP_EOL . $this->createBlock(['; Region ' . $region]);
	}

	#[Pure] private function createConstruction(Construction $construction): string {
		$building = $this->dictionary->get('building', getClass($construction->Building()));
		$name     = '; ' . $building . ' ' . $construction . ' ';
		return $this->createBlock([mbStrPad($name, self::SEPARATOR_LENGTH, '-')]);
	}

	#[Pure] private function createVessel(Vessel $vessel): string {
		$ship = $this->dictionary->get('ship', getClass($vessel->Ship()));
		$name = '; ' . $ship . ' ' . $vessel . ' ';
		return $this->createBlock([mbStrPad($name, self::SEPARATOR_LENGTH, '-')]);
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

	#[Pure] private function createRegionDivider(): string {
		return str_pad('; ', self::SEPARATOR_LENGTH, '=') . PHP_EOL;
	}

	private function createFooter(): string {
		return PHP_EOL . 'NÄCHSTER' . PHP_EOL;
	}

	#[Pure] private function createBlock(array $lines): string {
		return implode(PHP_EOL, $lines) . PHP_EOL . PHP_EOL;
	}
}
