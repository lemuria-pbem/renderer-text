<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use function Lemuria\Renderer\Text\View\wrap;
use Lemuria\Engine\Fantasya\Factory\Model\SpellDetails;
use Lemuria\Engine\Fantasya\Factory\SpellParser;
use Lemuria\Engine\Message\Filter;
use Lemuria\Id;
use Lemuria\Model\Fantasya\Exception\JsonException;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Model\Fantasya\Spell;
use Lemuria\Renderer\Writer;

class SpellBookWriter implements Writer
{
	use VersionTrait;

	public function __construct(private readonly string $path) {
	}

	public function setFilter(Filter $filter): Writer {
		return $this;
	}

	/**
	 * @throws JsonException
	 */
	public function render(Id $party): Writer {
		if (!file_put_contents($this->path, $this->generate($party))) {
			throw new \RuntimeException('Could not create spell book.');
		}
		return $this;
	}

	/**
	 * @throws JsonException
	 */
	protected function generate(Id $id): string {
		$party  = Party::get($id);
		$output = '';
		$n      = 0;
		foreach ($party->SpellBook() as $spell /* @var Spell $spell */) {
			$details    = new SpellDetails($spell);
			$components = $details->Components();

			if ($n++ > 0) {
				$output .= PHP_EOL . PHP_EOL;
			}

			$output .= $details->Name() . PHP_EOL . PHP_EOL;
			foreach ($details->Description() as $description) {
				$output .= wrap($description);
			}
			$output .= PHP_EOL;
			$output .= 'Talentstufe: ' . $spell->Difficulty() . PHP_EOL;

			if ($details->IsBattleSpell()) {
				$output .= 'Kampfzauber (' . $details->CombatPhase() . ')' . PHP_EOL;
			} else {
				$output .= 'Rang: ' . ($spell->Order() + 1) . PHP_EOL;
			}
			if ($spell->IsIncremental()) {
				$output .= 'Aura: ' . $spell->Aura() . ' * Stufe' . PHP_EOL;
			} else {
				if (SpellParser::getSyntax($spell) === SpellParser::REGION) {
					$output .= 'Aura: ' . $spell->Aura() . ' + Entfernung' . PHP_EOL;
				} else {
					$output .= 'Aura: ' . $spell->Aura() . PHP_EOL;
				}
			}
			if ($components) {
				$output .= 'Komponenten: ' . implode(', ', $components) . PHP_EOL;
			}

			$output .= 'Syntax: ' . $details->Syntax() . PHP_EOL;
		}
		return $output;
	}
}
