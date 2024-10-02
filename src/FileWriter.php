<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Dispatcher\Attribute\Emit;
use Lemuria\Dispatcher\Event\Renderer\Written;
use Lemuria\Engine\Message\Filter;
use Lemuria\Engine\Message\Filter\NullFilter;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\PathFactory;

abstract class FileWriter extends AbstractWriter
{
	use VersionTrait;

	protected Party $party;

	protected View $view;

	/**
	 * @var array<Wrapper>
	 */
	protected array $wrapper = [];

	protected Filter $messageFilter;

	public function __construct(PathFactory $pathFactory) {
		parent::__construct($pathFactory);
		$this->messageFilter = new NullFilter();
	}

	public function getPathFactory(): PathFactory {
		return $this->pathFactory;
	}

	public function getFilter(): Filter {
		return $this->messageFilter;
	}

	public function setFilter(Filter $filter): static {
		$this->messageFilter = $filter;
		return $this;
	}

	public function getParty(): Party {
		return $this->party;
	}

	public function getView(): View {
		return $this->view;
	}

	#[Emit(Written::class)]
	public function render(Id $entity): static {
		$this->party = Party::get($entity);
		$this->view  = $this->createView();
		$report      = $this->view->generate();

		foreach ($this->wrapper as $wrapper) {
			$report = $wrapper->wrap($report);
		}

		$path = $this->pathFactory->getPath($this, $this->party);
		if (!file_put_contents($path, $report)) {
			throw new \RuntimeException('Could not create report.');
		}
		Lemuria::Dispatcher()->dispatch(new Written($this, $entity, $path));

		return $this;
	}

	public function add(Wrapper $wrapper): self {
		$this->wrapper[] = $wrapper;
		return $this;
	}

	abstract protected function createView(): View;
}
