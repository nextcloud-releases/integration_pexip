<?php
/**
 * Nextcloud - Pexip
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 * @copyright Julien Veyssier 2023
 */

namespace OCA\Pexip\Controller;

use OCA\Pexip\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\DB\Exception;
use OCP\IConfig;
use OCP\IRequest;

use OCA\Pexip\Service\PexipService;

class PexipAPIController extends Controller {

	private PexipService $pexipService;
	private IInitialState $initialStateService;
	private ?string $userId;
	private IConfig $config;

	public function __construct(string $appName,
								IRequest $request,
								IConfig $config,
								PexipService $pexipService,
								IInitialState $initialStateService,
								?string $userId) {
		parent::__construct($appName, $request);
		$this->pexipService = $pexipService;
		$this->initialStateService = $initialStateService;
		$this->userId = $userId;
		$this->config = $config;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $pexipId
	 * @return DataResponse
	 * @throws MultipleObjectsReturnedException
	 * @throws Exception
	 */
	public function checkCall(string $pexipId): DataResponse {
		$response = $this->pexipService->checkCall($pexipId);
		if (isset($response['status']) && $response['status'] === 'fail') {
			return new DataResponse($response, Http::STATUS_NOT_FOUND);
		}
		return new DataResponse($response);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function getUserCalls(): DataResponse {
		$response = $this->pexipService->getUserCalls($this->userId);
		return new DataResponse($response);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $description
	 * @param string $pin
	 * @param string $guestPin
	 * @param bool $guestsCanPresent
	 * @param bool $allowGuests
	 * @return DataResponse
	 */
	public function createCall(string $description, string $pin = '', string $guestPin = '',
							   bool $guestsCanPresent = true, bool $allowGuests = true): DataResponse {
		$response = $this->pexipService->createCall($this->userId, $description, $pin, $guestPin, $guestsCanPresent, $allowGuests);
		if (isset($response['error'])) {
			return new DataResponse($response, Http::STATUS_BAD_REQUEST);
		}
		return new DataResponse($response);
	}
}
