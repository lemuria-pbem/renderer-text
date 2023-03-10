<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\View;

use function Lemuria\getClass;
use Lemuria\Engine\Fantasya\Combat\BattleLog;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Engine\Message;
use Lemuria\Engine\Message\Result;
use Lemuria\Identifiable;
use Lemuria\Model\Fantasya\Commodity\Silver;
use Lemuria\Model\Fantasya\Luxuries;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\Resources;
use Lemuria\Model\Fantasya\Transport;
use Lemuria\Model\Fantasya\Unicum;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Renderer\PathFactory;
use Lemuria\Renderer\Text\BattleLogWriter;
use Lemuria\Renderer\Text\FileWriter;
use Lemuria\Renderer\Text\HerbalBookWriter;
use Lemuria\Renderer\Text\SpellBookWriter;
use Lemuria\Renderer\Text\Statistics\Data\HtmlClassNumber;
use Lemuria\Renderer\Text\Statistics\Data\HtmlCommodity;
use Lemuria\Renderer\Text\Statistics\Data\HtmlMarket;
use Lemuria\Renderer\Text\Statistics\Data\HtmlMaterial;
use Lemuria\Renderer\Text\Statistics\Data\HtmlNumber;
use Lemuria\Renderer\Text\Statistics\Data\HtmlPrognosis;
use Lemuria\Renderer\Text\Statistics\Data\HtmlQualification;
use Lemuria\Renderer\Text\UnicumWriter;
use Lemuria\Renderer\Text\View;
use Lemuria\Statistics\Data\Number;

function id(Identifiable $entity, ?string $prefix = null): string {
	if (!$prefix) {
		$prefix = strtolower($entity->Catalog()->name);
	}
	return $prefix . '-' . $entity->Id();
}

/**
 * Replace email address with a mailto link.
 */
function linkEmail(string $input): string {
	if (preg_match('/\b([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,})\b/i', $input, $matches) === 1) {
		$n = count($matches);
		for ($i = 1; $i < $n; $i++) {
			$e     = $matches[$i];
			$input = str_replace($e, '<a href="mailto:' . $e . '">' . $e . '</a>', $input);
		}
	}
	return $input;
}

/**
 * Calculate the right Bootstrap column padding classes for a given column number.
 */
function p3(int $i, string $bp = 'md'): string {
	return match (--$i % 6) {
		0       => 'p-0 pe-' . $bp . '-3',
		1       => 'p-0 ps-' . $bp . '-3 pe-xl-3',
		2       => 'p-0 pe-' . $bp . '-3 ps-xl-3 pe-xl-0',
		3       => 'p-0 ps-' . $bp . '-3 ps-xl-0 pe-xl-3',
		4       => 'p-0 pe-' . $bp . '-3 ps-xl-3',
		default => 'p-0 ps-' . $bp . '-3 ps-xl-3 pe-xl-0'
	};
}

class Html extends View
{
	protected const BADGE_UNDEFINED = 'dark';

	protected const BADGE = [
		Result::Debug->value   => 'light',
		Result::Error->value   => 'danger',
		Result::Event->value   => 'info',
		Result::Failure->value => 'warning',
		Result::Success->value => 'success'
	];

	protected const LEVEL_UNDEFINED = 'U';

	protected const LEVEL = [
		Result::Debug->value => 'D', Result::Error->value => 'F', Result::Event->value => 'E', Result::Failure->value => 'W', Result::Success->value => 'M'
	];

	private PathFactory $pathFactory;

	public function __construct(FileWriter $writer) {
		parent::__construct($writer);
		$this->pathFactory = $writer->getPathFactory();
	}

	/**
	 * Render a template.
	 */
	public function template(string $name, mixed ...$variables): string {
		$this->variables = $variables;
		return $this->generateContent($name);
	}

	/**
	 * Render a report message.
	 */
	public function message(Message $message): string {
		$level = $message->Result()->value;
		$badge = self::BADGE[$level] ?? self::BADGE_UNDEFINED;
		$b     = self::LEVEL[$level] ?? self::LEVEL_UNDEFINED;
		return '<span class="badge text-bg-' . $badge . ' font-monospace">' . $b . '</span>&nbsp;' . $message;
	}

	/**
	 * Render a report message with section class.
	 */
	public function messageWithSection(Message $message): string {
		$level   = $message->Result()->value;
		$badge   = self::BADGE[$level] ?? self::BADGE_UNDEFINED;
		$b       = self::LEVEL[$level] ?? self::LEVEL_UNDEFINED;
		$section = strtolower($message->Section()->name);
		return '<span class="badge text-bg-' . $badge . ' font-monospace" data-section="' . $section . '">' . $b . '</span>&nbsp;' . $message;
	}

	public function numberStatistics(Subject $subject, Identifiable $entity): HtmlNumber {
		$data = $this->statistics($subject, $entity);
		if (!($data instanceof Number)) {
			$data = new Number();
		}
		return new HtmlNumber($data);
	}

	public function numberStatisticsOrNull(Subject $subject, Identifiable $entity): ?HtmlNumber {
		$data = $this->statistics($subject, $entity);
		if (!($data instanceof Number) || !$data->value && !$data->change) {
			return null;
		}
		return new HtmlNumber($data);
	}

	/**
	 * @param array<string, Subject> $subjects
	 * @return array<string, HtmlNumber>
	 */
	public function multipleStatistics(array $subjects, Region $region): array {
		foreach ($region->Residents() as $unit) {
			if ($unit->Party() === $this->party) {
				break;
			}
		}
		$statistics = [];
		foreach ($subjects as $name => $subject) {
			/** @noinspection PhpUndefinedVariableInspection */
			$number = $this->statistics($subject, $unit);
			if (!($number instanceof Number) || !$number->value && !$number->change) {
				continue;
			}
			$statistics[$name] = new HtmlClassNumber($number, strtolower($subject->name));
		}
		return $statistics;
	}

	/**
	 * @return array<HtmlCommodity>
	 */
	public function animalStatistics(Subject $subject, Region $region): array {
		$statistics = [];
		foreach (Transport::ANIMALS as $class) {
			$statistics[getClass($class)] = null;
		}
		$commodities = $this->statistics($subject, $region);
		if ($commodities) {
			foreach ($commodities as $class => $number) {
				$statistics[$class] = new HtmlCommodity($number, $class);
			}
		}
		foreach (array_keys($statistics) as $class) {
			if (!$statistics[$class]) {
				unset($statistics[$class]);
			}
		}
		return array_values($statistics);
	}

	/**
	 * @return array<HtmlCommodity>
	 */
	public function materialPoolStatistics(Subject $subject, Party $party): array {
		$statistics  = array_fill_keys(Resources::getAll(), null);
		$commodities = $this->statistics($subject, $party);
		if ($commodities) {
			foreach ($commodities as $class => $number) {
				$statistics[$class] = new HtmlCommodity($number, $class);
			}
		}
		foreach (array_keys($statistics) as $class) {
			if (!$statistics[$class]) {
				unset($statistics[$class]);
			}
		}
		return array_values($statistics);
	}

	/**
	 * @return array<HtmlCommodity>
	 */
	public function regionPoolStatistics(Unit $unit): array {
		$statistics  = array_fill_keys(Resources::getAll(), null);
		$commodities = $this->statistics(Subject::RegionPool, $unit);
		if ($commodities) {
			foreach ($commodities as $class => $number /** @var Number $number */) {
				if ($number->value > 0) {
					$statistics[$class] = new HtmlMaterial($number, $class, $this);
				}
			}
		}
		foreach (array_keys($statistics) as $class) {
			if (!$statistics[$class]) {
				unset($statistics[$class]);
			}
		}
		return array_values($statistics);
	}

	public function regionSilverStatistics(?Unit $unit): HtmlNumber {
		if (!$unit) {
			return new HtmlNumber(new Number());
		}
		$commodities = $this->statistics(Subject::RegionPool, $unit);
		$class       = getClass(Silver::class);
		$silver      = $commodities[$class] ?? new Number();
		return new HtmlNumber($silver);
	}

	/**
	 * @return array<HtmlMarket>
	 */
	public function marketStatistics(Subject $subject, Region $region): array {
		$statistics = [];
		$market     = $this->statistics($subject, $region);
		if (!$market) {
			return $statistics;
		}

		$offer = getClass($region->Luxuries()->Offer()->Commodity());
		foreach (Luxuries::LUXURIES as $class) {
			$class              = getClass($class);
			$number             = new HtmlMarket($market[$class], $class);
			$statistics[$class] = $number->setIsOffer($class === $offer);
		}
		return array_values($statistics);
	}

	/**
	 * @return array<HtmlCommodity>
	 */
	public function expertsStatistics(Subject $subject, Party $party): array {
		$statistics = [];
		$experts    = $this->statistics($subject, $party);
		if ($experts) {
			foreach ($experts as $class => $prognosis) {
				$translation              = $this->get('talent.' . $class);
				$statistics[$translation] = new HtmlPrognosis($prognosis, $class);
			}
		}
		ksort($statistics);
		return array_values($statistics);
	}

	/**
	 * @return array<HtmlQualification>
	 */
	public function qualificationStatistics(Subject $subject, Unit $unit): array {
		$statistics    = [];
		$qualification = $this->statistics($subject, $unit);
		if ($qualification) {
			foreach ($qualification as $class => $values) {
				$translation              = $this->get('talent.' . $class);
				$statistics[$translation] = new HtmlQualification($values, $class);
			}
		}
		ksort($statistics);
		return array_values($statistics);
	}

	protected function battleLogPath(BattleLog $battleLog): string {
		return basename($this->pathFactory->getPath(new BattleLogWriter($this->pathFactory), $battleLog));
	}

	protected function spellBookPath(): string {
		return basename($this->pathFactory->getPath(new SpellBookWriter($this->pathFactory)));
	}

	protected function herbalBookPath(): string {
		return basename($this->pathFactory->getPath(new HerbalBookWriter($this->pathFactory)));
	}

	protected function unicumPath(Unicum $unicum): string {
		return basename($this->pathFactory->getPath(new UnicumWriter($this->pathFactory), $unicum));
	}

	/**
	 * Generate HTML output.
	 */
	protected function generateContent(string $template): string {
		ob_start();
		$result = @include __DIR__ . '/../../templates/html/' . $template . '.php';
		$output = ob_get_clean();
		if ($result) {
			return $output;
		}
		throw new \RuntimeException('Template error.' . ($output ? PHP_EOL . $output : ''));
	}
}
