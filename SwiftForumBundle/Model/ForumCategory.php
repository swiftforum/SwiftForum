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

use Talis\SwiftForumBundle\Model\Icons;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Forum Category Entity Superclass
 *
 * @ORM\Table(name="forumCats")
 * @ORM\Entity()
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForumCategory implements \Serializable
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
     *      minMessage = "Category names must be at least {{ limit }} characters",
     *      maxMessage = "Category name cannot be longer than {{ limit }} characters"
     * )
     */
    protected $name;

    /**
     * @ORM\Column(name="orderOffset", type="integer", nullable=false)
     */
    protected $orderOffset = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Icons")
     * @ORM\JoinColumn(name="iconId", referencedColumnName="id")
     */
    protected $iconId = 0;

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
     * @return ForumCategory
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
     * @return ForumCategory
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
     * Set iconId
     *
     * @param Icons $iconId
     * @return ForumCategory
     */
    public function setIconId(Icons $iconId = null)
    {
        $this->iconId = $iconId;
    
        return $this;
    }

    /**
     * Get iconId
     *
     * @return Icons
     */
    public function getIconId()
    {
        return $this->iconId;
    }
}