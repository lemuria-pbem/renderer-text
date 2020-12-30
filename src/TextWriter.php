<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Model\Lemuria\Party;
use Lemuria\Renderer\Text\View\Text;

final class TextWriter extends FileWriter
{
	/**
	 * @noinspection PhpPureAttributeCanBeAddedInspection
	 */
	protected function getView(Party $party): View {
		return new Text($party);
	}
}
