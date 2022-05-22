<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Engine\Message\Filter;
use Lemuria\Renderer\PathFactory;
use Lemuria\Renderer\Writer;

abstract class AbstractWriter implements Writer
{
	use VersionTrait;

	public function __construct(protected PathFactory $pathFactory) {
	}

	public function setFilter(Filter $filter): Writer {
		return $this;
	}
}
