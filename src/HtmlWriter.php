<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Renderer\Text\View\Html;

final class HtmlWriter extends FileWriter
{
	protected function createView(): View {
		return new Html($this);
	}
}
