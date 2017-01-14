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
 * AuctionItemCodeSearch
 *
 * @package RakutenRws
 * @subpackage Api_Definition
 */
class AuctionItemCodeSearch extends AppRakutenApi
{
    protected
        $isRequiredAccessToken = false,
        $versionMap = array(
            '2012-10-10' => '20121010'
        );

    public function getService()
    {
        return 'AuctionItemCode';
    }

    public function getOperation()
    {
        return 'Search';
    }
}
