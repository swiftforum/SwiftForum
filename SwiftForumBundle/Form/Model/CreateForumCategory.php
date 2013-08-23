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

use Talis\SwiftForumBundle\Model\ForumCategory;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Create Forum Category Model
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class CreateForumCategory
{
    /**
     * @Assert\Type(type="Talis\SwiftForumBundle\Model\ForumCategory")
     * @Assert\Valid()
     */
    protected $forumCategory;

    /**
     * @Assert\Type(type="integer", message="{{ value }} is not a valid {{ type }}.")
     */
    protected $iconId;


    public function setForumCategory(ForumCategory $forumCategory)
    {
        $this->forumCategory = $forumCategory;
    }

    public function getForumCategory()
    {
        return $this->forumCategory;
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