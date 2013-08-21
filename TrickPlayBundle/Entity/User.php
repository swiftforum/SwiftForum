<?php

namespace Talis\TrickPlayBundle\Entity;

use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Talis\SwiftForumBundle\Model\User as BaseUser;
use Talis\TrickPlayBundle\Entity\Character;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
/**
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\UserRepository")
 * @ORM\Table(name="users")
*/
class User extends BaseUser implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\OneToMany(targetEntity="Character", mappedBy="user")
     */
    private $characters;

    public function __construct()
    {
        parent::__construct();
        $this->characters = new ArrayCollection();
    }

    /**
     * Add characters
     *
     * @param Character $characters
     * @return User
     */
    public function addCharacter(Character $characters)
    {
        $this->characters[] = $characters;

        return $this;
    }

    /**
     * Remove characters
     *
     * @param Character $characters
     */
    public function removeCharacter(Character $characters)
    {
        $this->characters->removeElement($characters);
    }

    /**
     * Get characters
     *
     * @return ArrayCollection
     */
    public function getCharacters()
    {
        return $this->characters;
    }

}