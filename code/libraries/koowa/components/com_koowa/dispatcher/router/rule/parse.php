<?php

/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */
class ComKoowaDispatcherRouterRuleParse extends KObject
{
    function execute(& $segments)
    {
        $vars = array();
        $vars['view'] = $segments[0];

        if (isset($segments[1])) {
            $vars['id'] = $segments[1];
        }

        $menu = JFactory::getApplication()->getMenu()->getActive();

        if (isset($menu->query['layout'])) {
            $vars['layout'] = $menu->query['layout'];
        }
        return $vars;

    }

}