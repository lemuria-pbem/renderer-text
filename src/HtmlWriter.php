<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Model\Fantasya\Party;
use Lemuria\Renderer\Text\View\Html;

final class HtmlWriter extends FileWriter
{
	/**
	 * @noinspection PhpPureAttributeCanBeAddedInspection
	 */
	protected function getView(Party $party): View {
		return new Html($party);
	}
}
