<?php
/**
 * arbit CouchDB backend
 *
 * This file is part of arbit.
 *
 * arbit is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * arbit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with arbit; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @subpackage CouchDbBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Document representing the groups
 *
 * @package Core
 * @subpackage CouchDbBackend
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class arbitBackendCouchDBGroupDocument extends phpillowDocument
{
    /**
     * List of required properties. For each required property, which is not
     * set, a validation exception will be thrown on save.
     *
     * @var array
     */
    protected $requiredProperties = array(
        'name',
    );

    /**
     * Indicates wheather to keep old revisions of this document or not.
     *
     * @var bool
     */
    protected $versioned = false;

    /**
     * Construct new book document
     *
     * Construct new book document and set its property validators.
     *
     * @return void
     */
    public function __construct()
    {
        $this->properties = array(
            'name'          => new phpillowRegexpValidator( '(^[\x21-\x7e]+$)i' ),
            'description'   => new phpillowTextValidator(),
            'users'         => new phpillowArrayValidator(),
            'permissions'   => new phpillowArrayValidator(),
        );

        parent::__construct();
    }

    /**
     * Return document type name
     *
     * This method is required to be implemented to return the document type
     * for PHP versions lower then 5.2. When only using PHP 5.3 and higher you
     * might just implement a method which does "return static:$type" in a base
     * class.
     *
     * @return string
     */
    protected function getType()
    {
        return 'group';
    }

    /**
     * Get ID from document
     *
     * The ID normally should be calculated on some meaningful / unique
     * property for the current ttype of documents. The returned string should
     * not be too long and should not contain multibyte characters.
     *
     * @return string
     */
    protected function generateId()
    {
        return $this->stringToId( $this->storage->name );
    }
}

