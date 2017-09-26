<?php

namespace GrazeTech\SabreAPI\Workflow;

class Workflow
{
    /**
     *
     * @var SharedContext
     */
    private $sharedContext;

    /**
     *
     * @var Activity
     */
    private $startActivity;

    /**
     *
     * @param Activity $startActivity
     */
    public function __construct($startActivity)
    {
        $this->startActivity = $startActivity;
    }

    /**
     *
     * @return SharedContext
     */
    public function runWorkflow()
    {
        $this->sharedContext = new SharedContext;
        $next = $this->startActivity;
        while ($next) {
            $next = $next->run($this->sharedContext);
        }
        return $this->sharedContext;
    }
}
