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

use Talis\SwiftForumBundle\Model\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Registration model
 *
 * @Assert\Callback(methods={"isPasswordValid"})
 * @author Felix Kastner <felix@chapterfain.com>
 */
class Registration
{
    /**
     * @Assert\Type(type="Talis\SwiftForumBundle\Model\User")
     * @Assert\Valid()
     */
    protected $user;

    /**
     * @Assert\Length(
     *      min = "3",
     *      max = "50",
     *      minMessage = "Your password must be at least {{ limit }} characters length",
     *      maxMessage = "Your password cannot be longer than {{ limit }} characters length"
     * )
     */
    protected $password;

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function isPasswordValid(ExecutionContextInterface $context)
    {
        $user = $this->getUser();
        if ($user->getUsername() == $this->password) {
            $context->addViolationAt('password', 'Your password can\'t be the same as your username', array(), null);
        }

        if (!preg_match('/[A-Za-z]/', $this->password) || !preg_match('/[0-9]/', $this->password)) {
            $context->addViolationAt('password', 'Your password must have both letters and numbers', array(), null);
        }
    }
} 