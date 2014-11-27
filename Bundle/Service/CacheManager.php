<?php
/*
 * This file is part of the Egils\CacheBundle package.
 *
 * (c) Egidijus Lukauskas <egils.ps@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Egils\Bundle\CacheBundle\Service;

use Doctrine\Common\Cache\CacheProvider;
use Egils\Component\Cache\Adapter\DoctrineCacheAdapter;
use Egils\Component\Cache\CacheManager as BaseCacheManager;

class CacheManager extends BaseCacheManager
{
    public function addDoctrineCacheProvider($name, CacheProvider $provider)
    {
        $this->addAdapter($name, new DoctrineCacheAdapter($provider));
    }
}
