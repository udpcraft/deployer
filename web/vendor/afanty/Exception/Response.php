<?php

namespace Afanty\Exception;


class Response extends \Exception
{

    public function __construct($message)
    {
        parent::__construct($message, 500);
    }

}