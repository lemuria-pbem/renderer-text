<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use JetBrains\PhpStorm\Pure;

use Lemuria\Id;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Writer;

abstract class FileWriter implements Writer
{
	#[Pure] public function __construct(private string $path) {
	}

	public function render(Id $party): Writer {
		$view = $this->getView(Party::get($party));

		if (!file_put_contents($this->path, $view->generate())) {
			throw new \RuntimeException('Could not create report.');
		}

		return $this;
	}

	abstract protected function getView(Party $party): View;
}
