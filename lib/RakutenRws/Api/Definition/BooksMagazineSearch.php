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
 * BooksMagazineSearch
 *
 * @package RakutenRws
 * @subpackage Api_Definition
 */
class BooksMagazineSearch extends AppRakutenApi
{
    protected
        $autoSetIterator = true,
        $isRequiredAccessToken = false,
        $versionMap = array(
            '2013-05-22' => '20130522',
            '2012-11-28' => '20121128'
        );

    public function getService()
    {
        return 'BooksMagazine';
    }

    public function getOperation()
    {
        return 'Search';
    }
}
