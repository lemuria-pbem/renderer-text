<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use function Lemuria\mbStrPad;
use Lemuria\Engine\Fantasya\Census;
use Lemuria\Engine\Fantasya\Factory\GrammarTrait;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\People;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Model\Fantasya\Vessel;
use Lemuria\Renderer\PathFactory;
use Lemuria\Renderer\Writer;

class OrderWriter extends AbstractWriter
{
	use GrammarTrait;
	use VersionTrait;

	protected final const SEPARATOR_LENGTH = 78;

	public function __construct(PathFactory $pathFactory) {
		parent::__construct($pathFactory);
		$this->initDictionary();
	}

	public function render(Id $entity): Writer {
		$party = Party::get($entity);
		$path  = $this->pathFactory->getPath($this, $party);
		if (!file_put_contents($path, $this->generate($party))) {
			throw new \RuntimeException('Could not create template.');
		}
		return $this;
	}

	protected function generate(Party $party): string {
		$census    = new Census($party);
		$template  = $this->createHeader($party);
		$template .= $this->createRegionDivider();
		foreach ($census->getAtlas() as $region) {
			$inConstruction = null;
			$inVessel       = null;

			$template .= $this->createRegion($region);

			foreach (View::sortedEstate($region) as $construction) {
				$units = new People();
				foreach ($construction->Inhabitants() as $unit) {
					if ($unit->Party() === $party) {
						$units->add($unit);
					}
				}
				if ($units->count()) {
					$inConstruction = true;
					$template      .= $this->createConstruction($construction);
					foreach ($units as $unit) {
						$template .= $this->createUnit($unit);
					}
				}
			}

			foreach (View::sortedFleet($region) as $vessel) {
				$units = new People();
				foreach ($vessel->Passengers() as $unit) {
					if ($unit->Party() === $party) {
						$units->add($unit);
					}
				}
				if ($units->count()) {
					$inVessel  = true;
					$template .= $this->createVessel($vessel);
					foreach ($vessel->Passengers() as $unit) {
						$template .= $this->createUnit($unit);
					}
				}
			}

			$writeSeparator = $inConstruction || $inVessel;
			foreach ($region->Residents() as $unit) {
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

	private function createRegion(Region $region): string {
		return PHP_EOL . $this->createBlock(['; Region ' . $region]);
	}

	private function createConstruction(Construction $construction): string {
		$building = $this->translateSingleton($construction->Building());
		$name     = '; ' . $building . ' ' . $construction . ' ';
		return $this->createBlock([mbStrPad($name, self::SEPARATOR_LENGTH, '-')]);
	}

	private function createVessel(Vessel $vessel): string {
		$ship = $this->translateSingleton($vessel->Ship());
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

	private function createSeparator(): string {
		return $this->createBlock([str_pad('; ', self::SEPARATOR_LENGTH, '-')]);
	}

	private function createRegionDivider(): string {
		return str_pad('; ', self::SEPARATOR_LENGTH, '=') . PHP_EOL;
	}

	private function createFooter(): string {
		return PHP_EOL . 'NÄCHSTER' . PHP_EOL;
	}

	private function createBlock(array $lines): string {
		return implode(PHP_EOL, $lines) . PHP_EOL . PHP_EOL;
	}
}
