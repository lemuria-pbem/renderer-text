<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Engine\Fantasya\Factory\Model\CompositionDetails;
use Lemuria\Model\Fantasya\Exception\JsonException;
use Lemuria\Model\Fantasya\Practice;
use Lemuria\Model\Fantasya\Unicum;

class UnicumWriter
{
	public function __construct(private string $path) {
	}

	/**
	 * @throws JsonException
	 */
	public function render(Unicum $unicum): UnicumWriter {
		if (!file_put_contents($this->path, $this->generate($unicum))) {
			throw new \RuntimeException('Could not create unicum information.');
		}
		return $this;
	}

	/**
	 * @throws JsonException
	 */
	protected function generate(Unicum $unicum): string {
		$details     = new CompositionDetails($unicum);
		$composition = $details->Composition();

		$output  = $details->Name() . PHP_EOL . PHP_EOL;
		$output .= implode(PHP_EOL, $details->Description()) . PHP_EOL . PHP_EOL;
		$output .= 'Aktionen: ' . PHP_EOL . PHP_EOL;
		if ($composition->supports(Practice::APPLY)) {
			$output .= $details->ApplyCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::GIVE)) {
			$output .= $details->BestowCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::READ)) {
			$output .= $details->ReadCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::WRITE)) {
			$output .= $details->WriteCommand() . PHP_EOL;
		}

		return $output;
	}
}
