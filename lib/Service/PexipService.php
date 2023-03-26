<?php
/**
 * Nextcloud - Pexip
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier
 * @copyright Julien Veyssier 2023
 */

namespace OCA\Pexip\Service;

use DateTime;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use OCA\Pexip\AppInfo\Application;
use OCA\Pexip\Db\CallMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\Http\Client\IClient;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;
use OCP\Http\Client\IClientService;
use Throwable;

class PexipService {
	private LoggerInterface $logger;
	private IL10N $l10n;
	private IConfig $config;
	private IClient $client;
	private CallMapper $callMapper;

	public function __construct (string $appName,
								LoggerInterface $logger,
								IL10N $l10n,
								IConfig $config,
								CallMapper $callMapper,
								IClientService $clientService) {
		$this->client = $clientService->newClient();
		$this->logger = $logger;
		$this->l10n = $l10n;
		$this->config = $config;
		$this->callMapper = $callMapper;
	}

	/**
	 * @param string $pexipId
	 * @return array|string[]
	 * @throws MultipleObjectsReturnedException
	 * @throws \OCP\DB\Exception
	 */
	public function checkCall(string $pexipId): array {
		try {
			$call = $this->callMapper->getCallFromPexipId($pexipId);
			$this->callMapper->touchCall($call->getId());
			// TODO return proper info needed by Pexip
			return [
				'pexip_id' => $call->getPexipId(),
			];
		} catch (DoesNotExistException $e) {
			return [
				'error' => 'notfound',
			];
		}
	}
}
