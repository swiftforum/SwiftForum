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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Forum Post Type
 *
 * @author Felix Kastner <felix@chapterfain.com>
 */
class ForumPostType extends AbstractType
{
    private $entityPath;

    public function __construct($tsforum) {
        $this->entityPath = $tsforum['path'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', 'textarea', array('label' => 'Post Content', 'attr' => array('class' => 'post-content', 'data-widearea' => 'enable')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => $this->entityPath . '\Entity\ForumPost',
            ));
    }

    public function getName()
    {
        return 'talis_forum_post';
    }
} 