<?php

namespace RakutenRws\Api\Definition;

use RakutenRws\Api\AppRakutenApi;

/**
 * This file is part of Rakuten Web Service SDK
 *
 * (c) Rakuten, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with source code.
 */

/**
 * ProductSearch
 *
 * @package RakutenRws
 * @subpackage Api_Definition
 */
class ProductSearch extends AppRakutenApi
{
    protected
        $autoSetIterator = true,
        $arrayName = 'Products',
        $entityName = 'Product',
        $isRequiredAccessToken = false,
        $versionMap = array(
            '2014-03-05' => '20140305',
        );

    public function getService()
    {
        return 'Product';
    }

    public function getOperation()
    {
        return 'Search';
    }
}
