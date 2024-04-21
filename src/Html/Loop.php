<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Html;

class Loop implements \Stringable
{
	protected const int COUNT = 0;

	protected const string ACTIVE = 'active';

	protected string $alwaysPrefix = '';

	protected string $prefix = '';

	protected string $text = '';

	public static function classList(string $always = '', string $prefix = ''): self {
		$loop   = new self();
		$always = trim($always);
		$loop->setAlways($always);
		if ($always) {
			$prefix = trim($prefix);
			$loop->setPrefix($prefix ? ' ' . $prefix . ' ' : ' ');
		} elseif ($prefix) {
			$loop->setPrefix(trim($prefix) . ' ');
		}
		return $loop;
	}

	public function __construct(protected int $count = self::COUNT) {
	}

	public function __toString():string {
		return $this->alwaysPrefix . $this->text;
	}

	public function active(int $when = self::COUNT): static {
		$this->text = $this->count++ === $when ? $this->prefix . self::ACTIVE : '';
		return $this;
	}

	public function setAlways(string $prefix): static {
		$this->alwaysPrefix = $prefix;
		return $this;
	}

	public function setPrefix(string $prefix): static {
		$this->prefix = $prefix;
		return $this;
	}
}
