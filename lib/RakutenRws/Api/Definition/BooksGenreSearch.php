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
 * BooksGenreSearch
 *
 * @package RakutenRws
 * @subpackage Api_Definition
 */
class BooksGenreSearch extends AppRakutenApi
{
    protected
        $isRequiredAccessToken = false,
        $versionMap = array(
            '2012-11-28' => '20121128'
        );

    public function getService()
    {
        return 'BooksGenre';
    }

    public function getOperation()
    {
        return 'Search';
    }
}
