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
use Talis\SwiftForumBundle\Model\ForumBoard;
use Talis\SwiftForumBundle\Model\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Forum Topic Entity Superclass
 *
 * @todo Having creator & creation time in the topic might be unnecessary, as they can be obtained from the post that goes along with it. For now I'll leave it in though.
 * @todo Would it be good to have number of posts stored directly here, rather than calculating & caching ?
 *
 * @ORM\Table(name="forumTopics")
 * @ORM\Entity(repositoryClass="Talis\SwiftForumBundle\Model\ForumTopicRepository")
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForumTopic implements \Serializable
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "3",
     *      max = "100",
     *      minMessage = "Topic titles must be at least {{ limit }} characters",
     *      maxMessage = "Topic titles cannot be longer than {{ limit }} characters"
     * )
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Icons")
     * @ORM\JoinColumn(name="icon", referencedColumnName="id")
     */
    protected $icon;

    /**
     * @ORM\ManyToOne(targetEntity="ForumBoard", inversedBy="topics")
     * @ORM\JoinColumn(name="board", referencedColumnName="id")
     */
    protected $board;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="topics")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id")
     */
    protected $creator;

    /**
     * @ORM\Column(name="creationDate", type="datetime")
     */
    protected $creationDate;

    /**
     * @todo Is this the best way? It means that this info is stored doubly: once in the last post itself and once in the table
     * @todo However, this will make the topic sorting MUCH easier.
     * @ORM\Column(name="lastPostDate", type="datetime")
     */
    protected $lastPostDate;

    /**
     * @ORM\Column(name="isSticky", type="boolean")
     */
    protected $isSticky;

    /**
     * @ORM\Column(name="isLocked", type="boolean")
     */
    protected $isLocked;

    /**
     * @ORM\Column(name="views", type="integer")
     */
    protected $views;

    /**
     * @OneToMany(targetEntity="ForumPost", mappedBy="topic")
     * @OrderBy({"creationDate" = "ASC"})
     */
    protected $posts;

    public function __construct()
    {
        $this->isSticky = false;
        $this->isLocked = false;
        $this->views = 0;
        $this->creationDate = new \DateTime("now");
        $this->lastPostDate = new \DateTime("now");
        $this->posts = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return ForumTopic
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set icon
     *
     * @param Icons $icon
     * @return ForumTopic
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
     * Set board
     *
     * @param ForumBoard $forumBoard
     * @return ForumTopic
     */
    public function setBoard(ForumBoard $forumBoard)
    {
        $this->board = $forumBoard;

        return $this;
    }

    /**
     * Get board
     *
     * @return ForumBoard
     */
    public function getBoard()
    {
        return $this->board;
    }

    /**
     * Set creator
     *
     * @param User $user
     * @return ForumTopic
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
     * @return ForumTopic
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
     * Set isSticky
     *
     * @param boolean $isSticky
     * @return ForumTopic
     */
    public function setIsSticky($isSticky)
    {
        $this->isSticky = $isSticky;

        return $this;
    }

    /**
     * Get isSticky
     *
     * @return bool
     */
    public function getIsSticky()
    {
        return $this->isSticky;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     * @return ForumTopic
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return bool
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set views
     *
     * @param int $views
     * @return ForumTopic
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set lastPostDate
     *
     * @param \DateTime $time
     * @return ForumTopic
     */
    public function setLastPostDate(\DateTime $time = null)
    {
        $this->lastPostDate = $time;

        return $this;
    }

    /**
     * Get lastPostDate
     *
     * @return \DateTime
     */
    public function getLastPostDate()
    {
        return $this->lastPostDate;
    }

}
