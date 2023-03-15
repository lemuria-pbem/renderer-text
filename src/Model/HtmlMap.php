<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Model;

use function Lemuria\Renderer\Text\View\id;
use Lemuria\Engine\Fantasya\Factory\Model\TravelAtlas;
use Lemuria\Model\Fantasya\Region;
use Lemuria\Model\Fantasya\World\PartyMap;
use Lemuria\Model\World\Direction;
use Lemuria\Renderer\Text\View\Html;

/**
 * This is a decorated world that calculates map coordinates for a specific party.
 */
final class HtmlMap implements \Iterator
{
	private readonly PartyMap $map;

	private readonly TravelAtlas $atlas;

	private Region $key;

	private int $xOffset = PHP_INT_MAX;

	private int $yOffset = 0;

	public function __construct(Html $view) {
		$this->map   = $view->map;
		$this->atlas = $view->atlas;
		$this->calculateOffset();
	}

	public function current(): HtmlMap {
		return $this;
	}

	public function key(): Region {
		return $this->key;
	}

	public function next(): void {
		$this->atlas->next();
	}

	public function rewind(): void {
		$this->atlas->rewind();
	}

	public function valid(): bool {
		if ($this->atlas->valid()) {
			/** @var Region $region */
			$region    = $this->atlas->current();
			$this->key = $region;
			return true;
		}
		return false;
	}

	public function coordinates(): string {
		$coordinates = $this->map->getCoordinates($this->key);
		$x           = $coordinates->X();
		$y           = $coordinates->Y();
		$left        = $this->xOffset + $x * 64 + $y * 32;
		$top         = $this->yOffset - $y * 48;
		return 'style="left: ' . $left . 'px; top: ' . $top . 'px;"';
	}

	public function id(): string {
		return id($this->key, 'map');
	}

	public function landscape(): string {
		return strtolower((string)$this->key->Landscape());
	}

	public function location(): string {
		$coordinates = $this->map->getCoordinates($this->key);
		return 'data-location-x="' . $coordinates->X() . '" data-location-y="' . $coordinates->Y() . '"';
	}

	public function mapClass(): string {
		return $this->map->isDirection(Direction::North) ? 'map-octagonal' : 'map-hexagonal';
	}

	public function name(): string {
		return $this->key->Name();
	}

	public function offset(): string {
		return 'data-offset-x="' . $this->xOffset . '" data-offset-y="' . $this->yOffset . '"';
	}

	public function title(): string {
		return $this->map->getCoordinates($this->key) . ' ' . $this->key;
	}

	protected function calculateOffset(): void {
		$minY = 0;
		foreach ($this->atlas as $region) {
			$coordinates   = $this->map->getCoordinates($region);
			$this->xOffset = min($this->xOffset, $coordinates->X());
			$this->yOffset = max($this->yOffset, $coordinates->Y());
			$minY          = min($minY, $coordinates->Y());
		}
		if ($this->xOffset === PHP_INT_MAX) {
			$this->xOffset = 0;
			return;
		}
		$this->xOffset  = abs(64 * $this->xOffset + 32 * ($this->yOffset + $minY) - 96);
		$this->yOffset *= 48;
	}
}
