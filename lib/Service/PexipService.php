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

use Exception;
use OCA\Pexip\Db\CallMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\IConfig;
use OCP\IL10N;
use Psr\Log\LoggerInterface;
use Throwable;

class PexipService {
	private LoggerInterface $logger;
	private IL10N $l10n;
	private IConfig $config;
	private CallMapper $callMapper;

	public function __construct (string $appName,
								LoggerInterface $logger,
								IL10N $l10n,
								IConfig $config,
								CallMapper $callMapper) {
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
			return [
				'status' => 'success',
				'action' => 'continue',
				'result' => [
					'service_type' => 'conference',
					'name' => $call->getPexipId(),
					'service_tag' => 'Nextcloud',
					'description' => $call->getDescription(),
					'call_tag' => '',
					'pin' => $call->getPin(),
					'guest_pin' => $call->getGuestPin(),
					'guests_can_present' => $call->getGuestsCanPresent(),
					'allow_guests' => $call->getAllowGuests(),
					'view' => 'five_mains_seven_pips', // We choose the layout
					'locked' => false
				],
			];
		} catch (DoesNotExistException $e) {
			return [
				'status' => 'fail',
				'action' => 'continue',
				'result' => [],
			];
		}
	}

	/**
	 * @param string $userId
	 * @return array
	 * @throws \OCP\DB\Exception
	 */
	public function getUserCalls(string $userId): array {
		return $this->callMapper->getUserCalls($userId);
	}

	/**
	 * @param string $userId
	 * @param string $pexipId
	 * @param string $description
	 * @param string $pin
	 * @param string $guestPin
	 * @param bool $guestsCanPresent
	 * @param bool $allowGuests
	 * @return array
	 */
	public function createCall(string $userId, string $pexipId, string $description, string $pin, string $guestPin = '',
							   bool   $guestsCanPresent = true, bool $allowGuests = true): array {
		try {
			$call = $this->callMapper->createCall(
				$userId, $pexipId, $description, $pin, $guestPin,
				$guestsCanPresent, $allowGuests
			);
			return $call->jsonSerialize();
		} catch (Exception | Throwable $e) {
			return [
				'error' => $e->getMessage(),
			];
		}
	}
}
