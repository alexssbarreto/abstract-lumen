<?php

namespace Abtechi\Laravel;

/**
 * Class Result
 * @package Abtechi\Laravel
 */
class Result
{
    protected $result,
        $message,
        $data,
        $params;

    /**
     * Result constructor.
     * @param $result
     * @param $message
     * @param $data
     * @param $params
     */
    public function __construct($result = false, $message = null, $data = null, $params = null)
    {
        $this->result = $result;
        $this->message = $message;
        $this->data = $data;
        $this->params = $params;
    }

    /**
     * @return bool
     */
    public function isResult()
    {
        return $this->result;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return null
     */
    public function getParams()
    {
        return $this->params;
    }
}