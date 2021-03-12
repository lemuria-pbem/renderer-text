<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Wrapper;

use Lemuria\Exception\FileNotFoundException;
use Lemuria\Renderer\Text\Wrapper;

class FileWrapper implements Wrapper
{
	protected string $wrapperText;

	public function __construct(string $path) {
		if (!is_file($path)) {
			throw new FileNotFoundException($path);
		}
		$this->wrapperText = file_get_contents($path);
	}

	public function wrap(string $report): string {
		return str_replace(Wrapper::REPORT, $report, $this->wrapperText);
	}
}
