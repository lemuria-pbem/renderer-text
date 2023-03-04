<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Composition;

use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\hr;
use Lemuria\Model\Fantasya\Composition\HerbAlmanac as Almanac;
use Lemuria\Renderer\Text\Model\HerbalBookTrait;

final class HerbAlmanac extends AbstractComposition
{
	use HerbalBookTrait;

	public function getContent(): string {
		$herbalBook = $this->getAlmanac()->HerbalBook();
		if ($herbalBook->isEmpty()) {
			return $this->noContent('Die Seiten dieses Kräuteralmanachs sind leer.');
		}

		$content  = PHP_EOL . hr() . PHP_EOL . center('Inhalt') . PHP_EOL;
		$content .= $this->sortAndGenerate($herbalBook);
		return $content;
	}

	private function getAlmanac(): Almanac {
		/** @var Almanac $almanac */
		$almanac = $this->composition;
		return $almanac;
	}
}
