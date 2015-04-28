<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Class ComKoowaRouter
 */
class ComKoowaDispatcherRouter extends KDispatcherRouter
{

    /**
     * @param KHttpUrlInterface $url
     * @return array
     */
    public function parse(KHttpUrlInterface $url)
    {

        $identifier = $this->getIdentifier()->toArray();
        $identifier['path'] = array('dispatcher', 'router', 'rule');
        $identifier['name'] = 'parse';
        $segments = explode('/', ltrim($url->getPath(), '/'));

        if($segments[0] == 'component')
        {
            $segments = array_slice($segments, 2);
        }

        $vars = $this->getObject($identifier)->execute($segments);

        return $vars;
    }
//
//    public function parseRoute(JUri $url)
//    {
//
//        $url = $this->getObject('com:koowa.dispatcher.router.route', array('url' => (string)$url));//new KHttpUrl();
//        $vars = array();
//
//    }
//
//    public function build(KHttpUrlInterface $url)
//    {
//        $query = $url->getQuery(true);
//        $vars = $this->buildRoute($query);
//
//        return $vars;
//    }
//
//    public function buildRoute(& $query)
//    {
//        $segments = array();
//        // try to load an Itemid
//        if (!isset($query['Itemid'])) {
//
//            $component = 'com_' . $this->getIdentifier()->getPackage();
//            $component = JComponentHelper::getComponent($component);
//
//            $attributes = array('component_id');
//            $values = array($component->id);
//
//            $items = JApplication::getInstance($this->getIdentifier()->getDomain())
//                ->getMenu()
//                ->getItems($attributes, $values);
//
//            if (count($items)) {
//                $query['Itemid'] = $items[0]->id;
//            }
//
//        }
//
//        // pluralizing the view
//        if (isset($query['view'])) {
//            $segments[] = KStringInflector::pluralize($query['view']);
//            unset($query['view']);
//        }
//
//        if (isset($query['id'])) {
//            $segments[] = $query['id'];
//            unset($query['id']);
//        }
//
//        return $segments;
//    }
//
//    public function preprocess($query)
//    { /*do nothing */
//    }

}
