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
 * Icons Repository
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class IconsRepository extends EntityRepository
{

    /**
     * Gets the list of icons
     *
     * @return array
     */
    public function getIcons()
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.id', 'asc')
            ->getQuery()
            ->useResultCache(true, null, 'icons')
            ->getResult();

    }
}