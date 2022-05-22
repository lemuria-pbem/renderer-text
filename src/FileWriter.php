<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use JetBrains\PhpStorm\Pure;

use Lemuria\Engine\Message\Filter;
use Lemuria\Engine\Message\Filter\NullFilter;
use Lemuria\Id;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\PathFactory;
use Lemuria\Renderer\Writer;

abstract class FileWriter extends AbstractWriter
{
	use VersionTrait;

	/**
	 * @var Wrapper[]
	 */
	protected array $wrapper = [];

	protected Filter $messageFilter;

	#[Pure] public function __construct(PathFactory $pathFactory) {
		parent::__construct($pathFactory);
		$this->messageFilter = new NullFilter();
	}

	public function setFilter(Filter $filter): Writer {
		$this->messageFilter = $filter;
		return $this;
	}

	public function render(Id $entity): Writer {
		$party  = Party::get($entity);
		$view   = $this->getView($party);
		$report = $view->generate();

		foreach ($this->wrapper as $wrapper) {
			$report = $wrapper->wrap($report);
		}

		$path = $this->pathFactory->getPath($this, $party);
		if (!file_put_contents($path, $report)) {
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
