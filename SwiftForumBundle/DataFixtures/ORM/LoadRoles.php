<?php
/*
* This file is part of the Swift Forum package.
*
* (c) SwiftForum <https://github.com/swiftforum>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Talis\SwiftForumBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Talis\SwiftForumBundle\Model\Role;

/**
 * Fixture for loading Roles into the database.
 *
 * @package Talis\SwiftForumBundle\DataFixtures\ORM
 * @author Felix Kastner <felix@chapterfain.com>
 */
class LoadRoles implements FixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $rolelist = array(
            array('name' => 'Banned', 'role' => 'ROLE_BANNED'),
            array('name' => 'Warned', 'role' => 'ROLE_WARNED'),
            array('name' => 'Guest', 'role' => 'ROLE_GUEST'),
            array('name' => 'Non-Member', 'role' => 'ROLE_USER'),
            array('name' => 'Member', 'role' => 'ROLE_MEMBER'),
            array('name' => 'Officer', 'role' => 'ROLE_OFFICER'),
            array('name' => 'Master', 'role' => 'ROLE_MASTER'),
            array('name' => 'Admin', 'role' => 'ROLE_ADMIN'),
            array('name' => 'Creator', 'role' => 'ROLE_SUPER_ADMIN')
        );

        foreach($rolelist as $roleArray) {
            $role = new Role();
            $role->setName($roleArray['name']);
            $role->setRole($roleArray['role']);
            $manager->persist($role);
        }
        $manager->flush();
    }
} 