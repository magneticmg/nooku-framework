<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Abstract Controller
 *
 * Note: Concrete controllers must have a singular name
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
abstract class KControllerAbstract extends KObject implements KControllerInterface
{
    /**
     * The class actions
     *
     * @var array
     */
    protected $_actions = array();

    /**
     * Array of class methods to call for a given action.
     *
     * @var array
     */
    protected $_action_map = array();

    /**
     * Has the controller been dispatched
     *
     * @var boolean
     */
    protected $_dispatched;

    /**
	 * The request information
	 *
	 * @var array
	 */
	protected $_request = null;

    /**
     * Chain of command object
     *
     * @var KCommandChain
     */
    protected $_command_chain;

    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options.
     */
    public function __construct( KObjectConfig $config = null)
    {
        //If no config is passed create it
        if(!isset($config)) $config = new KObjectConfig();

        parent::__construct($config);

         //Set the dispatched state
        $this->_dispatched = $config->dispatched;

        //Set the request
        $this->setRequest((array) KObjectConfig::unbox($config->request));

        // Mixin the behavior interface
        $this->mixin('koowa:behavior.mixin', $config);
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'command_chain'     => 'koowa:command.chain',
            'dispatch_events'   => true,
            'event_dispatcher'  => 'koowa:event.dispatcher',
            'enable_callbacks'  => true,
            'dispatched'		=> false,
            'request'		    => null,
            'behaviors'         => array(),
        ));

        parent::_initialize($config);
    }

	/**
     * Has the controller been dispatched
     *
     * @return  boolean	Returns true if the controller has been dispatched
     */
    public function isDispatched()
    {
        return $this->_dispatched;
    }

    /**
     * Execute an action by triggering a method in the derived class.
     *
     * @param   string          $action  The action to execute
     * @param   KCommandContext $context A command context object
     * @throws Exception
     * @throws BadMethodCallException
     * @return  mixed|bool      The value returned by the called method, false in error case.
     */
    public function execute($action, KCommandContext $context)
    {
        $action = strtolower($action);

        //Update the context
        $context->action = $action;
        $context->caller = $this;

        //Find the mapped action
        if (isset( $this->_action_map[$action] )) {
           $command = $this->_action_map[$action];
        } else {
           $command = $action;
        }

        //Execute the action
        if($this->getCommandChain()->run('before.'.$command, $context) !== false)
        {
            $method = '_action' . ucfirst($command);

            if (!method_exists($this, $method))
            {
                if (isset($this->_mixed_methods[$command])) {
                    $context->result = $this->_mixed_methods[$command]->execute('action.' . $command, $context);
                } else {
                    throw new KControllerExceptionNotImplemented("Can't execute '$command', method: '$method' does not exist");
                }
            }
            else  $context->result = $this->$method($context);

            $this->getCommandChain()->run('after.'.$command, $context);
        }

        //Handle exceptions
        if($context->getError() instanceof Exception)
        {
            if($context->headers)
	        {
	            foreach($context->headers as $name => $value) {
	                header($name.' : '.$value);
	            }
	        }

            throw $context->getError();
        }

        return $context->result;
    }

    /**
     * Mixin an object
     *
     * When using mixin(), the calling object inherits the methods of the mixed in objects, in a LIFO order.
     *
     * @@param   mixed  $mixin  An object that implements KObjectMixinInterface, KObjectIdentifier object
     *                          or valid identifier string
     * @param    array $config  An optional associative array of configuration options
     * @return  KObject
     */
    public function mixin($mixin, $config = array())
    {
        if ($mixin instanceof KControllerBehaviorAbstract)
        {
            foreach ($mixin->getMethods() as $method)
            {
                if (substr($method, 0, 7) == '_action') {
                    $this->_actions[] = strtolower(substr($method, 7));
                }
            }

            $this->_actions = array_unique(array_merge($this->_actions, array_keys($this->_action_map)));
        }

        return parent::mixin($mixin, $config);
    }

    /**
     * Gets the available actions in the controller.
     *
     * @return  array Array[i] of action names.
     */
    public function getActions()
    {
        if (!$this->_actions)
        {
            $this->_actions = array();

            foreach ($this->getMethods() as $method)
            {
                if (substr($method, 0, 7) == '_action') {
                    $this->_actions[] = strtolower(substr($method, 7));
                }
            }

            $this->_actions = array_unique(array_merge($this->_actions, array_keys($this->_action_map)));
        }

        return $this->_actions;
    }

	/**
	 * Get the request information
	 *
	 * @return KObjectConfig	A KObjectConfig object with request information
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Set the request information
	 *
	 * @param array	$request An associative array of request information
	 * @return KControllerAbstract
	 */
	public function setRequest(array $request)
	{
		$this->_request = new KObjectConfig();
		foreach($request as $key => $value) {
		    $this->$key = $value;
		}

		return $this;
	}

    /**
     * Get the chain of command object
     *
     * To increase performance the a reference to the command chain is stored in object scope to prevent slower calls
     * to the KCommandChain mixin.
     *
     * @return  KCommandChainInterface
     */
    public function getCommandChain()
    {
        if(!$this->_command_chain instanceof KCommandChainInterface)
        {
            //Ask the parent the relay the call to the mixin
            $this->_command_chain = parent::getCommandChain();

            if(!$this->_command_chain instanceof KCommandChainInterface)
            {
                throw new UnexpectedValueException(
                    'CommandChain: '.get_class($this->_command_chain).' does not implement KCommandChainInterface'
                );
            }
        }

        return $this->_command_chain;
    }

    /**
     * Get the command chain context
     *
     * Overrides CommandMixin::getCommandContext() to insert the request and response objects into the controller
     * command context.
     *
     * @return  KCommandContext
     * @see KCommandMixin::getCommandContext
     */
    public function getCommandContext()
    {
        $context = parent::getCommandContext();

        //$context->request = $this->getRequest();

        return $context;
    }

    /**
     * Register (map) an action to a method in the class.
     *
     * @param   string  $alias  The action.
     * @param   string  $action The name of the method in the derived class to perform for this action.
     *
     * @return  KControllerAbstract
     */
    public function registerActionAlias($alias, $action)
    {
        $alias = strtolower($alias);

        if (!in_array($alias, $this->getActions())) {
            $this->_action_map[$alias] = $action;
        }

        //Force reload of the actions
        $this->_actions = array_unique(array_merge($this->_actions, array_keys($this->_action_map)));

        return $this;
    }

	/**
     * Set a request properties
     *
     * @param  	string 	$property The property name.
     * @param 	mixed 	$value    The property value.
     */
 	public function __set($property, $value)
    {
    	$this->_request->$property = $value;
  	}

  	/**
     * Get a request property
     *
     * @param  	string 	$property The property name.
     * @return 	string 	The property value.
     */
    public function __get($property)
    {
    	$result = null;
    	if(isset($this->_request->$property)) {
    		$result = $this->_request->$property;
    	}

    	return $result;
    }

    /**
     * Execute a controller action by it's name.
	 *
	 * Function is also capable of checking is a behavior has been mixed successfully using is[Behavior] function. If
     * the behavior exists the function will return TRUE, otherwise FALSE.
     *
     * @param  string  $method Method name
     * @param  array   $args   Array containing all the arguments for the original call
     * @return mixed
     * @see execute()
     */
    public function __call($method, $args)
    {
        //Handle action alias method
        if(in_array($method, $this->getActions()))
        {
            //Get the data
            $data = !empty($args) ? $args[0] : array();

            //Create a context object
            if(!($data instanceof KCommandContext))
            {
                $context = $this->getCommandContext();
                $context->data   = $data;
                $context->result = false;
            }
            else $context = $data;

            //Execute the action
            return $this->execute($method, $context);
        }

        //Check if a behavior is mixed
		$parts = KStringInflector::explode($method);

		if($parts[0] == 'is' && isset($parts[1]))
		{
		    //Lazy mix behaviors
		    $behavior = strtolower($parts[1]);

            if(!isset($this->_mixed_methods[$method]))
            {
                if($this->hasBehavior($behavior))
                {
                    $this->mixin($this->getBehavior($behavior));
                    return true;
		        }

			    return false;
            }

            return true;
		}

        return parent::__call($method, $args);
    }
}