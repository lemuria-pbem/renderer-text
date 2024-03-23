<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Engine;

use Lemuria\Engine\Fantasya\Message\Region\AttackBattleMessage;
use Lemuria\Engine\Fantasya\Message\Unit\AttackBoardAfterCombatMessage;
use Lemuria\Engine\Fantasya\Message\Unit\AttackEnterAfterCombatMessage;
use Lemuria\Engine\Fantasya\Message\Unit\AttackLeaveConstructionCombatMessage;
use Lemuria\Engine\Fantasya\Message\Unit\AttackLeaveVesselCombatMessage;
use function Lemuria\getClass;
use Lemuria\Engine\Fantasya\Message\LemuriaMessage;
use Lemuria\Engine\Fantasya\Message\Unit\LayaboutMessage;
use Lemuria\Engine\Fantasya\Message\Unit\LearnReducedMessage;
use Lemuria\Engine\Fantasya\Message\Unit\LearnVesselMessage;
use Lemuria\Engine\Fantasya\Message\Unit\RecreateAuraMessage;
use Lemuria\Engine\Fantasya\Message\Unit\RecreateHealthMessage;
use Lemuria\Engine\Fantasya\Message\Unit\VisitMessage;
use Lemuria\Engine\Fantasya\Message\Unit\VisitRumorMessage;

class SectionFilter
{
	protected const string LAYABOUT = 'filter-layabout';

	protected const string ABOARD = 'filter-aboard';

	protected const string RECREATE = 'filter-recreate';

	protected const string ANNOUNCE = 'filter-announce';

	protected const string COMBAT = 'filter-combat';

	/**
	 * @type array<string, string>
	 */
	protected const array FILTER = [
		AttackBattleMessage::class                  => self::COMBAT,
		AttackEnterAfterCombatMessage::class        => self::COMBAT, AttackBoardAfterCombatMessage::class  => self::COMBAT,
		AttackLeaveConstructionCombatMessage::class => self::COMBAT, AttackLeaveVesselCombatMessage::class => self::COMBAT,

		LayaboutMessage::class     => self::LAYABOUT,

		LearnReducedMessage::class => self::ABOARD, LearnVesselMessage::class      => self::ABOARD,

		RecreateAuraMessage::class => self::RECREATE, RecreateHealthMessage::class => self::RECREATE,

		VisitMessage::class        => self::ANNOUNCE, VisitRumorMessage::class     => self::ANNOUNCE
	];

	/**
	 * @type array<string, string>
	 */
	protected const array PREFIXES = [
		'Announcement' => self::ANNOUNCE
	];

	public function getSection(LemuriaMessage $message, ?string $default = null): ?string {
		$type = $message->MessageType()::class;
		$name = getClass($type);
		foreach (self::PREFIXES as $prefix => $filter) {
			if (str_starts_with($name, $prefix)) {
				return $filter;
			}
		}
		return self::FILTER[$type] ?? $default;
	}
}
