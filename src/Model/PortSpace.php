<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Model;

use Lemuria\Model\Fantasya\Construction;
use Lemuria\Model\Fantasya\Vessel;

class PortSpace implements \Stringable
{
	private int $space;

	public function __construct(Construction $port) {
		$this->space = $port->Size();
		foreach ($port->Region()->Fleet() as $vessel/* @var Vessel $vessel */) {
			if ($vessel->Port() === $port) {
				$this->space -= $vessel->Ship()->Captain();
			}
		}
	}

	public function __toString(): string {
		return match(true) {
			$this->space < 0   => 'Der Hafen ist überfüllt.',
			$this->space === 0 => 'Alle Ankerplätze sind belegt.',
			default            => 'Freie Ankerplätze: ' . $this->space . '.'
		};
	}
}
