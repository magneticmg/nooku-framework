<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Template View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
abstract class KViewTemplate extends KViewAbstract
{
    /**
     * Template identifier (com://APP/COMPONENT.template.NAME)
     *
     * @var string|object
     */
    protected $_template;

    /**
     * Auto assign
     *
     * @var boolean
     */
    protected $_auto_assign;

    /**
     * The assigned data
     *
     * @var boolean
     */
    protected $_data;

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Set the auto assign state
        $this->_auto_assign = $config->auto_assign;

        //Set the data
        $this->_data = KObjectConfig::unbox($config->data);

        //Set the template object
        $this->_template = $config->template;

        //Add the template filters
        $filters = (array) KObjectConfig::unbox($config->template_filters);

        foreach ($filters as $key => $value)
        {
            if (is_numeric($key)) {
                $this->getTemplate()->addFilter($value);
            } else {
                $this->getTemplate()->addFilter($key, $value);
            }
        }
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'data'			   => array(),
            'template'         => $this->getName(),
            'template_filters' => array('shorttag', 'function', 'variable', 'script', 'style', 'link', 'template', 'url'),
            'auto_assign'      => true,
        ));

        parent::_initialize($config);
    }

    /**
     * Set a view properties
     *
     * @param   string  $property The property name.
     * @param   mixed   $value    The property value.
     */
    public function __set($property, $value)
    {
        $this->_data[$property] = $value;
    }

    /**
     * Get a view property
     *
     * @param   string  $property The property name.
     * @return  string  The property value.
     */
    public function __get($property)
    {
        $result = null;
        if(isset($this->_data[$property])) {
            $result = $this->_data[$property];
        }

        return $result;
    }

    /**
     * Return the views output
     *
     * @return string 	The output of the view
     */
    public function display()
    {
        $this->_content = $this->getTemplate()
            ->loadIdentifier($this->_layout, $this->_data)
            ->render();

        return parent::display();
    }

    /**
     * Sets the view data
     *
     * @param   array $data The view data
     * @return  KViewTemplate
     */
    public function setData(array $data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Get the view data
     *
     * @return  array   The view data
     */
    public function getData()
    {
        return $this->_data;
    }

	/**
     * Sets the layout name
     *
     * @param    string  $layout The template name.
     * @return   KViewAbstract
     */
    public function setLayout($layout)
    {
        if(is_string($layout) && strpos($layout, '.') === false )
		{
            $identifier = clone $this->getIdentifier();
            $identifier->name = $layout;
	    }
		else $identifier = $this->getIdentifier($layout);

        $this->_layout = $identifier;
        return $this;
    }

	/**
     * Get the layout.
     *
     * @return string The layout name
     */
    public function getLayout()
    {
        return $this->_layout->name;
    }

    /**
     * Get the identifier for the template with the same name
     *
     * @return  KTemplateInterface
     */
    public function getTemplate()
    {
        if(!$this->_template instanceof KTemplateInterface)
        {
            //Make sure we have a template identifier
            if(!($this->_template instanceof KObjectIdentifier)) {
                $this->setTemplate($this->_template);
            }

            $options = array(
            	'view' => $this,
                'translator' => $this->getTranslator()
            );

            $this->_template = $this->getObject($this->_template, $options);
        }

        return $this->_template;
    }

    /**
     * Method to set a template object attached to the view
     *
     * @param   mixed   $template An object that implements KObjectInterface, an object that
     *                  implements KObjectIdentifierInterface or valid identifier string
     * @throws  UnexpectedValueException    If the identifier is not a table identifier
     * @return  KViewAbstract
     */
    public function setTemplate($template)
    {
        if(!($template instanceof KTemplateInterface))
        {
            if(is_string($template) && strpos($template, '.') === false )
		    {
			    $identifier = clone $this->getIdentifier();
                $identifier->path = array('template');
                $identifier->name = $template;
			}
			else $identifier = $this->getIdentifier($template);

            if($identifier->path[0] != 'template') {
                throw new UnexpectedValueException('Identifier: '.$identifier.' is not a template identifier');
            }

            $template = $identifier;
        }

        $this->_template = $template;

        return $this;
    }

    /**
     * Execute and return the views output
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->display();
    }

    /**
     * Supports a simple form of Fluent Interfaces. Allows you to assign variables to the view by using the variable
     * name as the method name. If the method name is a setter method the setter will be called instead.
     *
     * For example : $view->layout('foo')->title('name')->display().
     *
     * @param   string  $method Method name
     * @param   array   $args   Array containing all the arguments for the original call
     * @return  KViewAbstract
     *
     * @see http://martinfowler.com/bliki/FluentInterface.html
     */
    public function __call($method, $args)
    {
        //If one argument is passed we assume a setter method is being called
        if(count($args) == 1)
        {
            if(method_exists($this, 'set'.ucfirst($method))) {
                return $this->{'set'.ucfirst($method)}($args[0]);
            } else {
                return $this->set($method, $args[0]);
            }
        }

        return parent::__call($method, $args);
    }
}