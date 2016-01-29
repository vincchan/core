<?php
/**
 * @author Roeland Jago Douma <rullzer@owncloud.com>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace OC\Files\Checksum;

use OCP\Files\Checksum\IManager;
use OCP\Files\File;
use OCP\Files\Checksum\IProvider;
use OCP\Files\Checksum\UnsupportedChecksumTypeException;

class Manager implements IManager {

	/** @var \Closure */
	private $providerClosure;

	/** @var IProvider */
	private $provider;

	/**
	 * TODO throw exception. We only want to allow 1 provider
	 *
	 * @param \Closure $closure
	 */
	public function registerProvider(\Closure $closure) {
		$this->providerClosure = $closure;
	}

	/**
	 * @return IProvider
	 */
	private function getProvider() {
		if ($this->provider === null) {
			if ($this->providerClosure === null) {
				return null;
			}
			$closure = $this->providerClosure;
			$p = $closure();
			if (!($p instanceof IProvider)) {
				throw \InvalidArgumentException('The given provider does not implement the \OCP\Files\Checksum\IProvider interface');
			}
			$this->provider = $p;
		}

		return $this->provider;
	}

	/**
	 * @inheritdoc
	 */
	public function addChecksum(File $file, $type, $checksum) {
		$provider = $this->getProvider();
		if ($provider === null) {
			return true;
		}

		return $this->getProvider()->addChecksum($file, $type, $checksum);
	}

	/**
	 * @inheritdoc
	 */
	public function getChecksums(File $file) {
		$provider = $this->getProvider();
		if ($provider === null) {
			return [];
		}

		return $this->getProvider()->getChecksums($file);;
	}

	/**
	 * @inheritdoc
	 */
	public function clearChecksums(File $file) {
		$provider = $this->getProvider();
		if ($provider === null) {
			return;
		}

		$this->getProvider()->clearChecksums($file);
	}
}