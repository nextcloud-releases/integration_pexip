<?php
namespace OCA\Pexip\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\Settings\ISettings;
use OCA\Pexip\AppInfo\Application;

class Admin implements ISettings {

	private IConfig $config;
	private IInitialState $initialStateService;

	public function __construct(IConfig $config,
								IInitialState $initialStateService) {
		$this->config = $config;
		$this->initialStateService = $initialStateService;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$pexipUrl = $this->config->getAppValue(Application::APP_ID, 'pexip_url');

		$adminConfig = [
			'pexip_url' => $pexipUrl,
		];
		$this->initialStateService->provideInitialState('admin-config', $adminConfig);

		return new TemplateResponse(Application::APP_ID, 'adminSettings');
	}

	public function getSection(): string {
		return 'connected-accounts';
	}

	public function getPriority(): int {
		return 10;
	}
}
