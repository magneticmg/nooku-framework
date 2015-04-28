<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

class ComKoowaDispatcherRouterRuleBuild extends KObject
{

    function execute(& $query)
    {
        $segments = array();
        // try to load an Itemid
        $package = $this->getIdentifier()->getPackage();


        if (!isset($query['Itemid'])) {

            $component = JComponentHelper::getComponent('com_' . $package);

            $attributes = array('component_id');
            $values = array($component->id);

            $items = JFactory::getApplication()
                ->getMenu()
                ->getItems($attributes, $values);

            if (count($items)) {
                $query['Itemid'] = $items[0]->id;
            }

        }


        // pluralizing the view
        if (isset($query['view'])) {
            $segments[] = KStringInflector::pluralize($query['view']);
            unset($query['view']);
        }

        if (isset($query['id'])) {
            $segments[] = $query['id'];
            unset($query['id']);
        }

        return $segments;

    }
}