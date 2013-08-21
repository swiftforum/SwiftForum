SwiftForum
========================

SwiftForum is still in early development. Detailed instructions on how to get it running will be added soon.

For now, this readme will be used for developer information.

License
-----------------------
This project uses the MIT license, which can be found under Resources/meta/LICENSE

To-Do
------------------------

**Create the actual forum components in this order**:
* Forum Categories ( in progress )
* Forum Boards
* Forum Topics
* Forum Posts

* Create user profile pages
* Create support for signatures and Gravatars
* Add Admin Features
* Boost the performance using ESI, Query Caches etc
* Add Unit testing support
* Make the bundle more portable and easier to include in projects

I know Unit Testing is supposed to be set up at the start of the project, but unfortunately time limitations prevented this.
I would really like to add it as soon as time allows.

Developer Reference:
-------------------------
Here I will outline some guides on how to achieve things, both for my own reference as well as for contributers.

### Controller Structure:
**AdminController**:
Lets users with authority perform administrative tasks on both forums as well as users. ( Route prefix: "/admin", route example: "/admin/forum/categories", "/admin/members" etc )

**AuthController**:
Handles account tasks. Logging in, out, verifying your email, registering a new account and editing your account. ( Route prefix: "/", route example: "/editprofile", "/register" etc )

**BaseController**:
Extends the base symfony controller to make some methods available to all other controllers.

**BasicController**:
Handles basic tasks, such as displaying the list of icons. ( Route prefix: "/", route example: "/icons" )

**ForumController**:
Display board structure, display posts etc. ( Route prefix: "/forum", route example: "/forum/" )

**HomeController**:
Displays the homepage. ( Route prefix: "/", route example: "/" )

### Caching with Symfony:

** Caching Doctrine result sets **:
Example:

Create Entity/Icons:

    /**
    * @ORM\Table(name="icons")
    * @ORM\Entity(repositoryClass="Acme\CachingBundle\Entity\IconsRepository")
    */
    class Icons
    {
        // Stuff
    }


Create Entity/IconsRepository:

    use Doctrine\ORM\EntityRepository;

    class IconsRepository extends EntityRepository
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.id', 'asc')
            ->getQuery()
            ->useQueryCache(true)
            ->setResultCacheId('icons')
            ->getResult();
    }

Note the useQueryCache and setResultCacheId('cachename').

To invalidate the result cache ( call everytime the data is modified ):

    $this->getDoctrine()->getManager()->getConfiguration()->getResultCacheImpl()->delete('icons');

To load from cache:

    if($this->getDoctrine()->getManager()->getConfiguration()->getResultCacheImpl()->contains('icons')) {
        $icons = $this->getDoctrine()->getManager()->getConfiguration()->getResultCacheImpl()->fetch('icons');
        $logger->info('Cache hit');
    } else {
        $icons = $em->getRepository('AcmeCachingBundle:Icons')
            ->getIcons();
        $logger->info('Cache was not hit');
    }

To delete all cache entries:

    $cacheDriver = $entityManager->getConfiguration()->getResultCacheImpl();
    $cacheDriver->deleteAll();

Caching of a site:
Set cache settings in one call

    $response->setCache(array(
        'etag'          => $etag,
        'last_modified' => $date,
        'max_age'       => 10,
        's_maxage'      => 10,
        'public'        => true,
        'private'    => true,
    ));

    if($response->isNotModified($this->getRequest())) {
        $logger->err('Not modified!');
    }


Marks the Response stale

    $response->expire();

Force the response to return a proper 304 response with no content

    $response->setNotModified();

