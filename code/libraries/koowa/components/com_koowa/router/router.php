<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */


/**
 * Class ComKoowaRouterRouter
 *
 * This class will intercept the parse
 */
class ComKoowaRouterRouter extends KObject
{

    /**
     * @param $router
     * @param JUri $uri
     */
    public function build($router, JUri $uri)
    {

        $query = $uri->getQuery(true);

        if(isset($query['option']))
        {
            $component = str_replace('com_', '', $query['option']);
            // this should be more agnostic
            $domain = JFactory::getApplication()->isSite() ? 'site' : 'admin';

            $identifier = $this->getIdentifier()->toArray();

            $identifier['domain'] = $domain;
            $identifier['package'] = $component;
            $identifier['path'] = array('dispatcher');

            $str_identifier = 'com://' . $domain .'/'. $component .'.dispatcher.router';
            //$identifier = $this->getIdentifier($identifier);
            $manager = KObjectManager::getInstance();

            $class = $manager->getClass($str_identifier, false);

            // has been bootstrapped OR class is defined exists
            $enabled = $manager->isRegistered($str_identifier) OR ($class &&  $class != 'ComKoowaDispatcherRouter');

            if($enabled)
            {
                if($this->getConfig($identifier)->get('prefix_component', true))
                {
                    $segments[] = 'component';
                }

                $segments[] = $component;

                $identifier['path'] = array('dispatcher', 'router', 'rule');
                $identifier['name'] = 'build';
                // do the parsing

                $segments = array_merge($segments, $this->getObject($identifier)->execute($query));

                unset($query['option']);

                $uri->setPath(implode('/', $segments));

                $uri->setQuery($query);
            }

        }

    }

    /**
     * @param $router
     * @param JUri $uri
     * @return array
     */
//    function parse($router, JUri $uri)
//    {
//        $vars = array();
//        $route = $uri->getPath();
//
//        $parts = explode('/',$route);
//
//        if($parts[0] == 'component')
//        {
//            $component = $parts[1];
//            array_shift($parts);
//            array_shift($parts);
//        }
//
//        // this should be more agnostic
//        $domain = JFactory::getApplication()->isSite() ? 'site' : 'admin';
//
//        $identifier = $this->getIdentifier()->toArray();
//
//        $identifier['domain'] = $domain;
//        $identifier['package'] = $component;
//        $identifier['path'] = array('dispatcher');
//
//        $identifier = $this->getIdentifier($identifier);
//
//        $manager = $this->getObject('manager');
//        // has been bootstrapped OR class exists
//        $enabled = $manager->hasIdentifier($identifier) OR $manager->getClass($identifier, false);
//
//        if ($enabled) {
//
//            $identifier = $this->getIdentifier($identifier)->toArray();
//
//            $identifier['path'] = array('dispatcher', 'router', 'rule');
//            $identifier['name'] = 'parse';
//
//            $vars = $this->getObject($identifier)->execute($parts);
//
//        }
//        return $vars;
//    }

//    function _buildIdentifier($component)
//    {
//
//        if(strpos($component, 'com_') == 0)
//        {
//            $component = str_replace('com_', '', $component);
//        }
//
//        // this should be more agnostic
//        $domain = JFactory::getApplication()->isSite() ? 'site' : 'admin';
//
//        $identifier = $this->getIdentifier()->toArray();
//
//        $identifier['domain'] = $domain;
//        $identifier['package'] = $component;
//        $identifier['path'] = array('dispatcher');
//
//        $identifier = $this->getIdentifier($identifier);
//
//        return $identifier;
//
//    }
//    function _findMenuItem($route)
//    {
//
//    }
}