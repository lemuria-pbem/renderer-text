<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

interface Wrapper
{
	public final const string CREATED = '%CREATED%';

	public final const string MOVE = '%MOVE%';

	public final const string REPORT ='%WRAPPED_REPORT%';

	public final const string PARTY = '%PARTY%';

	public final const string VERSION = '%VERSION%';

	public function wrap(string $report): string;
}
