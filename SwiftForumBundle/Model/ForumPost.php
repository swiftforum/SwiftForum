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

use Talis\SwiftForumBundle\Model\ForumTopic;
use Talis\SwiftForumBundle\Model\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Forum Post Entity Superclass
 *
 * @todo Evaluate whether 10000 characters as maximum and 5 chars minimum is a good post size limitation
 *
 * @ORM\Table(name="forumPosts")
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\ForumPostRepository")
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForumPost implements \Serializable
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="content", type="string", length=10000)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "5",
     *      max = "10000",
     *      minMessage = "Posts must contain at least {{ limit }} characters",
     *      maxMessage = "Posts cannot be longer than {{ limit }} characters"
     * )
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="ForumTopic", inversedBy="posts")
     * @ORM\JoinColumn(name="topic", referencedColumnName="id")
     */
    protected $topic;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id")
     */
    protected $creator;

    /**
     * @ORM\Column(name="creationDate", type="datetime")
     */
    protected $creationDate;

    /**
     * @ORM\Column(name="lastEditDate", type="datetime", nullable=true)
     */
    protected $lastEditDate;

    /**
     * @ORM\Column(name="isDeleted", type="boolean")
     */
    protected $isDeleted;

    public function __construct()
    {
        $this->isDeleted = false;
        $this->creationDate = new \DateTime("now");
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
     * Set content
     *
     * @param string $content
     * @return ForumPost
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set topic
     *
     * @param ForumTopic $forumTopic
     * @return ForumPost
     */
    public function setTopic(ForumTopic $forumTopic)
    {
        $this->topic = $forumTopic;

        return $this;
    }

    /**
     * Get topic
     *
     * @return ForumPost
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * Set creator
     *
     * @param User $user
     * @return ForumPost
     */
    public function setCreator(User $user)
    {
        $this->creator = $user;

        return $this;
    }

    /**
     * Get creator
     *
     * @return boolean
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $time
     * @return ForumPost
     */
    public function setCreationDate(\DateTime $time = null)
    {
        $this->creationDate = $time;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set lastEditDate
     *
     * @param \DateTime $time
     * @return ForumPost
     */
    public function setLastEditDate(\DateTime $time = null)
    {
        $this->lastEditDate = $time;

        return $this;
    }

    /**
     * Get lastEditDate
     *
     * @return \DateTime
     */
    public function getLastEditDate()
    {
        return $this->lastEditDate;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return ForumPost
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

}
