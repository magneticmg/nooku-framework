<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Date
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Date
 */
class KDate extends DateTime implements KDateInterface
{
    /**
     * Constructor.
     *
     * @param   array|KObjectConfig An associative array of configuration settings or a ObjectConfig instance.
     */
    public function __construct($config = array())
    {
        if (!$config instanceof KObjectConfig) {
            $config = new KObjectConfig($config);
        }

        $this->_initialize($config);

        if (!($config->timezone instanceof DateTimeZone)) {
            $config->timezone = new DateTimeZone($config->timezone);
        }

        parent::__construct($config->date, $config->timezone);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'date'       => 'now',
            'timezone'   => 'UTC'
        ));
    }

    /**
     * Get a handle for this object
     *
     * This function returns an unique identifier for the object. This id can be used as a hash key for storing objects
     * or for identifying an object
     *
     * @return string A string that is unique
     */
    public function getHandle()
    {
        return spl_object_hash($this);
    }
}