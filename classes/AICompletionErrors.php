<?php
class AICompletionException extends Exception {
    public function __construct($message = "", $request = null, $response = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->request = $request;
        $this->response = $response;
    }

    public $request;
    public $response;
}