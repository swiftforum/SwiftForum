<?php
/*
* This file is part of the Swift Forum package.
*
* (c) SwiftForum <https://github.com/swiftforum>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Talis\SwiftForumBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Forgot Password Type
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForgotPasswordType extends AbstractType
{
    private $entityPath;

    public function __construct($entityPath) {
        $this->entityPath = $entityPath;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email');
        $builder->add('Send Password Recovery Email', 'submit', array('attr' => array('class' => 'btn btn-primary')));
    }

    public function getName()
    {
        return 'talis_forgot_password';
    }
} 