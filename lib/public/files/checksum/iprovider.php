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
namespace OCP\Files\Checksum;

use \OCP\Files\File;

/**
 * Interface IProvider
 *
 * @package OCP\Files\Checksum
 * @since 9.0.0
 */
interface IProvider {

	/**
	 * Add the checksum for $file
	 *
	 * @param File $file
	 * @param string $type
	 * @param string $checksum
	 * @return bool
	 * @since 9.0.0
	 * @throws UnsupportedChecksumTypeException
	 */
	public function addChecksum(File $file, $type, $checksum);

	/**
	 * Get all the checksums for $file
	 *
	 * @param File $file
	 * @return string[] Mapping of type => checksum
	 * @since 9.0.0
	 */
	public function getChecksums(File $file);

	/**
	 * Remove all checksums for $file
	 *
	 * @param File $file
	 * @since 9.0.0
	 */
	public function clearChecksums(File $file);
}