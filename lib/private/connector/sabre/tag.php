<?php
/**
 * ownCloud
 *
 * @author Vincent Petry
 * @copyright 2014 Vincent Petry <pvince81@owncloud.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OC\Connector\Sabre;

use Sabre\DAV;

/**
 * Tag property
 *
 * The "tag" property represents a tag
 */
class Tag extends DAV\Property {
	const NS_OWNCLOUD = 'http://owncloud.org/ns';

    /**
     * tag
     *
     * @var string
     */
    private $tag;

    /**
     * @param string $tag
     */
    public function __construct($tag) {
        $this->tag = $tag;
    }

    /**
     * Returns the tag name
     *
     * @return string
     */
    public function getTag() {

        return $this->tag;

    }

    /**
     * Serializes this property.
     *
     * @param DAV\Server $server
     * @param \DOMElement $dom
     * @return void
     */
    public function serialize(DAV\Server $server, \DOMElement $dom) {

        $prefix = $server->xmlNamespaces[self::NS_OWNCLOUD];
        $elem = $dom->ownerDocument->createElement($prefix . ':tag');

        $elem->appendChild($dom->ownerDocument->createTextNode($tag));

        $dom->appendChild($elem);

    }

    /**
     * Unserializes this property from a DOM Element
     *
     * This method returns an instance of this class.
     * It will only decode tag values. For non-compatible elements null will be returned.
     *
     * @param \DOMElement $dom
     * @return DAV\Property\Tag
     */
    static function unserialize(\DOMElement $dom) {

        if ($dom->firstChild && DAV\XMLUtil::toClarkNotation($dom->firstChild)==='{' . self::NS_OWNCLOUD . '}tag') {
            return new self($dom->firstChild->textContent,false);
        }
		return null;

    }

}
