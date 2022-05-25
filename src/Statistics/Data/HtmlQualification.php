<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text\Statistics\Data;

class HtmlQualification
{
	public array $level = [0, 0, 0];

	public array $prognosis = [null, null, null];

	public function __construct(array $qualification, public string $class) {
		$i = 0;
		foreach ($qualification as $level => $prognosis) {
			$this->level[$i]     = $level;
			$this->prognosis[$i] = new HtmlPrognosis($prognosis);
			if (++$i >= 3) {
				break;
			}
		}
	}
}
