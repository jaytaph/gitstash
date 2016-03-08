<?php

namespace NoxLogic\AppBundle\DataCollector;

use NoxLogic\AppBundle\Logger\GitLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class GitCollector extends DataCollector
{

    /** @var GitLogger */
    protected $logger;

    public function __construct(GitLogger $logger)
    {
        $this->logger = $logger;
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $calls = $this->logger->getCalls();

        usort($calls, function ($a, $b) {
            return $a['count'] < $b['count'];
        });

        $this->data = array(
            'calls' => $calls,
        );
    }

    public function getCount()
    {
        $count = 0;

        array_walk($this->data['calls'], function ($e) use (&$count) {
            $count += $e['count'];
        });

        return $count;
    }

    public function getCalls()
    {
        return $this->data['calls'];
    }


    public function getName()
    {
        return 'git.collector';
    }
}
