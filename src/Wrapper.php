<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

interface Wrapper
{
	public const REPORT ='%WRAPPED_REPORT%';

	public function wrap(string $report): string;
}
