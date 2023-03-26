<?php

namespace OCA\Pexip\Tests;

use OCA\Pexip\AppInfo\Application;

class PexipServiceTest extends \PHPUnit\Framework\TestCase {

	public function testDummy() {
		$app = new Application();
		$this->assertEquals('integration_pexip', $app::APP_ID);
	}
}
