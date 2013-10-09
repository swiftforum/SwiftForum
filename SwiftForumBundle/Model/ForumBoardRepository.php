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

use Doctrine\ORM\EntityRepository;
/**
 * ForumBoardRepository
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForumBoardRepository extends EntityRepository
{

    /**
     * Gets the ordered list of boards
     *
     * @return array
     */
    public function getBoards()
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.id + COALESCE(f.orderOffset, 0)', 'asc')
            ->addOrderBy('f.id', 'asc')
            ->getQuery()
            ->getResult();
    }

}
