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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Forum Category Entity Superclass
 *
 * @ORM\Table(name="forumCats")
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\ForumCategoryRepository")
 * @UniqueEntity(
 *      fields = "name",
 *      message= "A category with this name already exists."
 * )
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
     *      maxMessage = "Category names cannot be longer than {{ limit }} characters"
     * )
     * @Assert\NotEqualTo(
     *      value = "System",
     *      message = "Category may not be named System"
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
     * Set icon
     *
     * @param Icons $icon
     * @return ForumCategory
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

    public function getPos()
    {
        return ($this->id + $this->orderOffset);
    }

}