<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Model;

use Lemuria\Engine\Fantasya\Command\Comment;
use Lemuria\Engine\Fantasya\Context;
use Lemuria\Engine\Fantasya\Factory\CommandFactory;
use Lemuria\Engine\Fantasya\Phrase;
use Lemuria\Engine\Fantasya\State;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Unit;
use Lemuria\Scenario\Fantasya\ScenarioOrders;

class Orders
{
	public array $orders = [];

	public array $comments = [];

	public array $acts = [];

	public function __construct(Unit $unit) {
		$context = new Context(new State());
		$factory = new CommandFactory($context->setUnit($unit));
		$orders  = Lemuria::Orders();
		foreach ($orders->getCurrent($unit->Id()) as $command) {
			$comment = $factory->create(new Phrase($command))->getDelegate();
			if ($comment instanceof Comment) {
				$line = trim($comment->Line());
				if ($line) {
					$this->comments[] = $line;
				}
			} else {
				$this->orders[] = trim($command);
			}
		}
		if ($orders instanceof ScenarioOrders) {
			foreach ($orders->getScenario($unit->Id()) as $act) {
				$this->acts[] = trim($act);
			}
		}
	}
}
