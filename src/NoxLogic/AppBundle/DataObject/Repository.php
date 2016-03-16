<?php

namespace NoxLogic\AppBundle\DataObject;

use NoxLogic\AppBundle\Entity\Repository as RepositoryEntity;
use NoxLogic\AppBundle\Service\GitService;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Repository {

    /** @var RepositoryEntity */
    protected $entity;

    /** @var \NoxLogic\AppBundle\Service\GitService */
    protected $gitService;

    /** @var \Symfony\Component\PropertyAccess\PropertyAccessor */
    protected $accessor;

    function __construct(RepositoryEntity $entity, GitService $gitService)
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();

        $this->entity = $entity;
        $this->gitService = $gitService;
    }

    function getTotalCommits()
    {
        return $this->gitService->getTotalCommits();
    }

    function getContributors()
    {
        return $this->gitService->getContributors();
    }


    function __get($property)
    {
        return $this->accessor->getValue($this->entity, $property);
    }

    function __isset($property)
    {
        return $this->accessor->isReadable($this->entity, $property);
    }

}
