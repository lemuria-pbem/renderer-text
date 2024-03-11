<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Engine;

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

	/**
	 * @type array<string, string>
	 */
	protected const array FILTER = [
		LayaboutMessage::class     => self::LAYABOUT,
		LearnReducedMessage::class => self::ABOARD, LearnVesselMessage::class      => self::ABOARD,
		RecreateAuraMessage::class => self::RECREATE, RecreateHealthMessage::class => self::RECREATE,
		VisitMessage::class        => self::ANNOUNCE, VisitRumorMessage::class     => self::ANNOUNCE
	];

	public function getSection(LemuriaMessage $message): ?string {
		$type = $message->MessageType()::class;
		return self::FILTER[$type] ?? null;
	}
}
