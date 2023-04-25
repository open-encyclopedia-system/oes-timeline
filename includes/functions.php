<?php

use OES\OES_Timeline;

add_shortcode('oes_timeline', 'oes_timeline_HTML');
add_shortcode('oes_timeline_anchors', 'oes_timeline_anchors_HTML');


/**
 * Display timeline.
 *
 * @param array $args The timeline arguments, the post ids.
 * @return string The timeline.
 */
function oes_timeline_HTML(array $args = []): string
{
    global $oes_post, $oes_timeline_temp; //TODO temporary - why does WordPress try to render this in admin mode?
    if($oes_post || $oes_timeline_temp) {
        if (!isset($args['ids'])) {
            if (!isset($args['post_type'])) return '';
            else $ids = oes_get_wp_query_posts(['post_type' => $args['post_type'], 'fields' => 'ids']);
        } else $ids = is_array($args['ids']) ? $args['ids'] : explode(',', $args['ids']);

        return oes_timeline_get_HTML_representation($ids);
    }
    else return '';
}


/**
 * Get the timeline representation of posts.
 *
 * @param array $ids The posts.
 * @return string Return the timeline representation.
 */
function oes_timeline_get_HTML_representation(array $ids = []): string {

    if(!empty($ids)){
        $timeline = new OES_Timeline($ids);
        return $timeline->html();
    }
    return '';
}


/**
 * Get timeline categories for post type.
 *
 * @param string $post_type The post type.
 * @param string $language The considered language.
 * @return array|mixed|void Return the timeline categories for this post type.
 */
function oes_timeline_get_categories(string $post_type, string $language = 'language0'){

    $option = get_option('oes_timeline-' . $post_type);
    $configs = get_option('oes_timeline-' . $post_type . '-categories');

    $categories = [];
    if ($option && $option > 0)
        $categories = $configs ? json_decode($configs, true) : [];

    /* modify for non-primary language */
    if($language != 'language0'){
        foreach($categories as $categoryKey => $category){
            foreach($category as $option => $field)
                if(!in_array($option, ['title', 'archive'])){
                    if(get_field_object($field . '_' . $language))
                        $categories[$categoryKey][$option] = $field . '_' . $language;
                }
        }
    }


    /**
     * Filter the OES timeline categories.
     *
     * @param array $categories The OES timeline categories.
     * @param string $post_type The post type.
     */
    if (has_filter('oes_timeline/categories'))
        $categories = apply_filters('oes_timeline/categories', $categories, $post_type);

    return $categories;
}


/**
 * Create list of jump anchors for timeline.
 *
 * @param array $args Additional parameters. Valid parameters are:
 *  'steps'     The steps in between jump anchors, e.g. 10 = 1920, 1930, 1940,...; 100 = 1900, 2000,...
 * @return string Return list of jump anchors,
 */
function oes_timeline_anchors_HTML(array $args = []) : string {

    global $oes_timeline_years;
    if(!empty($oes_timeline_years)) {
        $yearsHTML = '';
        $storedYears = [];
        foreach($oes_timeline_years as $year) {

            $displayedYear = $year;
            if(isset($args['steps']) && $args['steps'])
                $displayedYear = floor($year/$args['steps']) * $args['steps'];

            if(!in_array($displayedYear, $storedYears)) {
                $yearsHTML .= sprintf('<li><a href="#%s" class="oes-index-filter-anchor">%s</a></li>',
                    'oes_timeline_year_' . $year,
                    $displayedYear
                );
                $storedYears[] = $displayedYear;
            }
        }

        return '<div class="oes-index-archive-filter-wrapper"><ul class="oes-vertical-list">' . $yearsHTML . '</ul></div>';
    }
    return '';
}