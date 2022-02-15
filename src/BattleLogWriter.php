<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Engine\Combat\Battle;
use Lemuria\Engine\Fantasya\Combat\Log\Message;
use Lemuria\Engine\Message\Filter;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Dictionary;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\Writer;

class BattleLogWriter implements Writer
{
	use VersionTrait;

	protected readonly Dictionary $dictionary;

	public final const LOCATION_PLACEHOLDER = '%LOC%';

	protected final const START_SECTION = [
		'BattleBeginsMessage'         => true, 'BattleEndsMessage'           => true,
		'AttackerTacticsRoundMessage' => true, 'DefenderTacticsRoundMessage' => true, 'NoTacticsRoundMessage' => true,
		'AttackerOverrunMessage'      => true, 'DefenderOverrunMessage'      => true,
		'CombatRoundMessage'          => true,
		'BattleEndedInDrawMessage'    => true, 'BattleExhaustionMessage'     => true
	];

	protected final const CENTER_MESSAGE = ['BattleEndsMessage' => true, 'CombatRoundMessage' => true];

	public function __construct(private string $pathPattern) {
		$this->dictionary = new Dictionary();
	}

	public function setFilter(Filter $filter): Writer {
		return $this;
	}

	public function render(Id $party): Writer {
		foreach (Lemuria::Hostilities()->findFor(Party::get($party)) as $battleLog) {
			if ($battleLog->count()) {
				/** @var Region $region */
				$region = $battleLog->Location();
				$path   = str_replace(self::LOCATION_PLACEHOLDER, (string)$region->Id(), $this->pathPattern);
				if (!file_put_contents($path, $this->generate($battleLog, $region))) {
					throw new \RuntimeException('Could not create battle log.');
				}
			}
		}
		return $this;
	}

	protected function generate(Battle $log, Region $region): string {
		$output = $this->generateHeader($region);
		foreach ($log as $message /* @var Message $message */) {
			$class = getClass($message);
			if (isset(self::START_SECTION[$class])) {
				$output .= PHP_EOL;
			}
			if (isset(self::CENTER_MESSAGE[$class])) {
				$output .= center((string)$message) . PHP_EOL;
			} else {
				$output .= wrap((string)$message);
			}
		}
		return $output;
	}

	protected function generateHeader(Region $region): string {
		$calendar  = Lemuria::Calendar();
		$month     = $this->dictionary->get('calendar.month', $calendar->Month() - 1);
		$landscape = $this->dictionary->get('landscape', $region->Landscape());
		$where     = $landscape . ' ' . $region->Name();
		$when      = $calendar->Week() . '. Woche im Monat ' . $month . ', Jahr ' . $calendar->Year();
		$locality  = $where . ', ' . $when . ' (Runde ' . $calendar->Round() . ')';
		return center('Kampfbericht') . center('~~~~~~~~~~~~~') . center($locality);
	}
}
