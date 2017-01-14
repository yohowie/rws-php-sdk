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
 * BooksSoftwareSearch
 *
 * @package RakutenRws
 * @subpackage Api_Definition
 */
class BooksSoftwareSearch extends AppRakutenApi
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
        return 'BooksSoftware';
    }

    public function getOperation()
    {
        return 'Search';
    }
}
