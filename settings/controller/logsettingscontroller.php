<?php
/**
 * @author Georg Ehrke
 * @copyright 2014 Georg Ehrke <georg@ownCloud.com>
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OC\Settings\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IConfig;

/**
 * Class LogSettingsController
 *
 * @package OC\Settings\Controller
 */
class LogSettingsController extends Controller {
	/** @var \OCP\IConfig */
	private $config;

	/**
	 * @param string $appName
	 * @param IRequest $request
	 * @param IConfig $config
	 */
	public function __construct($appName,
								IRequest $request,
								IConfig $config) {
		parent::__construct($appName, $request);
		$this->config = $config;
	}

	/**
	 * set log level for logger
	 *
	 * @param int $level
	 * @return JSONResponse
	 */
	public function setLogLevel($level) {
		if ($level < 0 && $level > 3) {
			return new JSONResponse([
				'message' => 'log-level out of allowed range',
			]);
		}

		$this->config->setSystemValue('loglevel', (int) $level);
		return new JSONResponse();
	}

	/**
	 * get log entries from logfile
	 *
	 * @param int $count
	 * @param int $offset
	 * @return JSONResponse
	 */
	public function getEntries($count=50, $offset=0) {
		return new JSONResponse([
			'data' => \OC_Log_Owncloud::getEntries($count, $offset),
			'remain' => count(\OC_Log_Owncloud::getEntries(1, $offset + $count)) !== 0,
			'status' => 'success',
		]);
	}

	/**
	 * download the logfile
	 *
	 * @NoCSRFRequired
	 */
	public function downloadLogFile() {
		$this->sendHeadersForLogFileDownload();

		//AppFramework Responses don't support handles,
		//so we have to use readfile instead of a proper response
		$logFilePath = \OC_Log_Owncloud::getLogFilePath();
		readfile($logFilePath);
	}

	/**
	 * get filename for the logfile that's being downloaded
	 *
	 * @param int $timestamp (defaults to time())
	 * @return string
	 */
	private function getFilenameForDownload($timestamp=null) {
		$instanceId = $this->config->getSystemValue('instanceid');

		$filename = implode([
			'ownCloud',
			$instanceId,
			(!is_null($timestamp)) ? $timestamp : time()
		], '-');
		$filename .= '.log';

		return $filename;
	}

	/**
	 * send headers when download the log file
	 */
	private function sendHeadersForLogFileDownload() {
		$filename = $this->getFilenameForDownload();

		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Content-Type: application/json');
	}
}