<?php

namespace NoxLogic\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Repository
 *
 * @ORM\Table(name="repository")
 * @ORM\Entity(repositoryClass="NoxLogic\AppBundle\Repository\RepositoryRepository")
 */
class Repository
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string name of the repository (without .git)
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var RepoRight
     *
     * @ORM\OneToMany(targetEntity="NoxLogic\AppBundle\Entity\RepoRight", mappedBy="repository")
     */
    protected $rights;

    /**
     * @var User Owner of the repo
     *
     * @ORM\ManyToOne(targetEntity="NoxLogic\AppBundle\Entity\User")
     */
    protected $owner;

    /**
     * @var string Small description of the repository
     *
     * @ORM\COlumn(name="description", type="string", length=255)
     */
    protected $description;


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
     * Set name
     *
     * @param string $name
     * @return Repository
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rights = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add rights
     *
     * @param \NoxLogic\AppBundle\Entity\RepoRight $rights
     * @return Repository
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

    /**
     * Set description
     *
     * @param string $description
     * @return Repository
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set owner
     *
     * @param \NoxLogic\AppBundle\Entity\User $owner
     * @return Repository
     */
    public function setOwner(\NoxLogic\AppBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \NoxLogic\AppBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
