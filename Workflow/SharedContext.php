<?php

namespace GrazeTech\SabreAPI\Workflow;

class SharedContext
{
    /**
     *
     * @var array
     */
    private $results = [];

    /**
     *
     */
    public function __construct()
    {
        $this->results['SECURITY'] = null;
    }

    /**
     *
     * @param string $key
     * @param mixed $result
     */
    public function addResult(string $key, $result)
    {
        $this->results[$key] = $result;
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function getResult(string $key)
    {
        return isset($this->results[$key]) ? $this->results[$key] : null;
    }
}
