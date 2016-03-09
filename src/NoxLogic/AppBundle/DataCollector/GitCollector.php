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

        usort($calls['nocache'], function ($a, $b) {
            return $a['count'] < $b['count'];
        });

        usort($calls['cache'], function ($a, $b) {
            return $a['count'] < $b['count'];
        });


        $this->data = array(
            'calls' => $calls,
        );
    }

    public function getCount($key)
    {
        $count = 0;

        array_walk($this->data['calls'][$key], function ($e) use (&$count) {
            $count += $e['count'];
        });

        return $count;
    }

    public function getCalls($key)
    {
        return $this->data['calls'][$key];
    }


    public function getName()
    {
        return 'git.collector';
    }
}
