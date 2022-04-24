<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Wrapper;

use Lemuria\Exception\FileNotFoundException;
use Lemuria\Lemuria;
use Lemuria\Renderer\Text\Wrapper;
use Lemuria\Version;

class FileWrapper implements Wrapper
{
	protected string $wrapperText;

	public function __construct(string $path) {
		if (!is_file($path)) {
			throw new FileNotFoundException($path);
		}
		$this->wrapperText = file_get_contents($path);
	}

	/**
	 * @noinspection PhpUnnecessaryLocalVariableInspection
	 */
	public function wrap(string $report): string {
		$version = Lemuria::Version();
		$report  = str_replace(Wrapper::REPORT, $report, $this->wrapperText);
		$report  = str_replace(Wrapper::VERSION, $version[Version::GAME][0]->version, $report);
		return $report;
	}
}
