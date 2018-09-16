<?php
return array(
    'pm'     => array(
        'user' => array(
            'token'        => array(
                'key'     => '51jiyou_pm_user_token_',
                'expires' => 60 * 30,
            ),
            'permissions'  => array(
                'key'     => '51jiyou_pm_user_permissions_',
                'expires' => 60 * 30,
            ),
            'pageElements' => array(
                'key'     => '51jiyou_pm_user_pageElements_',
                'expires' => 60 * 30,
            ),
            'user'         => array(
                'key'     => '51jiyou_pm_user_',
                'expires' => 60 * 30,
            ),
        ),
    ),
    'region' => array(
        'provinces' => array(
            'key'     => '51jiyou_region_provinces',
            'expires' => 60 * 60 * 24 * 30,
        ),
        'cities'    => array(
            'key'     => '51jiyou_region_cities_',
            'expires' => 60 * 60 * 24 * 30,
        ),
        'districts' => array(
            'key'     => '51jiyou_region_districts_',
            'expires' => 60 * 60 * 24 * 30,
        ),
    ),
);
