<?php

namespace NoxLogic\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RepoRight
 *
 * @ORM\Table(name="repo_right")
 * @ORM\Entity(repositoryClass="NoxLogic\AppBundle\Repository\RepoRightRepository")
 */
class RepoRight
{
    const FETCH = "FETCH";      // Allowed to read
    const PUSH  = "PUSH";       // Allowed to write

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="NoxLogic\AppBundle\Entity\User", inversedBy="rights")
     */
    protected $user;

    /**
     * @var Repository
     *
     * @ORM\ManyToOne(targetEntity="NoxLogic\AppBundle\Entity\Repository", inversedBy="rights")
     */
    protected $repository;

    /**
     * @var string
     *
     * @ORM\Column(name="right", type="string", length=10)
     */
    protected $right;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set right
     *
     * @param string $right
     * @return RepoRight
     */
    public function setRight($right)
    {
        $this->right = $right;

        return $this;
    }

    /**
     * Get right
     *
     * @return string 
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Set user
     *
     * @param \NoxLogic\AppBundle\Entity\User $user
     * @return RepoRight
     */
    public function setUser(\NoxLogic\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \NoxLogic\AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set repository
     *
     * @param \NoxLogic\AppBundle\Entity\Repository $repository
     * @return RepoRight
     */
    public function setRepository(\NoxLogic\AppBundle\Entity\Repository $repository = null)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Get repository
     *
     * @return \NoxLogic\AppBundle\Entity\Repository 
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
