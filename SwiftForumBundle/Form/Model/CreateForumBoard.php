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

use Talis\SwiftForumBundle\Model\ForumBoard;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Create Forum Board Model
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class CreateForumBoard
{
    /**
     * @Assert\Type(type="Talis\SwiftForumBundle\Model\ForumBoard")
     * @Assert\Valid()
     */
    protected $forumBoard;

    /**
     * @Assert\Type(type="integer", message="{{ value }} is not a valid {{ type }}.")
     */
    protected $iconId;

    public function setForumBoard(ForumBoard $forumBoard)
    {
        $this->forumBoard = $forumBoard;
    }

    public function getForumBoard()
    {
        return $this->forumBoard;
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
