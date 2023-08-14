<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Id;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Text\Model\HerbalBookTrait;

class HerbalBookWriter extends AbstractWriter
{
	use HerbalBookTrait;
	use VersionTrait;

	public function render(Id $entity): static {
		$party = Party::get($entity);
		$path  = $this->pathFactory->getPath($this, $party);
		if (!file_put_contents($path, $this->generate($party))) {
			throw new \RuntimeException('Could not create herbage.');
		}
		return $this;
	}

	protected function generate(Party $party): string {
		$herbalBook = $party->HerbalBook();
		return $this->sortAndGenerate($herbalBook);
	}
}
