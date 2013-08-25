<?php
/*
* This file is part of the Swift Forum package.
*
* (c) SwiftForum <https://github.com/swiftforum>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Talis\SwiftForumBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User Entity Superclass
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\UserRepository")
 * @UniqueEntity(
 *      fields = "username",
 *      message= "This username is already taken."
 * )
 * @UniqueEntity(
 *      fields = "email",
 *      message="This email is already in use."
 * )
 * @author Felix Kastner <felix@chapterfain.com>
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2",
     *      max = "25",
     *      minMessage = "Your username must be at least {{ limit }} characters length",
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters length"
     * )
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=88)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "'{{ value }}' is not a valid email address",
     *     checkMX = true,
     *     checkHost = true)
     */
    protected $email;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="id")
     */
    protected $role;

    /**
     * @ORM\Column(name="isActive", type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @ORM\Column(name="createdDate", type="datetime")
     */
    protected $createdDate;

    /**
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(name="statusDate", type="datetime", nullable=true)
     */
    protected $statusDate;

    public function __construct()
    {
        $this->isActive = false;
        $this->salt = md5(uniqid(null, true));
        //$this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->createdDate = new \DateTime("now");
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->role);
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
                $this->id,
                $this->password,
                $this->isActive,
                $this->salt,
                $this->username
            ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->password,
            $this->isActive,
            $this->salt,
            $this->username
            ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    // Returns a link to the user's avatar
    // Will default to gravatar if not set
    // $size: Preferred image size (defaults to 20)
    public function getAvatar($size = 20)
    {
        return "http://www.gravatar.com/avatar/" . urlencode(md5($this->email)) . "?s=" . urlencode($size);
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set role
     *
     * @param \Talis\SwiftForumBundle\Model\Role $role
     * @return User
     */
    public function setRole(\Talis\SwiftForumBundle\Model\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get URL to profile page
     *
     * @return string
     */
    public function getUrl()
    {
        // TODO: Return actual URL to profile page
        return "/roster#" . $this->id;
    }

    /**
     * Get role
     *
     * @return \Talis\SwiftForumBundle\Model\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTime $time = null)
    {
        $this->createdDate = $time;

        return $this;
    }

    public function setStatus($status = null)
    {
        $this->status = $status;

        // If the status was set to null, set update time to null as well, otherwise set it to now
        if(!is_null($status)) {
            $this->setStatusDate(new \DateTime("now"));
        } else {
            $this->setStatusDate();
        }

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatusDate(\DateTime $statusDate = null)
    {
        $this->statusDate = $statusDate;

        return $this;
    }

    public function getStatusDate()
    {
        return $this->statusDate;
    }
}
