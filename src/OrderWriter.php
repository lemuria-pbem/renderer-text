<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use JetBrains\PhpStorm\Pure;

use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\Writer;

class OrderWriter implements Writer
{
	#[Pure] public function __construct(private string $path) {
	}

	public function render(Id $party): Writer {
		if (!file_put_contents($this->path, $this->generate($party))) {
			throw new \RuntimeException('Could not create template.');
		}
		return $this;
	}

	protected function generate(Id $id): string {
		$party    = Party::get($id);
		$template = $this->createHeader($party);
		foreach ($party->People() as $unit /* @var Unit $unit */) {
			$template .= $this->createUnit($unit);
		}
		$template .= $this->createFooter();
		return $template;
	}

	private function createHeader(Party $party): string {
		return $this->createBlock([
			'PARTEI ' . $party->Id()
		]);
	}

	private function createUnit(Unit $unit): string {
		$id     = $unit->Id();
		$lines  = ['EINHEIT ' . $id . '; ' . $unit->Name() . ' in ' . $unit->Region()->Name()];
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
