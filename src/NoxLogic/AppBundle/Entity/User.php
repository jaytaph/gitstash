<?php

namespace NoxLogic\AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="NoxLogic\AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();

        $this->repositories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @var string Users real name
     *
     * @ORM\Column(name="realname", type="string", length=255, nullable=true)
     */
    protected $realname;

    /**
     * @var AuthorizedKeys[] Keys attached to user
     *
     * @ORM\OneToMany(targetEntity="NoxLogic\AppBundle\Entity\AuthorizedKeys", mappedBy="user")
     */
    protected $sshKeys;

    /**
     * @var RepoRight[] Rights per repo attached to the user
     *
     * @ORM\OneToMany(targetEntity="NoxLogic\AppBundle\Entity\RepoRight", mappedBy="user")
     */
    protected $rights;


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
     * Set realname
     *
     * @param string $realname
     * @return User
     */
    public function setRealname($realname)
    {
        $this->realname = $realname;

        return $this;
    }

    /**
     * Get realname
     *
     * @return string 
     */
    public function getRealname()
    {
        return $this->realname;
    }

    /**
     * Add repositories
     *
     * @param \NoxLogic\AppBundle\Entity\Repository $repositories
     * @return User
     */
    public function addRepository(\NoxLogic\AppBundle\Entity\Repository $repositories)
    {
        $this->repositories[] = $repositories;

        return $this;
    }

    /**
     * Remove repositories
     *
     * @param \NoxLogic\AppBundle\Entity\Repository $repositories
     */
    public function removeRepository(\NoxLogic\AppBundle\Entity\Repository $repositories)
    {
        $this->repositories->removeElement($repositories);
    }

    /**
     * Get repositories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRepositories()
    {
        return $this->repositories;
    }


    /**
     * Add sshKeys
     *
     * @param \NoxLogic\AppBundle\Entity\AuthorizedKeys $sshKeys
     * @return User
     */
    public function addSshKey(\NoxLogic\AppBundle\Entity\AuthorizedKeys $sshKeys)
    {
        $this->sshKeys[] = $sshKeys;

        return $this;
    }

    /**
     * Remove sshKeys
     *
     * @param \NoxLogic\AppBundle\Entity\AuthorizedKeys $sshKeys
     */
    public function removeSshKey(\NoxLogic\AppBundle\Entity\AuthorizedKeys $sshKeys)
    {
        $this->sshKeys->removeElement($sshKeys);
    }

    /**
     * Get sshKeys
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSshKeys()
    {
        return $this->sshKeys;
    }

    /**
     * Add rights
     *
     * @param \NoxLogic\AppBundle\Entity\RepoRight $rights
     * @return User
     */
    public function addRight(\NoxLogic\AppBundle\Entity\RepoRight $rights)
    {
        $this->rights[] = $rights;

        return $this;
    }

    /**
     * Remove rights
     *
     * @param \NoxLogic\AppBundle\Entity\RepoRight $rights
     */
    public function removeRight(\NoxLogic\AppBundle\Entity\RepoRight $rights)
    {
        $this->rights->removeElement($rights);
    }

    /**
     * Get rights
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRights()
    {
        return $this->rights;
    }
}
