<?php

namespace OES\Timeline;

/**
 * Output timeline CSS custom properties in the document head.
 *
 * @return void
 */
function wp_head(): void
{
    global $oes;

    // Define default values based on theme type
    $defaults = $oes->block_theme
        ? [
            'font-size'  => 'var(--wp--preset--font-size--small)',
            'color'      => 'var(--wp--preset--color--text)',
            'color2'     => 'var(--wp--preset--color--inactive)',
            'background' => 'var(--wp--preset--color--background)',
            'year'       => 'var(--wp--preset--color--year)',
        ]
        : [
            'font-size'  => '16px',
            'color'      => 'var(--oes-text-black)',
            'color2'     => 'var(--oes-text-black)',
            'background' => 'var(--oes-text-black)',
            'year'       => 'var(--oes-text-black)',
        ];

    // Fetch saved custom colours (if any)
    $colors = json_decode(get_option('oes_timeline', ''), true);
    $colors = is_array($colors) ? $colors : [];

    // Build CSS custom properties
    $colorStyle = '';
    foreach ($defaults as $id => $defaultValue) {
        $value = isset($colors[$id]) ? $colors[$id] : $defaultValue;
        $colorStyle .= '--oes-timeline-' . $id . ': ' . $value . ';';
    }

    // Output style block
    echo '<style type="text/css" id="oes-timeline-colors">';
    echo ':root {' . $colorStyle . '}';
    echo '</style>';
}

/**
 * Render the timeline HTML for a set of posts.
 *
 * Accepts either a list of post IDs or a post type (to query IDs).
 * Will only render if context conditions are met (global flags or 'force' flag).
 *
 * @param array $args {
 *     Optional. Arguments to control timeline rendering.
 *
 *     @type array|string $ids       List of post IDs or comma-separated string.
 *     @type string       $post_type Optional. Post type to query IDs from.
 *     @type bool         $force     Optional. Force render regardless of context. Default false.
 * }
 * @return string Timeline HTML if allowed and data exists, otherwise an empty string.
 */
function html(array $args = []): string
{
    global $oes_post, $oes_timeline_temp;

    if (is_admin() && empty($args['force'])) {
        return '';
    }

    if (!$oes_post && !$oes_timeline_temp && empty($args['force'])) {
        return '';
    }

    // Resolve IDs
    if (!empty($args['ids'])) {
        $raw = is_array($args['ids']) ? $args['ids'] : explode(',', $args['ids']);
        $ids = array_map(function ($item) {
            return $item instanceof \WP_Post ? $item->ID : (int) $item;
        }, $raw);
        $ids = array_filter($ids); // Remove falsy values (e.g., 0)
    } elseif (!empty($args['post_type'])) {
        $ids = oes_get_wp_query_posts([
            'post_type' => $args['post_type'],
            'fields' => 'ids',
        ]);
    } else {
        return ''; // No valid input source
    }

    $additionalArgs = $args;
    unset($additionalArgs['ids']);

    return !empty($ids) ? get_html_representation($ids, $additionalArgs) : '';
}

/**
 * Get the timeline HTML representation for a list of post IDs.
 *
 * Resolves a project-specific Timeline class dynamically and delegates rendering.
 *
 * @param array $ids Array of WordPress post IDs.
 * @param array $args Array of additional arguments.
 * @return string HTML timeline output, or empty string if input is invalid.
 */
function get_html_representation(array $ids = [], array $args = []): string
{
    $ids = array_filter(array_map('intval', $ids));
    if (empty($ids)) {
        return '';
    }

    $class = oes_get_project_class_name('\OES\Timeline\Timeline', 'OES_Timeline');
    $timeline = new $class($ids, $args);
    return $timeline->html();
}

/**
 * Get timeline options for a post type.
 *
 * @param string $post_type The post type.
 * @param string $language The considered language. Defaults to 'language0' (primary).
 * @return array The timeline options for this post type.
 */
function get_options(string $post_type, string $language = 'language0'): array
{
    // Retrieve and decode the stored options
    $optionJSON = get_option('oes_timeline-' . $post_type);
    $options = is_string($optionJSON) ? json_decode($optionJSON, true) : [];

    // Ensure we return an array even if decode fails
    if (!is_array($options)) {
        $options = [];
    }

    // Adjust for non-primary languages
    if ($language !== 'language0') {
        foreach ($options as $option => $field) {
            if (!in_array($option, ['title', 'archive'], true)) {
                $translated_field = $field . '_' . $language;
                if (get_field_object($translated_field)) {
                    $options[$option] = $translated_field;
                }
            }
        }
    }

    /**
     * Filter the OES timeline options.
     *
     * @param array  $options   The timeline options array.
     * @param string $post_type The post type.
     */
    return apply_filters('oes_timeline/options', $options, $post_type);
}

/**
 * Create a list of jump anchors for the timeline, based on the available years.
 *
 * @param array $args {
 *     Optional. Arguments to customise anchor behaviour.
 *
 *     @type int $steps Interval between jump anchors (e.g. 10 = 1920, 1930, ...; 100 = 1900, 2000, ...).
 * }
 * @return string HTML list of jump anchors.
 */
function anchors_html(array $args = []): string
{
    global $oes_timeline_years;

    if (empty($oes_timeline_years)) {
        return '';
    }

    $yearsHTML = '';
    $storedYears = [];

    foreach ($oes_timeline_years as $year) {
        $displayedYear = isset($args['steps']) && $args['steps']
            ? floor($year / $args['steps']) * $args['steps']
            : $year;

        if (!in_array($displayedYear, $storedYears, true)) {
            $yearsHTML .= sprintf(
                '<li><a href="#%s" class="oes-index-filter-anchor">%s</a></li>',
                esc_attr('oes_timeline_year_' . $year),
                esc_html($displayedYear)
            );
            $storedYears[] = $displayedYear;
        }
    }

    return '<div class="oes-index-archive-filter-wrapper"><ul class="oes-vertical-list">' .
        $yearsHTML .
        '</ul></div>';
}

/**
 * Replace the default archive list with a timeline if enabled for the current post type.
 *
 * @return void
 */
function theme_archive_list(): void
{
    global $post_type;

    // Bail early if post type is not set
    if (empty($post_type)) {
        return;
    }

    // Check if timeline is enabled for the current post type
    $replaceArchive = false;
    $option = get_option('oes_timeline-' . $post_type);
    if ($option) {
        $configs = json_decode($option, true);
        if (is_array($configs)) {
            $replaceArchive = !empty($configs['archive']);
        }
    }

    // If enabled, replace archive with timeline display
    if ($replaceArchive) {
        global $oes_archive_displayed, $oes_timeline_temp;

        // @oesDevelopment: temporary workaround to avoid admin rendering issues
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
    wp_register_style('oes-timeline', OES_TIMELINE_PLUGIN_URL . '/assets/timeline.css');
    wp_enqueue_style('oes-timeline');
}

/**
 * Get the HTML representation of a timeline for a single post.
 *
 * @param array $args {
 *     Optional. Additional arguments.
 *
 *     @type array  $labels Optional label array for the header.
 *     @type bool   $detail Whether to wrap in a details block.
 * }
 * @return string HTML representation of the timeline, or an empty string if not applicable.
 */
function get_post_timeline_html(array $args = []): string
{
    global $oes_post;

    // Ensure we're in a single post view with a valid post object
    if (!is_single() || empty($oes_post) || empty($oes_post->post_type)) {
        return '';
    }

    // Get timeline-related fields configured for this post type
    $options = get_option('oes_timeline-event_field-' . $oes_post->post_type);
    if (empty($options) || !is_array($options)) {
        return '';
    }

    // Collect related post IDs from the specified fields
    $ids = [];
    foreach ($options as $option) {
        $fieldValue = oes_get_field($option, $oes_post->object_ID);
        if (!empty($fieldValue) && is_array($fieldValue)) {
            $ids = array_merge($ids, $fieldValue);
        }
    }

    if (empty($ids)) {
        return '';
    }

    // Generate timeline content
    $header = !empty($args['labels']) ? oes_language_label_html($args['labels']) : '';
    $content = html(['ids' => $ids]);

    if (empty($content)) {
        return '';
    }

    $content = '<div class="oes-timeline-wrapper">' . $content . '</div>';

    if (!empty($args['detail'])) {
        return '<div class="oes-timeline-container">' .
            oes_get_details_block($header, $content) .
            '</div>';
    }

    return '<div class="oes-timeline-container oes-timeline-single-post">' .
        (!empty($header) ? '<h5>' . esc_html($header) . '</h5>' : '') .
        $content .
        '</div>';
}
