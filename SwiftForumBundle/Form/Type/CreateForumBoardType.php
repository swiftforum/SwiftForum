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
 * Create Forum Board Type
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class CreateForumBoardType extends AbstractType
{
    private $entityPath;

    public function __construct($entityPath) {
        $this->entityPath = $entityPath;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('forumboard', 'talis_admin_forum_board');
        $builder->add('iconid', 'integer', array('label' => 'Select Icon', 'required' => false, 'read_only' => true, 'attr' => array('class' => 'iconpicker-field')));
        $builder->add('Save', 'submit', array('attr' => array('class' => 'btn btn-primary')));
    }

    public function getName()
    {
        return 'talis_admin_create_forum_board';
    }
}
