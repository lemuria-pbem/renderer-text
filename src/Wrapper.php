<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

interface Wrapper
{
	public final const CREATED = '%CREATED%';

	public final const MOVE = '%MOVE%';

	public final const REPORT ='%WRAPPED_REPORT%';

	public final const PARTY = '%PARTY%';

	public final const VERSION = '%VERSION%';

	public function wrap(string $report): string;
}
