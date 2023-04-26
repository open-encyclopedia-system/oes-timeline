<?php

namespace OES;

use function OES\ACF\oes_get_field;
use function OES\ACF\oes_get_field_object;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('OES_Timeline')) :

    /**
     * Class OES_Timeline
     *
     * Prepare and display a timeline representing posts.
     */
    class OES_Timeline
    {

        /** @var array The timeline categories for post types. */
        public array $categories = [];

        /** @var array The timeline data. */
        public array $data = [];

        /** @var array Additional parameters. */
        public array $additional = [];


        /**
         * OES_Timeline constructor.
         *
         * @param array $ids The post ids.
         * @param array $additional_args Additional arguments.
         */
        function __construct(array $ids = [], array $additional_args = [])
        {
            foreach ($additional_args as $key => $value) $this->additional[$key] = $value;
            $this->prepare_data($ids);
        }


        /**
         * Prepare the timeline data.
         *
         * @param array $ids The post ids.
         * @return void
         */
        function prepare_data(array $ids = []): void
        {
            /* loop through post ids */
            foreach ($ids as $id) {

                $post = get_post($id);
                $postType = $post->post_type;

                /* exit early if post not published */
                if ($post->post_status != 'publish') continue;

                if (!isset($this->categories[$postType])) $this->set_categories($postType);

                /* loop through categories */
                foreach ($this->categories[$postType] as $categoryID => $category) {

                    /* start date */
                    if (!isset($category['start'])) continue;

                    $start = oes_get_field($category['start'], $id);
                    $startTimestamp =  strtotime(str_replace('/', '-', $start)); //TODO @nextRelease: validate timestamp
                    $end = (isset($category['end']) ? oes_get_field($category['end'], $id) : false);

                    /* prepare label */
                    $startLabel = (isset($category['start_label']) ? oes_get_field($category['start_label'], $id) : false);
                    $endLabel = (isset($category['end_label']) ? oes_get_field($category['end_label'], $id) : false);

                    /* prepare name */
                    $name = (isset($category['name']) ? oes_get_field($category['name'], $id) : false);


                    /**
                     * Filters the start label.
                     *
                     * @param string $startLabel The value.
                     * @param string $start The start value.
                     * @param string $categoryID The category ID.
                     */
                    if (has_filter('oes_timeline/start_date'))
                        $startLabel = apply_filters('oes_timeline/start_date', $startLabel, $start, $categoryID);


                    /**
                     * Filters the end label.
                     *
                     * @param string $endLabel The value.
                     * @param string $end The end value.
                     * @param string $categoryID The category ID.
                     */
                    if (has_filter('oes_timeline/end_date'))
                        $endLabel = apply_filters('oes_timeline/end_date', $endLabel, $end, $categoryID);


                    /**
                     * Filters the name.
                     *
                     * @param string $name The name.
                     * @param string $categoryID The category ID.
                     */
                    if (has_filter('oes_timeline/name'))
                        $name = apply_filters('oes_timeline/end_date', $name, $categoryID);


                    $this->data[date('Y', $startTimestamp)][$startTimestamp][] = [
                        'start' => $start,
                        'end' => $end,
                        'start_label' => $startLabel,
                        'end_label' => $endLabel,
                        'permalink' => get_permalink($id),
                        'name' => $name,
                        'range' => (!empty($start) && !empty($end)),
                        'category' => $categoryID
                    ];
                }
            }
        }


        /**
         * Set categories for post type.
         *
         * @param string $post_type The post type.
         * @return void
         */
        function set_categories(string $post_type): void
        {
            global $oes_language;
            $categories = oes_timeline_get_categories($post_type, $oes_language);
            $this->categories[$post_type] = $categories;

            /* add format */
            foreach ($categories as $categoryID => $category) {
                foreach (['start', 'end'] as $part)
                    if (isset($category[$part])) {
                        $field = oes_get_field_object($category[$part]);
                        if (isset($field['return_format']))
                            $this->categories[$post_type][$categoryID][$part . '_format'] = $field['return_format'];
                    }
            }
        }


        /**
         * Get html representation of the timeline.
         *
         * @return string Return the html representation of the timeline.
         */
        function html(): string {

            /* store years in global variable */
            global $oes_timeline_years;

            $content = '';
            if(!empty($this->data)) {

                $content = '<div class="oes-timeline-container"><div class="oes-timeline-outer">';

                /* sort data before looping */
                ksort($this->data);
                foreach($this->data as $year => $yearData){

                    $oes_timeline_years[] = $year;
                    $content .= '<div class="oes-timeline-year" id="oes_timeline_year_' . $year . '">' . $year . '</div>';
                    
                    ksort($yearData);
                    foreach($yearData as $date) {
                        foreach ($date as $event) {

                            $label = empty($event['start_label']) ?
                                $event['start'] :
                                (($event['start_label'] == 'hidden') ? '' : $event['start_label']);
                            if($event['range']) {
                                $endLabel = empty($event['end_label']) ?
                                    $event['end'] :
                                    (($event['end_label'] == 'hidden') ? '' : $event['end_label']);
                                if(!empty($endLabel)) $label .= ' - ' . $endLabel;
                            }


                            /**
                             * Filters the label.
                             *
                             * @param string $label The label.
                             * @param array $event The event.
                             */
                            if (has_filter('oes_timeline/label'))
                                $label = apply_filters('oes_timeline/label', $label, $event);


                            $content .= sprintf('<div class="oes-timeline-event-wrapper"><div class="oes-timeline-event %s">' .
                                '<p><span class="oes-timeline-event-title"><a href = "%s">%s</a></span>%s</p>' .
                                '</div></div>',
                                ($event['range'] ? 'oes-timeline-range' : ''),
                                $event['permalink'],
                                $label,
                                $event['name']
                            );
                        }
                    }
                }
                $content .= '</div></div>';
            }

            return $content;
        }
    }
endif;
