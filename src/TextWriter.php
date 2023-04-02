<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Renderer\Text\View\Text;

final class TextWriter extends FileWriter
{
	protected function createView(): View {
		return new Text($this);
	}
}
