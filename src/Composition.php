<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Engine\Fantasya\Factory\PartyUnica;
use Lemuria\Model\Fantasya\Unicum;

interface Composition
{
	public function __construct(Unicum $unicum);

	public function getContent(): string;

	public function setContext(PartyUnica $context): static;
}
