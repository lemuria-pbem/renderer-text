<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\hr;
use function Lemuria\Renderer\Text\View\underline;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Engine\Fantasya\Factory\Model\CompositionDetails;
use Lemuria\Engine\Fantasya\Factory\PartyUnica;
use Lemuria\Exception\LemuriaException;
use Lemuria\Id;
use Lemuria\Model\Fantasya\Exception\JsonException;
use Lemuria\Model\Fantasya\Practice;
use Lemuria\Model\Fantasya\Unicum;

class UnicumWriter extends AbstractWriter
{
	protected final const NAMESPACE = 'Lemuria\\Renderer\\Text\\Composition\\';

	protected PartyUnica $context;

	/**
	 * @throws JsonException
	 */
	public function render(Id $entity): static {
		$unicum = Unicum::get($entity);
		$path   = $this->pathFactory->getPath($this, $unicum);
		if (!file_put_contents($path, $this->generate($unicum))) {
			throw new \RuntimeException('Could not create unicum information.');
		}
		return $this;
	}

	public function setContext(PartyUnica $context): static {
		$this->context = $context;
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
		if ($composition->supports(Practice::Apply)) {
			$output .= $details->ApplyCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::Give)) {
			$output .= $details->BestowCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::Take)) {
			$output .= $details->TakeCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::Read)) {
			$output .= $details->ReadCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::Write)) {
			$output .= $details->WriteCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::Lose)) {
			$output .= $details->LoseCommand() . PHP_EOL;
		}
		if ($composition->supports(Practice::Destroy)) {
			$output .= $details->DestroyCommand() . PHP_EOL;
		}

		$output .= $this->getContent($unicum);
		return $output;
	}

	protected function getContent(Unicum $unicum): string {
		$model    = $unicum->Composition();
		$class    = getClass($model);
		$renderer = self::NAMESPACE . $class;
		try {
			$composition = new $renderer($unicum);
			if ($composition instanceof Composition) {
				return $composition->setContext($this->context)->getContent();
			}
			throw new LemuriaException('Composition ' . $class . ' is not supported yet.');
		} catch (\Error $e) {
			throw new LemuriaException('Composition ' . $class . ' is not supported yet.', $e);
		}
	}
}
