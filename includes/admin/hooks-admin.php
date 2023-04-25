<?php

add_filter('oes/admin_menu_pages', 'oes_timeline_admin_menu_pages');


/**
 * Add timeline settings page.
 * 
 * @param array $pages The admin menu pages.
 * @return array Return the modified admin menu pages.
 */
function oes_timeline_admin_menu_pages(array $pages): array
{
    $pages['086_timeline'] = [
        'sub_page' => true,
        'page_parameters' => [
            'page_title' => 'Timeline',
            'menu_title' => 'Timeline',
            'menu_slug' => 'oes_timeline',
            'position' => 11
        ],
        'view_file_name_full_path' => (__DIR__ . '/views/view-settings-timeline.php')
    ];
    return $pages;
}