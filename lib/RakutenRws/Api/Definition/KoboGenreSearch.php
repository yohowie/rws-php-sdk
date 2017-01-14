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
 * KoboGenreSearch
 *
 * @package RakutenRws
 * @subpackage Api_Definition
 */
class KoboGenreSearch extends AppRakutenApi
{
    protected
        $isRequiredAccessToken = false,
        $versionMap = array(
            '2013-10-10' => '20131010'
        );

    public function getService()
    {
        return 'Kobo';
    }

    public function getOperation()
    {
        return 'GenreSearch';
    }
}
