<?php

namespace NoxLogic\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuthorizedKeys
 *
 * @ORM\Table(name="authorized_keys")
 * @ORM\Entity(repositoryClass="NoxLogic\AppBundle\Repository\AuthorizedKeysRepository")
 */
class AuthorizedKeys
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
     * @var string actual SSH key
     * @TODO: This is set to 512. We should set it to text type, but this causes issues when adding a unique constraint to it
     *
     * @ORM\Column(name="sshkey", type="string", length=512, unique=true, options={"collation":"ascii_bin"})
     */
    private $sshkey;

    /**
     * @var string Fingerprint of the SHA key
     *
     * @ORM\Column(name="fingerprint", type="string", length=100, unique=true)
     */
    private $fingerprint;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="NoxLogic\AppBundle\Entity\User", inversedBy="sshKeys")
     */
    protected $user;

    /**
     * @var string Label of key
     *
     * @ORM\Column(name="label", type="string", length=100)
     */
    protected $label;


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
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add user
     *
     * @param \NoxLogic\AppBundle\Entity\User $user
     * @return AuthorizedKeys
     */
    public function addUser(\NoxLogic\AppBundle\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \NoxLogic\AppBundle\Entity\User $user
     */
    public function removeUser(\NoxLogic\AppBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set sshkey
     *
     * @param string $sshkey
     * @return AuthorizedKeys
     */
    public function setSshkey($sshkey)
    {
        $this->sshkey = $sshkey;

        return $this;
    }

    /**
     * Get sshkey
     *
     * @return string 
     */
    public function getSshkey()
    {
        return $this->sshkey;
    }

    /**
     * Set user
     *
     * @param \NoxLogic\AppBundle\Entity\User $user
     * @return AuthorizedKeys
     */
    public function setUser(\NoxLogic\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set fingerprint
     *
     * @param string $fingerprint
     * @return AuthorizedKeys
     */
    public function setFingerprint($fingerprint)
    {
        $this->fingerprint = $fingerprint;

        return $this;
    }

    /**
     * Get fingerprint
     *
     * @return string 
     */
    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

}
