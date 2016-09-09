<?php

namespace Afanty\Exception;


class Forbidden extends \Exception
{

    public function __construct($message)
    {
        parent::__construct($message, 403);
    }

}