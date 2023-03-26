<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Wrapper;

use Lemuria\Exception\FileNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Text\Wrapper;
use Lemuria\Version\Module;

class FileWrapper implements Wrapper
{
	protected Party $party;

	protected string $wrapperText;

	public function __construct(string $path) {
		if (!is_file($path)) {
			throw new FileNotFoundException($path);
		}
		$this->wrapperText = file_get_contents($path);
	}

	public function setParty(Party $party): FileWrapper {
		$this->party = $party;
		return $this;
	}

	/**
	 * @noinspection PhpUnnecessaryLocalVariableInspection
	 */
	public function wrap(string $report): string {
		$version = Lemuria::Version();
		$wrapper = str_replace(Wrapper::VERSION, $version[Module::Game][0]->version, $this->wrapperText);
		$wrapper = str_replace(Wrapper::UUID, $this->party->Uuid(), $wrapper);
		$report  = str_replace(Wrapper::REPORT, $report, $wrapper);
		return $report;
	}
}
