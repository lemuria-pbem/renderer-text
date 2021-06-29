<?php
declare(strict_types = 1);
namespace Lemuria\Renderer\Text;

use Lemuria\Version\VersionFinder;
use Lemuria\Version\VersionTag;

trait VersionTrait
{
	public function getVersion(): VersionTag {
		$versionFinder = new VersionFinder(__DIR__ . '/..');
		return $versionFinder->get();
	}
}
