<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Engine\Combat\Battle;
use Lemuria\Engine\Fantasya\Combat\Log\Message;
use Lemuria\Engine\Fantasya\Factory\GrammarTrait;
use Lemuria\Engine\Fantasya\Message\Casus;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\PathFactory;
use Lemuria\Renderer\Writer;

class BattleLogWriter extends AbstractWriter
{
	use GrammarTrait;
	use VersionTrait;

	protected final const START_SECTION = [
		'BattleBeginsMessage'         => true, 'BattleEndsMessage'           => true,
		'AttackerTacticsRoundMessage' => true, 'DefenderTacticsRoundMessage' => true, 'NoTacticsRoundMessage' => true,
		'AttackerOverrunMessage'      => true, 'DefenderOverrunMessage'      => true,
		'CombatRoundMessage'          => true,
		'BattleEndedInDrawMessage'    => true, 'BattleExhaustionMessage'     => true
	];

	protected final const CENTER_MESSAGE = ['BattleEndsMessage' => true, 'CombatRoundMessage' => true];

	public function __construct(PathFactory $pathFactory) {
		parent::__construct($pathFactory);
		$this->initDictionary();
	}

	public function render(Id $entity): Writer {
		foreach (Lemuria::Hostilities()->findFor(Party::get($entity)) as $battleLog) {
			if ($battleLog->count()) {
				$path = $this->pathFactory->getPath($this, $battleLog);
				if (!file_put_contents($path, $this->generate($battleLog, $battleLog->Location()))) {
					throw new \RuntimeException('Could not create battle log.');
				}
			}
		}
		return $this;
	}

	protected function generate(Battle $log, Region $region): string {
		$output = $this->generateHeader($region);
		foreach ($log as $message /** @var Message $message */) {
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
		$landscape = $this->translateSingleton($region->Landscape());
		$where     = $landscape . ' ' . $region->Name();
		$when      = $calendar->Week() . '. Woche im Monat ' . $month . ', Jahr ' . $calendar->Year();
		$locality  = $where . ', ' . $when . ' (Runde ' . $calendar->Round() . ')';
		return center('Kampfbericht') . center('~~~~~~~~~~~~~') . center($locality);
	}
}
