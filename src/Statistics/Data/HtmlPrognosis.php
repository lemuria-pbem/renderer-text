<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

use function Lemuria\number;
use Lemuria\Statistics\Data\Prognosis;

class HtmlPrognosis extends HtmlClassNumber
{
	public string $prognosis;

	public function __construct(Prognosis $prognosis, string $class = '') {
		parent::__construct($prognosis, $class);
		$this->prognosis = $this->getPrognosis($prognosis);
	}

	private function getPrognosis(Prognosis $prognosis): string {
		return match (true) {
			$prognosis->eta > 0 => '≈ ' . number($prognosis->eta),
			default             => '∞'
		};
	}
}
