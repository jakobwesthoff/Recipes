<?php
/**
 * This file is part of recipe.
 *
 * recipe is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * recipe is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with recipe; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

namespace Recipes\Model\Validator;
use Recipes\Model\Validator;

/**
 * Array validator class
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class ArrayValidator extends Validator
{
    /**
     * Validator for the array values.
     *
     * @var recipeModelValidatorBase
     */
    protected $valueValidator = null;

    /**
     * Validator for the array keys.
     *
     * @var recipeModelValidatorBase
     */
    protected $keyValidator = null;

    /**
     * Configure validator
     *
     * The array validator may optionally be configured with an additional
     * validator, which is applied to all array values. If you do not pass a
     * validator class no assumptions will be made on the array values.
     *
     * @param recipeModelValidatorBase $keyValidator
     * @param recipeModelValidatorBase $valueValidator
     * @return void
     */
    public function configure( Validator $keyValidator = null, Validator $valueValidator = null )
    {
        $this->keyValidator   = $keyValidator;
        $this->valueValidator = $valueValidator;
    }

    /**
     * Validate value
     *
     * Validates the given input. Returns the input, when it matches the
     * validation constraints and throws a Exception
     * exception otherwise.
     *
     * The name and expectation paramters are used to generate a better user
     * error message. The name should be the name of the property, and the
     * expectation should be a string somehow describing what kind of content
     * was expected from validation.
     *
     * @throws recipePropertyValue If validation constraints are not met.
     * @param string $name
     * @param mixed $value
     * @param string $expectation
     * @return mixed
     */
    public function validate( $name, $value, $expectation )
    {
        if ( !is_array( $value ) )
        {
            throw new Exception( $name, $expectation );
        }

        // If given, apply value validation to all array keys and contents
        foreach ( $value as $k => $v )
        {
            if ( $this->keyValidator !== null )
            {
                $this->keyValidator->validate( $name, $k, $expectation );
            }

            if ( $this->valueValidator !== null )
            {
                $this->valueValidator->validate( $name, $v, $expectation );
            }
        }

        return $value;
    }
}

