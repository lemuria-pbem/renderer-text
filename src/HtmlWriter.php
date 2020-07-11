<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Model\Lemuria\Party;
use Lemuria\Renderer\Text\View\Html;

final class HtmlWriter extends FileWriter
{
	/**
	 * @param Party $party
	 * @return View
	 */
	protected function getView(Party $party): View {
		return new Html($party);
	}
}
