<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Text\View\Text;

final class TextWriter extends FileWriter
{
	protected function getView(Party $party): View {
		return new Text($party, $this->messageFilter);
	}
}
