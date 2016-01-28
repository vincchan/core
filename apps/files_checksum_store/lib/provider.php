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
namespace OCA\Files_Checksum_Store;

use OCP\Files\Checksum\IProvider;
use OCP\Files\Checksum\IManager;
use OCP\Files\Checksum\UnsupportedChecksumTypeException;
use OCP\AppFramework\IAppContainer;
use OCP\Files\File;
use OCP\IDBConnection;

class Provider implements IProvider {


	/** @var IDBConnection */
	private $connection;

	public function __construct(\OCP\IDBConnection $connection) {
		$this->connection = $connection;
	}

	public static function register(IManager $cm, IAppContainer $container) {
		$cm->registerProvider(function() use ($container) {
			return $container->query('Provider');
		});
	}

	public static function supportedChecksums() {
		return [
			'MD5'      => 1,
			'SHA-1'    => 2,
			'ADLER-32' => 3,
		];
	}

	public function addChecksum(File $file, $type, $checksum) {
		$type = strtoupper($type);

		if (!in_array($type, array_keys($this->supportedChecksums()))) {
			throw new UnsupportedChecksumTypeException();
		}

		$qb = $this->connection->getQueryBuilder();

		$qb->insert('checksum')
			->setValue('file_id', $qb->createNamedParameter($file->getId()))
			->setValue('checksum_id', $qb->createNamedParameter($this->supportedChecksums()[$type]))
			->setValue('checksum', $qb->createNamedParameter($checksum))
			->execute();

		return true;
	}

	public function getChecksums(File $file) {
		$supportedChecksums = array_flip($this->supportedChecksums());

		$qb = $this->connection->getQueryBuilder();
		$stmt = $qb->select('checksum_id', 'checksum')
			->from('checksum')
			->where($qb->expr()->eq('file_id', $qb->createNamedParameter($file->getId())))
			->execute();

		$checksums = [];
		while($data = $stmt->fetch()) {
			$type = (int)$data['checksum_id'];
			if (!in_array($type, array_keys($supportedChecksums))) {
				continue;
			}

			$checksums[$supportedChecksums[$type]] = $data['checksum'];
		}
		$stmt->closeCursor();

		return $checksums;
	}

	public function clearChecksums(File $file) {
		$qb = $this->connection->getQueryBuilder();

		$qb->delete('checksum')
			->where($qb->expr()->eq('file_id', $qb->createNamedParameter($file->getId())))
			->execute();
	}
}