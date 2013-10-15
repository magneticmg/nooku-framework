<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Database Row
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
abstract class KDatabaseRowAbstract extends KObjectArray implements KDatabaseRowInterface
{
    /**
     * Tracks columns where data has been updated. Allows more specific save operations.
     *
     * @var array
     */
    protected $_modified = array();

    /**
     * Tracks the status the row
     *
     * Available row status values are defined as STATUS_ constants in KDatabase
     *
     * @var string
     * @see KDatabase
     */
    protected $_status = null;

    /**
     * The status message
     *
     * @var string
     */
    protected $_status_message = '';

    /**
     * Tracks if row data is new
     *
     * @var bool
     */
    protected $_new = true;

    /**
	 * Name of the identity column in the rowset
	 *
	 * @var	string
	 */
	protected $_identity_column;

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KObjectConfig();

        parent::__construct($config);

        // Set the table identifier
    	if(isset($config->identity_column)) {
			$this->_identity_column = $config->identity_column;
		}

        // Reset the row
        $this->reset();

        // Set the new state of the row
        $this->_new = $config->new;

        // Set the row data
        if(isset($config->data))  {
            $this->setData((array) KObjectConfig::unbox($config->data), $this->_new);
        }

        //Set the status
        if(isset($config->status)) {
            $this->setStatus($config->status);
        }

        //Set the status message
        if(!empty($config->status_message)) {
            $this->setStatusMessage($config->status_message);
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
             'data'             => null,
             'new'              => true,
             'status'           => null,
             'status_message'   => '',
             'identity_column'  => null
        ));

        parent::_initialize($config);
    }

	/**
	 * Test the connected status of the row.
	 *
	 * @return	boolean	Returns TRUE by default.
	 */
    public function isConnected()
	{
	    return true;
	}

 	/**
    * Returns an associative array of the raw data
    *
    * @param   boolean $modified If TRUE, only return the modified data. Default FALSE
    * @return  array
    */
    public function getData($modified = false)
    {
        if($modified) {
            $result = array_intersect_key($this->_data, $this->_modified);
        } else {
            $result = $this->_data;
        }

        return $result;
    }

    /**
     * Set the row data
     *
     * @param   mixed   $data       Either and associative array, an object or a KDatabaseRow
     * @param   boolean $modified   If TRUE, update the modified information for each column being set. Default TRUE
     * @return  KDatabaseRowAbstract
     */
     public function setData( $data, $modified = true )
     {
        if($data instanceof KDatabaseRowInterface) {
            $data = $data->toArray();
        } else {
            $data = (array) $data;
        }

        if($modified)
        {
            foreach($data as $column => $value) {
                $this->$column = $value;
            }
        }
        else
        {
            $this->_data = array_merge($this->_data, $data);
        }

        return $this;
    }

    /**
     * Returns the status
     *
     * @return string The status
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Set the status
     *
     * @param   string|null $status The status value or NULL to reset the status
     * @return  KDatabaseRowAbstract
     */
    public function setStatus($status)
    {
        $this->_status   = $status;
        $this->_new      = ($status === NULL) ? true : false;

        return $this;
    }

    /**
     * Returns the status message
     *
     * @return string The status message
     */
    public function getStatusMessage()
    {
        return $this->_status_message;
    }


    /**
     * Set the status message
     *
     * @param   string $message The status message
     * @return  KDatabaseRowAbstract
     */
    public function setStatusMessage($message)
    {
        $this->_status_message = $message;
        return $this;
    }

    /**
     * Load the row from the database.
     *
     * @return object	If successful returns the row object, otherwise NULL
     */
    public function load()
    {
        $this->_modified = array();

        return $this;
    }

    /**
     * Saves the row to the database.
     *
     * This performs an intelligent insert/update and reloads the properties with fresh data from the table on success.
     *
     * @return boolean  If successful return TRUE, otherwise FALSE
     */
    public function save()
    {
        $this->_modified = array();

        return false;
    }

    /**
     * Deletes the row form the database.
     *
     * @return boolean  If successful return TRUE, otherwise FALSE
     */
    public function delete()
    {
        return false;
    }

    /**
     * Resets to the default properties
     *
     * @return boolean  If successful return TRUE, otherwise FALSE
     */
    public function reset()
    {
        $this->_data     = array();
        $this->_modified = array();

        return true;
    }

    /**
     * Count the rows in the database based on the data in the row
     *
     * @return integer
     */
    public function count()
    {
        return false;
    }

    /**
     * Set row field value
     *
     * If the value is the same as the current value and the row is loaded from the database the value will not be
     * reset. If the row is new the value will be (re)set and marked as modified
     *
     * @param   string  $column The column name.
     * @param   mixed   $value  The value for the property.
     * @return  void
     */
    public function __set($column, $value)
    {
        if(!isset($this->_data[$column]) || ($this->_data[$column] != $value) || $this->isNew())
        {
            parent::__set($column, $value);

            $this->_modified[$column] = true;
            $this->_status            = null;
        }
    }

    /**
     * Unset a row field
     *
     * @param   string  $column The column name.
     * @return  void
     */
    public function __unset($column)
    {
         parent::__unset($column);

         unset($this->_modified[$column]);
    }

 	/**
     * Gets the identity column of the rowset
     *
     * @return string
     */
    public function getIdentityColumn()
    {
        return $this->_identity_column;
    }

    /**
     * Get a list of columns that have been modified
     *
     * @return array    An array of column names that have been modified
     */
    public function getModified()
    {
        return array_keys($this->_modified);
    }

    /**
     * Check if a column has been modified
     *
     * @param   string  $column The column name.
     * @return  boolean
     */
    public function isModified($column)
    {
        $result = false;
        if(isset($this->_modified[$column]) && $this->_modified[$column]) {
            $result = true;
        }

        return $result;
    }

    /**
     * Checks if the row is new or not
     *
     * @return bool
     */
    public function isNew()
    {
        return (bool) $this->_new;
    }

	/**
	 * Search the mixin method map and call the method or trigger an error
	 *
	 * Function is also capable of checking is a behavior has been mixed successfully using is[Behavior] function. If
     * the behavior exists the function will return TRUE, otherwise FALSE.
	 *
	 * @param  string 	$method     The function name
	 * @param  array  	$arguments  The function arguments
	 * @throws BadMethodCallException 	If method could not be found
	 * @return mixed The result of the function
	 */
	public function __call($method, $arguments)
	{
		// If the method is of the form is[Behavior] handle it.
		$parts = KStringInflector::explode($method);

		if($parts[0] == 'is' && isset($parts[1]))
		{
			if(isset($this->_mixed_methods[$method])) {
				return true;
			}

			return false;
		}

		return parent::__call($method, $arguments);
	}
}