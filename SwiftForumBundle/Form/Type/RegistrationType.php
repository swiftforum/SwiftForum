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
 * Registration type
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class RegistrationType extends AbstractType
{
    private $entityPath;

    public function __construct($entityPath) {
        $this->entityPath = $entityPath;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', new UserType($this->entityPath));
        $builder->add('password', 'repeated', array(
                'first_name' => 'password',
                'second_name' => 'password_repeat',
                'type' => 'password',
                'invalid_message' => 'The passwords must match',
            ));
        $builder->add('Register', 'submit', array('attr' => array('class' => 'btn btn-primary')));
    }

    public function getName()
    {
        return 'registration';
    }
} 