<?php

declare(strict_types=1);

namespace Machour\Konnect;

class ApiException extends \Exception
{
    /**
     * @var array
     */
    public $errors;

    public function __construct($errors, $message = "", $code = 0, \Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

}