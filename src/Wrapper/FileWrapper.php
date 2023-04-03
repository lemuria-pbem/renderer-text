<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Wrapper;

use function Lemuria\Renderer\Text\dateTimeIso8601;
use Lemuria\Exception\FileNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Text\FileWriter;
use Lemuria\Renderer\Text\Wrapper;
use Lemuria\Version\Module;

class FileWrapper implements Wrapper
{
	protected FileWriter $writer;

	protected ?int $received = null;

	protected string $wrapperText;

	public function __construct(string $path) {
		if (!is_file($path)) {
			throw new FileNotFoundException($path);
		}
		$this->wrapperText = file_get_contents($path);
	}

	public function setWriter(FileWriter $writer): FileWrapper {
		$this->writer = $writer;
		return $this;
	}

	public function setReceived(?int $received): FileWrapper {
		$this->received = $received;
		return $this;
	}

	/**
	 * @noinspection PhpUnnecessaryLocalVariableInspection
	 */
	public function wrap(string $report): string {
		$party    = $this->writer->getParty();
		$view     = $this->writer->getView();
		$created  = dateTimeIso8601($view->getCreatedTimestamp());
		$version  = Lemuria::Version();
		$wrapper  = str_replace(Wrapper::CREATED, $created, $this->wrapperText);
		$wrapper  = str_replace(Wrapper::MOVE, $this->createMove(), $wrapper);
		$wrapper  = str_replace(Wrapper::PARTY, $this->createParty($party), $wrapper);
		$wrapper  = str_replace(Wrapper::VERSION, $version[Module::Game][0]->version, $wrapper);
		$report   = str_replace(Wrapper::REPORT, $report, $wrapper);
		return $report;
	}

	private function createParty(Party $party): string {
		return 'uuid=' . $party->Uuid();
	}

	private function createMove(): string {
		return match ($this->received) {
			null    => 'status=unknown',
			0       => 'status=none',
			default => 'status=sent, received=' . dateTimeIso8601($this->received)
		};
	}
}
