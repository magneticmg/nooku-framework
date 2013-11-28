<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Filter Factory
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterFactory extends KObject implements KObjectMultiton
{
    /**
     * Factory method for KFilterInterface classes.
     *
     * Method accepts an array of filter names, or filter object identifiers and will create a chained filter
     * using a FIFO approach.
     *
     * @param	string|array $identifier Filter identifier(s)
     * @param 	object|array $config     An optional KObjectConfig object with configuration options
     * @return  KFilterInterface
     */
    public function getFilter($identifier, $config = array())
    {
        //Get the filter(s) we need to create
        $filters = (array) $identifier;

        //Create a filter chain
        if(count($filters) > 1)
        {
            $filter = $this->getObject('koowa:filter.chain');

            foreach($filters as $name)
            {
                $instance = $this->_createFilter($name, $config);
                $filter->addFilter($instance);
            }
        }
        else $filter = $this->_createFilter($filters[0], $config);

        return $filter;
    }

    /**
     * Create a filter based on it's name
     *
     * If the filter is not an identifier this function will create it directly instead of going through the KObject
     * identification process.
     *
     * @param 	string	$filter Filter identifier
     * @param   array   $config An array of configuration options.
     * @throws	UnexpectedValueException	When the filter does not implement FilterInterface
     * @return  KFilterInterface
     */
    protected function _createFilter($filter, $config)
    {
        if(is_string($filter) && strpos($filter, '.') === false ) {
            $filter = 'koowa:filter.'.trim($filter);
        }

        $filter = $this->getObject($filter, $config);

        //Check the filter interface
        if(!($filter instanceof KFilterInterface)) {
            throw new UnexpectedValueException('Filter:'.get_class($filter).' does not implement FilterInterface');
        }

        return $filter;
    }
}