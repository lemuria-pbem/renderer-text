<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Text;

use JetBrains\PhpStorm\Pure;

class TableRow implements \Stringable
{
	private const LENGTH = 11;

	private const MAX_NAME_LENGTH = 25;

	private const SEPARATION = 3;

	public string $value;

	public string $change;

	#[Pure] public function __construct(private string $name, string $value, string $change) {
		$this->value  = sprintf('%-' . self::LENGTH . 's', $value);
		$this->change = sprintf('%-' . self::LENGTH . 's', $change);
	}

	public function __toString(): string {
		$strLength = strlen($this->name);
		$mbLength  = mb_strlen($this->name);
		$l         = self::MAX_NAME_LENGTH + $strLength - $mbLength;
		$sep       = str_pad(' ', self::SEPARATION);
		return trim(sprintf('%-' . $l . 's', $this->name) . $sep . $this->value . $sep . $this->change) . PHP_EOL;
	}
}