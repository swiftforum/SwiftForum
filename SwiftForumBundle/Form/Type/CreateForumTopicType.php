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
 * Create Forum Topic Type
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class CreateForumTopicType extends AbstractType
{
    private $entityPath;

    public function __construct($entityPath) {
        $this->entityPath = $entityPath;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('forumtopic', 'talis_forum_topic');
        $builder->add('forumpost', 'talis_forum_post');
        $builder->add('iconid', 'integer', array('label' => 'Select Icon', 'required' => false, 'read_only' => true, 'attr' => array('class' => 'iconpicker-field')));
        $builder->add('save', 'submit', array('label' => 'Create Topic', 'attr' => array('class' => 'btn btn-success btn-block')));
    }

    public function getName()
    {
        return 'talis_create_forum_topic';
    }
}