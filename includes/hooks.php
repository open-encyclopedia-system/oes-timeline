<?php

add_action('oes/theme_archive_list', 'oes_timeline_theme_archive_list');
add_action('wp_enqueue_scripts', 'oes_timeline_enqueue_scripts');
add_action('dynamic_sidebar_after', 'oes_timeline_dynamic_sidebar_after');


/**
 * Replace archive list by timeline.
 * @return void
 */
function oes_timeline_theme_archive_list(): void
{

    global $oes_post_type;

    /* check if current post type is to be replaced by timeline */
    $replaceArchive = false;
    $categories = [];
    if($options = get_option('oes_timeline-' . $oes_post_type . '-categories')){
        $categoryArray = json_decode($options, true);
        foreach($categoryArray as $categoryKey => $category){
            $replaceArchive = $category['archive'] ?? false;
            if($replaceArchive) $categories[] = $categoryKey;
        }
    }

    /* replace archive by shortcode */
    global $oes_archive_displayed;
    if($replaceArchive && !empty($categories)) {
        global $oes_timeline_temp; //TODO temporary - why does WordPress try to render this in admin mode?
        $oes_timeline_temp = true;
        $args = ['post_type' => $oes_post_type];
        if(sizeof($categories) > 1) $args['categories'] .= implode(',', $categories);
        echo oes_timeline_HTML($args);
        $oes_archive_displayed = true;
    }
}


/**
 * Add year filter after sidebar.
 *
 * @param string $index The sidebar id.
 * @return void
 */
function oes_timeline_dynamic_sidebar_after(string $index): void
{
    global $oes_timeline_years;
    if($index == 'oes-archive-sidebar' && !empty($oes_timeline_years)) {
        $yearsHTML = '';
        foreach($oes_timeline_years as $year)
            $yearsHTML .= sprintf('<li><a href="#%s" class="oes-index-filter-anchor">%s</a></li>',
                'oes_timeline_year_' . $year,
                $year
            );

        echo '<div class="oes-index-archive-filter-wrapper"><ul class="oes-vertical-list">' . $yearsHTML . '</ul></div>';
    }
}


/**
 * Include timeline assets.
 * @return void
 */
function oes_timeline_enqueue_scripts(): void
{
    $path = plugins_url(basename(__DIR__)) . '/../oes-timeline/assets/';
    wp_register_style('oes-timeline', $path . 'css/oes-timeline.css', [], false, 'screen');
    wp_enqueue_style('oes-timeline');
}