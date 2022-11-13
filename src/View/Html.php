<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\View;

use function Lemuria\getClass;
use Lemuria\Engine\Fantasya\Combat\BattleLog;
use Lemuria\Engine\Fantasya\Statistics\Subject;
use Lemuria\Engine\Message;
use Lemuria\Identifiable;
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
		0       => 'p-0 pr-' . $bp . '-3',
		1       => 'p-0 pl-' . $bp . '-3 pr-xl-3',
		2       => 'p-0 pr-' . $bp . '-3 pl-xl-3 pr-xl-0',
		3       => 'p-0 pl-' . $bp . '-3 pl-xl-0 pr-xl-3',
		4       => 'p-0 pr-' . $bp . '-3 pl-xl-3',
		default => 'p-0 pl-' . $bp . '-3 pl-xl-3 pr-xl-0'
	};
}

class Html extends View
{
	protected const BADGE_UNDEFINED = 'dark';

	protected const BADGE = [
		Message::DEBUG   => 'light',
		Message::ERROR   => 'danger',
		Message::EVENT   => 'info',
		Message::FAILURE => 'warning',
		Message::SUCCESS => 'success'
	];

	protected const LEVEL_UNDEFINED = 'U';

	protected const LEVEL = [
		Message::DEBUG => 'D', Message::ERROR => 'F', Message::EVENT => 'E', Message::FAILURE => 'W', Message::SUCCESS => 'M'
	];

	private PathFactory $pathFactory;

	public function __construct(Party $party, FileWriter $writer) {
		parent::__construct($party, $writer);
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
		$level = $message->Level();
		$badge = self::BADGE[$level] ?? self::BADGE_UNDEFINED;
		$b     = self::LEVEL[$level] ?? self::LEVEL_UNDEFINED;
		return '<span class="badge text-bg-' . $badge . ' font-monospace">' . $b . '</span>&nbsp;' . $message;
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
		foreach ($region->Residents() as $unit /* @var Unit $unit */) {
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
	 * @return HtmlCommodity[]
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
	 * @return HtmlCommodity[]
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
	 * @return HtmlCommodity[]
	 */
	public function regionPoolStatistics(Subject $subject, Unit $unit): array {
		$statistics  = array_fill_keys(Resources::getAll(), null);
		$commodities = $this->statistics($subject, $unit);
		if ($commodities) {
			foreach ($commodities as $class => $number /* @var Number $number */) {
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

	/**
	 * @return HtmlMarket[]
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
	 * @return HtmlCommodity[]
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
	 * @return HtmlQualification[]
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
