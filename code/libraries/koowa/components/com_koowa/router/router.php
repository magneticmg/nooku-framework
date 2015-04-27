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

if (version_compare(JVERSION, '3.3', '<') && !interface_exists('JComponentRouterInterface')) {

    interface JComponentRouterInterface
    {
    }

}

class ComKoowaRouterRouter extends KObject implements JComponentRouterInterface, KObjectSingleton
{
    /**
     * Special instance of the constructor to take both types of inputs.
     */
    function __construct()
    {
        $args = func_get_args();

        $config = new KObjectConfig;

        if ($args[0] instanceof JApplicationWeb) {
            $config->append(array(
                'app' => $args[0],
                'menu' => $args[1]
            ));
        }

        parent::__construct($config);
    }

    public function parse(& $segments)
    {
        $vars = array();

        $vars['view'] = $segments[0];

        if (isset($segments[1])) {
            $vars['id'] = $segments[1];
            $vars['view'] = KStringInflector::singularize($segments[0]);
        }

        $menu = JFactory::getApplication()->getMenu()->getActive();
        if (isset($menu->query['layout'])) {
            $vars['layout'] = $menu->query['layout'];
        }

        return $vars;
    }

    public function build(& $query)
    {
        $segments = array();
        // try to load an Itemid
        if (!isset($query['Itemid'])) {

            var_dump(get_class($this));die();
            $component = 'com_' . $this->getIdentifier()->getPackage();
            $component = JComponentHelper::getComponent($component);

            $attributes = array('component_id');
            $values = array($component->id);

            $items = JApplication::getInstance($this->getIdentifier()->getDomain())
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

    public function preprocess($query)
    { /*do nothing */
    }

}
