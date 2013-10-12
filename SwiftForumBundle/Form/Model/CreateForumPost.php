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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Create Forum Post Model
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class CreateForumPost
{
    /**
     * @Assert\Type(type="Talis\SwiftForumBundle\Model\ForumPost")
     * @Assert\Valid()
     */
    protected $forumPost;

    public function setForumPost(ForumPost $forumPost)
    {
        $this->forumPost = $forumPost;
    }

    public function getForumPost()
    {
        return $this->forumPost;
    }

} 