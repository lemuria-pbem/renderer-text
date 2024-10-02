<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use function Lemuria\getClass;
use function Lemuria\Renderer\Text\View\center;
use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Dispatcher\Attribute\Emit;
use Lemuria\Dispatcher\Event\Renderer\Written;
use Lemuria\Engine\Combat\Battle;
use Lemuria\Engine\Fantasya\Combat\Log\Message;
use Lemuria\Engine\Fantasya\Factory\GrammarTrait;
use Lemuria\Id;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Renderer\PathFactory;

class BattleLogWriter extends AbstractWriter
{
	use GrammarTrait;
	use VersionTrait;

	/**
	 * @type array<string, true>
	 */
	protected final const array START_SECTION = [
		'BattleBeginsMessage'      => true, 'BattleEndsMessage'       => true,
		'AttackerSideMessage'      => true, 'DefenderSideMessage'     => true,
		'TacticsRoundMessage'      => true, 'NoTacticsRoundMessage'   => true, 'CombatRoundMessage' => true,
		'BattleEndedInDrawMessage' => true, 'BattleExhaustionMessage' => true
	];

	/**
	 * @type array<string, true>
	 */
	protected final const array CENTER_MESSAGE = [
		'TacticsRoundMessage' => true, 'CombatRoundMessage' => true, 'BattleEndsMessage' => true
	];

	public function __construct(PathFactory $pathFactory) {
		parent::__construct($pathFactory);
		$this->initDictionary();
	}

	#[Emit(Written::class, 'The event is emitted for every battle log of the given party.')]
	public function render(Id $entity): static {
		$dispatcher = Lemuria::Dispatcher();
		foreach (Lemuria::Hostilities()->findFor(Party::get($entity)) as $battleLog) {
			if ($battleLog->count()) {
				/** @var Region $region */
				$region = $battleLog->Location();
				$path   = $this->pathFactory->getPath($this, $battleLog);
				if (!file_put_contents($path, $this->generate($battleLog, $region))) {
					throw new \RuntimeException('Could not create battle log.');
				}
				$dispatcher->dispatch(new Written($this, $entity, $path));
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
