<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Wrapper;

use Lemuria\Exception\FileNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Text\FileWriter;
use Lemuria\Renderer\Text\Wrapper;
use Lemuria\Version\Module;

class FileWrapper implements Wrapper
{
	protected FileWriter $writer;

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

	/**
	 * @noinspection PhpUnnecessaryLocalVariableInspection
	 */
	public function wrap(string $report): string {
		$party   = $this->writer->getParty();
		$created = $this->writer->getView()->createdIso8601();
		$version = Lemuria::Version();
		$wrapper = str_replace(Wrapper::CREATED, $created, $this->wrapperText);
		$wrapper = str_replace(Wrapper::VERSION, $version[Module::Game][0]->version, $wrapper);
		$wrapper = str_replace(Wrapper::UUID, $party->Uuid(), $wrapper);
		$report  = str_replace(Wrapper::REPORT, $report, $wrapper);
		return $report;
	}
}
