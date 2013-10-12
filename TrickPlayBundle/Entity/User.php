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

    /**
     * @ORM\OneToOne(targetEntity="LodestoneCharacter", mappedBy="user")
     */
    private $character;

    public function __construct()
    {
        parent::__construct();
        $this->characters = new ArrayCollection();
    }

    // Get character
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * Get user profile URL
     *
     * @return string
     */
    public function getUrl()
    {
        return "/roster";
    }

    // Return avatar (overwrite)
    public function getAvatar($size = 20)
    {
        $character = $this->getcharacter();
        return $character ? $character->getPicture() : BaseUser::getAvatar($size);
    }

    // Return vanity name (overwrite)
    public function getVanityName()
    {
        $character = $this->getcharacter();
        return $character ? $character->getName() : BaseUser::getVanityName();
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
