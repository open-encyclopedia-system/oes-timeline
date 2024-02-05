<?php

namespace OES\Timeline;


/**
 * Display timeline.
 *
 * @param array $args The timeline arguments, the post ids.
 * @return string The timeline.
 */
function html(array $args = []): string
{
    global $oes_post, $oes_timeline_temp; //TODO temporary - why does WordPress try to render this in admin mode?
    if ($oes_post || $oes_timeline_temp || ($args['force'] ?? false)) {
        if (!isset($args['ids'])) {
            if (!isset($args['post_type'])) return '';
            else $ids = oes_get_wp_query_posts(['post_type' => $args['post_type'], 'fields' => 'ids']);
        } else $ids = is_array($args['ids']) ? $args['ids'] : explode(',', $args['ids']);

        return get_html_representation($ids);
    } else return '';
}


/**
 * Get the timeline representation of posts.
 *
 * @param array $ids The posts.
 * @return string Return the timeline representation.
 */
function get_html_representation(array $ids = []): string
{

    if (!empty($ids)) {
        $timeline = new Timeline($ids);
        return $timeline->html();
    }
    return '';
}


/**
 * Get timeline options for post type.
 *
 * @param string $post_type The post type.
 * @param string $language The considered language.
 * @return array|mixed|void Return the timeline options for this post type.
 */
function get_options(string $post_type, string $language = 'language0')
{

    $optionJSON = get_option('oes_timeline-' . $post_type);
    $options = $optionJSON ? json_decode($optionJSON, true) : [];

    /* modify for non-primary language */
    if ($language != 'language0') {
        foreach ($options as $option => $field)
            if (!in_array($option, ['title', 'archive'])) {
                if (get_field_object($field . '_' . $language))
                    $options[$option] = $field . '_' . $language;
            }
    }


    /**
     * Filter the OES timeline options.
     *
     * @param array $options The OES timeline options.
     * @param string $post_type The post type.
     */
    return apply_filters('oes_timeline/options', $options, $post_type);
}


/**
 * Create list of jump anchors for timeline.
 *
 * @param array $args Additional parameters. Valid parameters are:
 *  'steps'     The steps in between jump anchors, e.g. 10 = 1920, 1930, 1940,...; 100 = 1900, 2000,...
 * @return string Return list of jump anchors,
 */
function anchors_html(array $args = []): string
{

    global $oes_timeline_years;
    if (!empty($oes_timeline_years)) {
        $yearsHTML = '';
        $storedYears = [];
        foreach ($oes_timeline_years as $year) {

            $displayedYear = $year;
            if (isset($args['steps']) && $args['steps'])
                $displayedYear = floor($year / $args['steps']) * $args['steps'];

            if (!in_array($displayedYear, $storedYears)) {
                $yearsHTML .= sprintf('<li><a href="#%s" class="oes-index-filter-anchor">%s</a></li>',
                    'oes_timeline_year_' . $year,
                    $displayedYear
                );
                $storedYears[] = $displayedYear;
            }
        }

        return '<div class="oes-index-archive-filter-wrapper"><ul class="oes-vertical-list">' .
            $yearsHTML .
            '</ul></div>';
    }
    return '';
}


/**
 * Replace archive list by timeline.
 * @return void
 */
function theme_archive_list(): void
{

    global $post_type;

    /* check if current post type is to be replaced by timeline */
    $replaceArchive = false;
    if ($option = get_option('oes_timeline-' . $post_type)) {
        $configs = json_decode($option, true);
        $replaceArchive = $configs['archive'] ?? false;
    }

    /* replace archive by shortcode */
    global $oes_archive_displayed;
    if ($replaceArchive) {
        global $oes_timeline_temp; //TODO temporary - why does WordPress try to render this in admin mode?
        $oes_timeline_temp = true;
        $args = ['post_type' => $post_type];
        echo html($args);
        $oes_archive_displayed = true;
    }
}


/**
 * Include timeline assets.
 * @return void
 */
function enqueue_scripts(): void
{
    $path = plugins_url(basename(__DIR__)) . '/../oes-timeline/assets/';
    wp_register_style('oes-timeline', $path . '/timeline.css');
    wp_enqueue_style('oes-timeline');
}


//TODO
function get_post_timeline_html($args = []): string
{

    global $oes_post;
    if (is_single() && $oes_post) {

        $options = get_option('oes_timeline-event_field-' . $oes_post->post_type);
        if($options && is_array($options)) {

            $ids = [];
            foreach ($options as $option) {
                $fieldValue = oes_get_field($option, $oes_post->object_ID);
                if(!empty($fieldValue) && is_array($fieldValue))
                    $ids = array_merge($fieldValue, $ids); //TODO parent fields...?
            }
            if (!empty($ids)) {

                /* prepare header */
                $header = isset($args['labels']) ? oes_language_label_html($args['labels']) : '';
                $content = html(['ids' => $ids]);

                if (!empty($content)) {

                    $content = '<div class="oes-timeline-wrapper">' . $content . '</div>';
                    if ($args['detail']) return '<div class="oes-timeline-container">' .
                        oes_get_details_block(
                            $header,
                            $content
                        ) .
                        '</div>';

                    return '<div class="oes-timeline-container">' .
                        (empty($header) ? '' : '<h5>' . $header . '</h5>') .
                        $content .
                        '</div>';
                }
            }
        }
    }

    return '';
}