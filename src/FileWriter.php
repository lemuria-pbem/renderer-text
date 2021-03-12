<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use JetBrains\PhpStorm\Pure;

use Lemuria\Id;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Writer;

abstract class FileWriter implements Writer
{
	/**
	 * @var Wrapper[]
	 */
	protected array $wrapper = [];

	#[Pure] public function __construct(private string $path) {
	}

	public function render(Id $party): Writer {
		$view   = $this->getView(Party::get($party));
		$report = $view->generate();

		foreach ($this->wrapper as $wrapper) {
			$report = $wrapper->wrap($report);
		}

		if (!file_put_contents($this->path, $report)) {
			throw new \RuntimeException('Could not create report.');
		}

		return $this;
	}

	public function add(Wrapper $wrapper): self {
		$this->wrapper[] = $wrapper;
		return $this;
	}

	abstract protected function getView(Party $party): View;
}
