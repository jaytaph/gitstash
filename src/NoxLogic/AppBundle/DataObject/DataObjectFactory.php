<?php

namespace NoxLogic\AppBundle\DataObject;

use NoxLogic\AppBundle\Entity\Repository as RepositoryEntity;
use NoxLogic\AppBundle\Service\GitServiceFactory;

class DataObjectFactory {

    /** @var GitServiceFactory */
    protected $gitServiceFactory;

    function __construct(GitServiceFactory $gitServiceFactory)
    {
        $this->gitServiceFactory = $gitServiceFactory;
    }

    function create($entity) {
        switch (true) {
            case $entity instanceof RepositoryEntity :
                return new Repository($entity, $this->gitServiceFactory->create($entity));
        }

        throw new \LogicException(sprintf("Cannot convert '%s' into a data object.", get_class($entity)));
    }

}
