<?php

namespace Command\Project;

abstract class Handler extends \Afanty\Helper\Singleton
{
    protected $_successor = null;

    private static $_step = 1;

    private static $_steps = null;

    private static $_handlers = null;

    final public function setSuccessor(Handler $handler)
    {
        $this->_successor = $handler;

        return $handler;
    }

    public function getSuccessor()
    {
        return $this->_successor;
    }

    final public function handle(Request $request)
    {
        if (! self::$_handlers) {
            self::$_handlers = $this;
        }

        if (! self::$_steps) {
            $i = 1;
            $successor = self::$_handlers;
            while($successor->getSuccessor()) {
                $i++;
                $successor = $successor->getSuccessor();
            }

            self::$_steps = $i;
        }
        
        $response = $this->_process($request);

        $response['current_step'] = self::$_step;
        $response['total_steps'] = self::$_steps;

        \Helper\Project::upProcessing($request->get('projectInfo'), $response);

        self::$_step++;

        if ($this->_successor !== null) {
            $this->_successor->handle($request);
        }
    }

    abstract protected function _process(Request $request);
    
}

