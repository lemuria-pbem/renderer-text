<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Exception\LemuriaException;
use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\underline;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Engine\Fantasya\Factory\Model\CompositionDetails;
use Lemuria\Model\Fantasya\Composition as CompositionModel;
use Lemuria\Model\Fantasya\Exception\JsonException;
use Lemuria\Model\Fantasya\Practice;
use Lemuria\Model\Fantasya\Unicum;

class UnicumWriter
{
	protected final const NAMESPACE = 'Lemuria\\Renderer\\Text\\Composition\\';

	public function __construct(private readonly string $path) {
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
		$name        = $unicum->Name();
		$description = $unicum->Description();
		$details     = new CompositionDetails($unicum);
		$composition = $details->Composition();

		if ($name) {
			$output = underline($name);
			if ($description) {
				$output .= PHP_EOL . wrap($description) . PHP_EOL . hr();
			}
			$output .= PHP_EOL . $details->Name() . ' [' . $unicum->Id() . ']';
		} elseif ($description) {
			$output  = wrap($description) . PHP_EOL;
			$output .= $details->Name() . ', unbenannt [' . $unicum->Id() . ']';
		} else {
			$output = $details->Name() . ', unbenannt, ohne Beschreibung [' . $unicum->Id() . ']';
		}

		$output .= PHP_EOL . PHP_EOL;
		foreach ($details->Description() as $description) {
			$output .= wrap($description);
		}
		$output .= PHP_EOL;

		$output .= 'Aktionen: ' . PHP_EOL . PHP_EOL;
		if ($composition->supports(Practice::APPLY)) {
			$output .= $details->ApplyCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::GIVE)) {
			$output .= $details->BestowCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::TAKE)) {
			$output .= $details->TakeCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::LOSE)) {
			$output .= $details->LoseCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::DESTROY)) {
			$output .= $details->DestroyCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::READ)) {
			$output .= $details->ReadCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::WRITE)) {
			$output .= $details->WriteCommand() . PHP_EOL;
		}

		$output .= $this->getContent($composition);
		return $output;
	}

	protected function getContent(CompositionModel $model): string {
		$class       = getClass($model);
		$renderer    = self::NAMESPACE . $class;
		try {
			$composition = new $renderer($model);
			if ($composition instanceof Composition) {
				return $composition->getContent();
			}
			throw new LemuriaException('Composition ' . $class . ' is not supported yet.');
		} catch (\Error $e) {
			throw new LemuriaException('Composition ' . $class . ' is not supported yet.', $e);
		}
	}
}
