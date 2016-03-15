<?php

namespace NoxLogic\AppBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use NoxLogic\AppBundle\Entity\Repository;
use NoxLogic\AppBundle\Entity\User;
use Symfony\Component\Process\ProcessBuilder;

class RepoService {

    public function __construct(Registry $doctrine, $basePath)
    {
        $this->doctrine = $doctrine;
        $this->basePath = $basePath;
    }

    public function create(User $user, Repository $repo)
    {
        $repo->setOwner($user);

        // Add DB info
        $manager = $this->doctrine->getManager();
        $manager->persist($repo);
        $manager->flush();

        $gitPath = $this->getGitPath($repo);
        @mkdir($gitPath, 0777, true);

        $process = ProcessBuilder::create(array('git', '--git-dir='.$gitPath, 'init', '--bare'))->getProcess();
        $process->mustRun();

        return true;
    }

    public function getGitPath(Repository $repo)
    {
        return $this->basePath.'/'.strtolower($repo->getOwner()->getUsernameCanonical()).'/'.strtolower($repo->getName()).'.git';
    }

}
