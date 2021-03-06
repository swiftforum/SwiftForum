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
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Forum Board Type
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForumBoardType extends AbstractType
{
    private $entityPath;

    public function __construct($tsforum) {
        $this->entityPath = $tsforum['path'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array('label' => 'Board Name'));
        $builder->add('role', 'talis_admin_forum_board_role');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => $this->entityPath . '\Entity\ForumBoard',
            ));
    }

    public function getName()
    {
        return 'talis_admin_forum_board';
    }
} 
