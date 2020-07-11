<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Id;
use Lemuria\Model\Lemuria\Party;
use Lemuria\Renderer\Writer;

abstract class FileWriter implements Writer
{
	/**
	 * @var string
	 */
	private string $path;

	/**
	 * @param string $path
	 */
	public function __construct(string $path) {
		$this->path = $path;
	}

	/**
	 * @param Id $party
	 * @return Writer
	 */
	public function render(Id $party): Writer {
		$view = $this->getView(Party::get($party));

		if (!file_put_contents($this->path, $view->generate())) {
			throw new \RuntimeException('Could not create report.');
		}

		return $this;
	}

	/**
	 * @param Party $party
	 * @return View
	 */
	abstract protected function getView(Party $party): View;
}
