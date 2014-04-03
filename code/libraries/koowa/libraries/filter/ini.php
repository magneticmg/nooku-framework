<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Ini Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterIni extends KFilterAbstract
{
    /**
     * Validate a value
     *
     * @param    mixed  $value Value to be validated
     * @return   bool   True when the variable is valid
     */
    public function validate($value)
    {
        try {
            $config = $this->getObject('object.config.factory')->fromString('ini', $value);
        } catch(RuntimeException $e) {
            $config = null;
        }

        return is_string($value) && !is_null($config);
    }

    /**
     * Sanitize a value
     *
     * @param   mixed  $value Value to be sanitized
     * @return  KObjectConfig
     */
    public function sanitize($value)
    {
        if(!$value instanceof KObjectConfig)
        {
            if(is_string($value)) {
                $value = $this->getObject('object.config.factory')->fromString('ini', $value);
            } else {
                $value = $this->getObject('object.config.factory')->createFormat('ini', $value);
            }
        }

        return $value;
    }
}