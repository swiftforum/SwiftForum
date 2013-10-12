<?php
/*
* This file is part of the Swift Forum package.
*
* (c) SwiftForum <https://github.com/swiftforum>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Talis\SwiftForumBundle\Form\Model;

use Talis\SwiftForumBundle\Model\ForumPost;
use Talis\SwiftForumBundle\Model\ForumTopic;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Create Forum Topic Model
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class CreateForumTopic
{
    /**
     * @Assert\Type(type="Talis\SwiftForumBundle\Model\ForumTopic")
     * @Assert\Valid()
     */
    protected $forumTopic;

    /**
     * @Assert\Type(type="Talis\SwiftForumBundle\Model\ForumPost")
     * @Assert\Valid()
     */
    protected $forumPost;

    /**
     * @Assert\Type(type="integer", message="{{ value }} is not a valid {{ type }}.")
     */
    protected $iconId;

    public function setForumTopic(ForumTopic $forumTopic)
    {
        $this->forumTopic = $forumTopic;
    }

    public function getForumTopic()
    {
        return $this->forumTopic;
    }

    public function setForumPost(ForumPost $forumPost)
    {
        $this->forumPost = $forumPost;
    }

    public function getForumPost()
    {
        return $this->forumPost;
    }

    public function setIconId($iconId)
    {
        $this->iconId = $iconId;
    }

    public function getIconId()
    {
        return $this->iconId;
    }
} 