<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Filter Chain
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterChain extends KObjectQueue implements KFilterInterface
{
    /**
     * Validate a scalar or traversable value
     *
     * NOTE: This should always be a simple yes/no question (is $value valid?), so only true or false should be returned
     *
     * @param   mixed   $value Value to be validated
     * @return  bool    True when the value is valid. False otherwise.
     */
    public function validate($value)
    {
        $result = true;

        foreach($this as $filter)
        {
            if($filter->validate($value) === false) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Sanitize a scalar or traversable value
     *
     * @param   mixed   $value Value to be sanitized
     * @return  mixed   The sanitized value
     */
    public function sanitize($value)
    {
        foreach($this as $filter) {
            $value = $filter->sanitize($value);
        }

        return $value;
    }

    /**
     * Add a filter to the queue based on priority
     *
     * @param KFilterInterface 	$filter A Filter
     * @param integer	        $priority The command priority, usually between 1 (high priority) and 5 (lowest),
     *                                    default is 3. If no priority is set, the command priority will be used
     *                                    instead.
     *
     * @return KFilterChain
     */
    public function addFilter(KFilterInterface $filter, $priority = null)
    {
        $this->enqueue($filter, $priority);
        return $this;
    }

    /**
     * Get a list of error that occurred during sanitize or validate
     *
     * @return array
     */
    public function getErrors()
    {
        $errors = array();
        foreach($this as $filter) {
            $errors = array_merge($errors, $filter->getErrors());
        }

        return $errors;
    }

    /**
     * Attach a filter to the queue
     *
     * The priority parameter can be used to override the filter priority while enqueueing the filter.
     *
     * @param   KFilterInterface  $filter
     * @param   integer          $priority The filter priority, usually between 1 (high priority) and 5 (lowest),
     *                                     default is 3. If no priority is set, the filter priority will be used
     *                                     instead.
     * @return KFilterChain
     * @throws InvalidArgumentException if the object doesn't implement KFilterInterface
     */
    public function enqueue(KObjectHandlable $filter, $priority = null)
    {
        if (!$filter instanceof KFilterInterface) {
            throw new InvalidArgumentException('Filter needs to implement KFilterInterface');
        }

        $priority = is_int($priority) ? $priority : KFilter::PRIORITY_NORMAL;
        return parent::enqueue($filter, $priority);
    }

    /**
     * Removes a filter from the queue
     *
     * @param   KFilterInterface   $filter
     * @return  boolean    TRUE on success FALSE on failure
     * @throws \InvalidArgumentException if the object doesn't implement FilterInterface
     */
    public function dequeue(KObjectHandlable $filter)
    {
        if (!$filter instanceof KFilterInterface) {
            throw new InvalidArgumentException('Filter needs to implement KFilterInterface');
        }

        return parent::dequeue($filter);
    }

    /**
     * Check if the queue does contain a given filter
     *
     * @param  KFilterInterface   $filter
     * @return bool
     * @throws InvalidArgumentException if the object doesn't implement KFilterInterface
     */
    public function contains(KObjectHandlable $filter)
    {
        if (!$filter instanceof KFilterInterface) {
            throw new InvalidArgumentException('Filter needs to implement KFilterInterface');
        }

        return parent::contains($filter);
    }
}