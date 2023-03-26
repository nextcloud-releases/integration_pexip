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

use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\DB\Exception;
use OCP\IRequest;

use OCA\Pexip\Service\PexipService;

class PexipAPIController extends Controller {

	private PexipService $pexipService;
	private IInitialState $initialStateService;
	private ?string $userId;

	public function __construct(string           $appName,
								IRequest         $request,
								PexipService $pexipService,
								IInitialState    $initialStateService,
								?string          $userId) {
		parent::__construct($appName, $request);
		$this->pexipService = $pexipService;
		$this->initialStateService = $initialStateService;
		$this->userId = $userId;
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
		if (isset($response['error'])) {
			return new DataResponse($response, Http::STATUS_BAD_REQUEST);
		}
		return new DataResponse($response);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $name
	 * @param string $guestPin
	 * @param string $adminPin
	 * @return DataResponse
	 */
	public function createCall(string $name, string $guestPin = '', string $adminPin = ''): DataResponse {
		$response = $this->pexipService->createCall($this->userId, $name, $guestPin, $adminPin);
		if (isset($response['error'])) {
			return new DataResponse($response, Http::STATUS_BAD_REQUEST);
		}
		return new DataResponse($response);
	}
}
