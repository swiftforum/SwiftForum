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
 * Create Forum Category Type
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class CreateForumCategoryType extends AbstractType
{
    private $entityPath;

    public function __construct($entityPath) {
        $this->entityPath = $entityPath;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('forumcategory', 'talis_admin_forum_category');
        $builder->add('iconid', 'integer', array('label' => 'Icon ID', 'required' => false, 'attr' => array('class' => 'iconpicker')));
        $builder->add('Save', 'submit', array('attr' => array('class' => 'btn btn-primary')));
    }

    public function getName()
    {
        return 'talis_admin_create_forum_category';
    }
}