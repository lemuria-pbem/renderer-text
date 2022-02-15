<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Model\Fantasya\Composition as CompositionModel;

interface Composition
{
	public function __construct(CompositionModel $composition);

	public function getContent(): string;
}
