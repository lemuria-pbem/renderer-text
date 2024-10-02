<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Dispatcher\Attribute\Emit;
use Lemuria\Dispatcher\Event\Renderer\Written;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Text\Model\HerbalBookTrait;

class HerbalBookWriter extends AbstractWriter
{
	use HerbalBookTrait;
	use VersionTrait;

	#[Emit(Written::class)]
	public function render(Id $entity): static {
		$party = Party::get($entity);
		$path  = $this->pathFactory->getPath($this, $party);
		if (!file_put_contents($path, $this->generate($party))) {
			throw new \RuntimeException('Could not create herbage.');
		}
		Lemuria::Dispatcher()->dispatch(new Written($this, $entity, $path));
		return $this;
	}

	protected function generate(Party $party): string {
		$herbalBook = $party->HerbalBook();
		return $this->sortAndGenerate($herbalBook);
	}
}
