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

namespace Recipes;

/**
 * Basic model class, provifing the default methods for getters and setters for
 * model classes.
 *
 * @package Core
 * @subpackage Model
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class Model extends Struct
{
    /**
     * Array containing all model properties, all with the value "null", as
     * they have to be unintialized at this stage. The array should look like:
     * <code>
     *  array(
     *      'property' => null
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected $properties = array();

    /**
     * Model id
     *
     * This may be any arbrtrary identifier generated by the backend or the
     * database itself. Store for later reference, as the ID does not need to
     * map directly to an of the properties.
     *
     * @var mixed
     */
    protected $id = null;

    /**
     * Set to true, to force that no new data should be
     * fetched. This may be required if the model actually
     * represents an older revision of the model.
     *
     * @var bool
     */
    protected $static = false;

    /**
     * Array with a list of modified properties, which means properties, where
     * the data has been changed from the application, which should be stored
     * in the backend.
     *
     * @var array
     */
    protected $modifiedProperty = array();

    /**
     * Method from the model class implementation used to fetch the requested
     * data value for the constructed model. The method is called lazy, when
     * the data is actually requested from the model.
     *
     * The here given method is used, when there is nor special callback
     * defined in the $specialFetchMethods array.
     *
     * @var string
     */
    protected $defaultFetchMethod = null;

    /**
     * Method from the model class implementation used to fetch the requested
     * data value for the constructed model. The method is called lazy, when
     * the data is actually requested from the model. The array should look
     * like:
     * <code>
     *  array(
     *      'property' => 'callbackMethodName',
     *      ...
     *  )
     * </code>
     *
     * @var array
     */
    protected $specialFetchMethods = array(
    );

    /**
     * Properties containing user IDs
     *
     * Properties containing user IDs, which should be transformed into user
     * models. If the revision property is set with older revisions of the
     * current model, all properties with this name are also transformed.
     *
     * @var array
     */
    protected $userModelProperties = array(
        'author',
    );

    /**
     * Create model from identifier
     *
     * If the identifier of the model is known from somewhere in the
     * application you may set it directly here instead of searching for some
     * value.
     *
     * @param mixed $id
     * @return void
     */
    public function __construct( $id = null )
    {
        $this->id = $id;
    }

    /**
     * Read property from struct
     *
     * Read property from struct
     *
     * @ignore
     * @param string $property
     * @return mixed
     */
    public function __get( $property )
    {
        // Special handling for special property '_id'
        if ( $property === '_id' )
        {
            return $this->id;
        }

        // Check if the property exists at all - use array_key_exists, to let
        // this check pass, even if the property is set to null.
        if ( !array_key_exists( $property, $this->properties ) )
        {
            throw new recipePropertyException( $property );
        }

        // We fetch all stuff at once lazy, when one of the properties is
        // requested and not yet set.
        if ( ( $this->static === false ) &&
             ( $this->properties[$property] === null ) )
        {
            if ( isset( $this->specialFetchMethods[$property] ) )
            {
                // Check for, and special fetch callback.
                $fetchMethod = $this->specialFetchMethods[$property];
                $this->$fetchMethod();
            }
            else
            {
                // Use the default callback to fetch the data.
                $fetchMethod = $this->defaultFetchMethod;
                $this->$fetchMethod();
            }
        }

        return $this->properties[$property];
    }

    /**
     * Return modified values
     *
     * Return an array with all values which has been modified on the current
     * model instance, with their new values.
     *
     * @return array
     */
    protected function getModifiedValues()
    {
        $modified = array_unique( $this->modifiedProperty );
        $modifiedValues = array();

        foreach ( $modified as $property )
        {
            $modifiedValues[$property] = $this->properties[$property];
        }

        return $modifiedValues;
    }

    /**
     * Transform value to user model(s)
     *
     * Transform a user ID or an array with user IDs in the a user mdoel, or an
     * array of user models.
     *
     * @param mixed $property
     * @return mixed
     */
    protected function toUserModel( $property )
    {
        if ( is_scalar( $property ) )
        {
            $property = new recipeModelUser( $property );
        }
        elseif ( is_array( $property ) )
        {
            foreach ( $property as $key => $value )
            {
                $property[$key] = new recipeModelUser( $value );
            }
        }

        return $property;
    }

    /**
     * Transform all user properties
     *
     * Transform all iser IDs in the declared user properties into user models.
     *
     * If the model has a revisions property, the transformation will also be
     * performed for all revisions of the model.
     *
     * @param array $data
     * @return array
     */
    protected function transformUserProperties( array $data )
    {
        foreach ( $data as $key => $value )
        {
            if ( in_array( $key, $this->userModelProperties, true ) )
            {
                $data[$key] = $this->toUserModel( $value );
            }
        }

        // Apply to all revision, if available
        if ( isset( $data['revisions'] ) )
        {
            foreach ( $data['revisions'] as $rev => $revision )
            {
                foreach ( $revision as $key => $value )
                {
                    if ( in_array( $key, $this->userModelProperties, true ) )
                    {
                        $data['revisions'][$rev][$key] = $this->toUserModel( $value );
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Set property value
     *
     * Set property value and set the property modified. Property value checks
     * should be done by inheriting methods, which call this parent method for
     * actually setting the value.
     *
     * @ignore
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set( $property, $value )
    {
        // Check if the property exists at all - use array_key_exists, to let
        // this check pass, even if the property is set to null.
        //
        // @TODO: Make it impossible to overwrite internal data. Requires
        // thorough testing.
        if ( !array_key_exists( $property, $this->properties ) )
        {
            throw new recipePropertyException( $property );
        }

        // Store property and set modified
        $this->properties[$property] = $value;
        $this->modifiedProperty[] = $property;
    }

    /**
     * Check if property exists in struct
     *
     * Check if property exists in struct
     *
     * @ignore
     * @param string $property
     * @return mixed
     */
    public function __isset( $property )
    {
        // Check if the property exists at all - use array_key_exists, to let
        // this check pass, even if the property is set to null.
        return array_key_exists( $property, $this->properties );
    }

    /**
     * Recreate struct exported by var_export()
     *
     * Recreate struct exported by var_export()
     *
     * @ignore
     * @param array $properties
     * @return recipeBaseStruct
     */
    public static function __set_state( array $properties )
    {
        $struct = new static();

        foreach ( $properties as $key => $value )
        {
            $struct->$key = $value;
        }

        return $struct;
    }

    /**
     * Method called to create a new instance in the backend.
     *
     * Method called when the model should be created in the backend the first
     * time. This will normally throw an error if a model with the same
     * identifier already exists in the backend.
     *
     * @return void
     */
    abstract public function create();

    /**
     * Method called to store changes to the model.
     *
     * Method called to store changes in the model to the backend. The method
     * should only modify the backend data, if something really has been
     * changed in the model. Use the __set() method, which should wrap all
     * write access to the model, to remember write access.
     *
     * @return void
     */
    abstract public function storeChanges();
}

