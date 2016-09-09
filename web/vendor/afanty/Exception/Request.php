<?php

namespace Afanty\Exception;


class Request extends \Exception
{

    public function __construct($message)
    {
        parent::__construct($message, 400);
    }

}
