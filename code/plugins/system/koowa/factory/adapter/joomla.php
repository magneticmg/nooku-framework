<?php
/**
 * @version 	$Id:factory.php 46 2008-03-01 18:39:32Z mjaz $
 * @category	Koowa
 * @package		Koowa_Factory
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 */

/**
 * KFactoryAdpater for the Joomla! framework
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @category	Koowa
 * @package     Koowa_Factory
 */
class KFactoryAdapterJoomla extends KFactoryAdapterAbstract
{
	/**
	 * Create an instance of a class based on a class identifier
	 *
	 * @param mixed  $string 	The class identifier
	 * @param array  $options 	An optional associative array of configuration settings.
	 * @return object
	 */
	public function createInstance($identifier, array $options)
	{
		$parts = explode('.', $identifier);
		if($parts[0] != 'lib' || $parts[1] != 'joomla') {
			return false;
		}
		
		$name = ucfirst($parts[2]);
	
		//Handle exceptions
		if($name == 'Database') {
			$name = 'DBO';
		}
		
		if($name == 'Authorization') {
			$name = 'ACL';
		}
	
		return call_user_func_array(array('JFactory', 'get'.$name), $options);
	}
}