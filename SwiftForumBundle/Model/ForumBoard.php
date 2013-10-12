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
use Talis\SwiftForumBundle\Model\Icons;
use Talis\SwiftForumBundle\Model\ForumCategory;
use Talis\SwiftForumBundle\Model\Role;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Forum Board Entity Superclass
 *
 * @ORM\Table(name="forumBoards")
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\ForumBoardRepository")
 * @UniqueEntity(
 *      fields = "name",
 *      message= "A board with this name already exists."
 * )
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForumBoard implements \Serializable
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2",
     *      max = "40",
     *      minMessage = "Board names must be at least {{ limit }} characters",
     *      maxMessage = "Board names cannot be longer than {{ limit }} characters"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(name="orderOffset", type="integer", nullable=true)
     * @Assert\Type(type="integer", message="{{ value }} is not a valid {{ type }}.")
     */
    protected $orderOffset;

    /**
     * @ORM\ManyToOne(targetEntity="Icons")
     * @ORM\JoinColumn(name="icon", referencedColumnName="id")
     */
    protected $icon;

    /**
     * @ORM\ManyToOne(targetEntity="ForumCategory", inversedBy="boards")
     * @ORM\JoinColumn(name="category", referencedColumnName="id")
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="id")
     */
    protected $role;

    /**
     * @OneToMany(targetEntity="ForumTopic", mappedBy="board")
     * @OrderBy({"lastPostDate" = "DESC"})
     */
    protected $topics;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return ForumBoard
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
     * Set orderOffset
     *
     * @param integer $orderOffset
     * @return ForumBoard
     */
    public function setOrderOffset($orderOffset)
    {
        $this->orderOffset = $orderOffset;

        return $this;
    }

    /**
     * Get orderOffset
     *
     * @return integer
     */
    public function getOrderOffset()
    {
        return $this->orderOffset;
    }

    /**
     * Set icon
     *
     * @param Icons $icon
     * @return ForumBoard
     */
    public function setIcon(Icons $icon = null)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return Icons
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set category
     *
     * @param ForumCategory $forumCategory
     * @return ForumBoard
     */
    public function setCategory(ForumCategory $forumCategory)
    {
        $this->category = $forumCategory;

        return $this;
    }

    /**
     * Get category
     *
     * @return ForumCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set role
     *
     * @param Role $role
     * @return ForumBoard
     */
    public function setRole($role)
    {
        $this->role = $role->getRole();

        return $this;
    }

    /**
     * Set role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    public function getPos()
    {
        return ($this->id + $this->orderOffset);
    }

    public function getTopics()
    {
        return $this->topics;
    }

}
