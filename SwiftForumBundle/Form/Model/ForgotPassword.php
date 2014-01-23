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

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Forgot Password model
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForgotPassword
{
    /**
     * @Assert\Length(
     *      max = "255",
     *      maxMessage = "Your password cannot be longer than {{ limit }} characters length"
     * )
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "'{{ value }}' is not a valid email address",
     *     checkMX = true,
     *     checkHost = true)
     */
    protected $email;

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
} 