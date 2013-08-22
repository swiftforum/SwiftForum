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
 * Role Repository
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class RoleRepository extends EntityRepository
{

    /**
     * Returns a QueryBuilder object that grabs roles that are in the the permitted array parameter
     *
     * @param Array $permitted
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getPermittedRoles($permitted)
    {
        return $this->createQueryBuilder('r')
                ->where("r.role IN(:permitted)")
                ->setParameter('permitted', array_values($permitted))
                ->orderBy('r.id', 'desc');
    }
}