<?php

namespace Talis\TrickPlayBundle\Entity;

use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Talis\TrickPlayBundle\Entity\Character
 *
 * @ORM\Table(name="characters",
 *      uniqueConstraints={@UniqueConstraint(name="uniquePrimaryChar",columns={"userId","isPrimary"})})
 * @ORM\Entity()
 * @UniqueEntity(
 *      fields={"user", "isPrimary"},
 *      message= "This account already has a primary character."
 * )
 */
class Character implements \Serializable
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="characters")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     **/
    private $user;

    /**
     * @ORM\Column(name="charName", type="string", length=60, nullable=true)
     */
    private $charName = '';

    /**
     * @ORM\Column(name="charJobPrimary", type="string", length=60, nullable=true)
     */
    private $charJobPrimary = '';

    /**
     * @ORM\Column(name="charJobSecondary", type="string", length=60, nullable=true)
     */
    private $charJobSecondary = '';

    /**
     * @ORM\Column(name="charProfPrimary", type="string", length=60, nullable=true)
     */
    private $charProfPrimary = '';

    /**
     * @ORM\Column(name="charProfSecondary", type="string", length=60, nullable=true)
     */
    private $charProfSecondary = '';

    /**
     * @ORM\Column(name="isPrimary", type="boolean")
     */
    private $isPrimary = true;

    public function __construct()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
                $this->id
            ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id
            ) = unserialize($serialized);
    }

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
     * Set charName
     *
     * @param string $charName
     * @return Character
     */
    public function setCharName($charName)
    {
        $this->charName = $charName;
    
        return $this;
    }

    /**
     * Get charName
     *
     * @return string 
     */
    public function getCharName()
    {
        return $this->charName;
    }

    /**
     * Set charJobPrimary
     *
     * @param string $charJobPrimary
     * @return Character
     */
    public function setCharJobPrimary($charJobPrimary)
    {
        $this->charJobPrimary = $charJobPrimary;
    
        return $this;
    }

    /**
     * Get charJobPrimary
     *
     * @return string 
     */
    public function getCharJobPrimary()
    {
        return $this->charJobPrimary;
    }

    /**
     * Set charJobSecondary
     *
     * @param string $charJobSecondary
     * @return Character
     */
    public function setCharJobSecondary($charJobSecondary)
    {
        $this->charJobSecondary = $charJobSecondary;
    
        return $this;
    }

    /**
     * Get charJobSecondary
     *
     * @return string 
     */
    public function getCharJobSecondary()
    {
        return $this->charJobSecondary;
    }

    /**
     * Set charProfPrimary
     *
     * @param string $charProfPrimary
     * @return Character
     */
    public function setCharProfPrimary($charProfPrimary)
    {
        $this->charProfPrimary = $charProfPrimary;
    
        return $this;
    }

    /**
     * Get charProfPrimary
     *
     * @return string 
     */
    public function getCharProfPrimary()
    {
        return $this->charProfPrimary;
    }

    /**
     * Set charProfSecondary
     *
     * @param string $charProfSecondary
     * @return Character
     */
    public function setCharProfSecondary($charProfSecondary)
    {
        $this->charProfSecondary = $charProfSecondary;
    
        return $this;
    }

    /**
     * Get charProfSecondary
     *
     * @return string 
     */
    public function getCharProfSecondary()
    {
        return $this->charProfSecondary;
    }

    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     * @return Character
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = $isPrimary;
    
        return $this;
    }

    /**
     * Get isPrimary
     *
     * @return boolean 
     */
    public function getIsPrimary()
    {
        return $this->isPrimary;
    }

    /**
     * Set user
     *
     * @param \Talis\TrickPlayBundle\Entity\User $user
     * @return Character
     */
    public function setUser(\Talis\TrickPlayBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Talis\TrickPlayBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}