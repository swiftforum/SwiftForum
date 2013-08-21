<?php

namespace Talis\TrickPlayBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TalisTrickPlayBundle extends Bundle
{
    public function getParent()
    {
        return 'TalisSwiftForumBundle';
    }
}
