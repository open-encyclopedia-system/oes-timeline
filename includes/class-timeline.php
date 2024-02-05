<?php

namespace OES\Timeline;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('Timeline')) :

    /**
     * Class Timeline
     *
     * Prepare and display a timeline representing posts.
     */
    class Timeline
    {

        /** @var array The timeline configs for post types. */
        public array $configs = [];

        /** @var array The timeline data. */
        public array $data = [];

        /** @var array Additional parameters. */
        public array $additional = [];


        /**
         * Timeline constructor.
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

            $postType = '';
            $configs = [];

            /* loop through post ids */
            foreach ($ids as $id) {

                $post = get_post($id);
                if ($postType !== $post->post_type) {
                    $postType = $post->post_type;
                    $configs = $this->get_config($postType);
                }

                /* exit early if post not published */
                if ($post->post_status != 'publish') continue;


                /* start date */
                if (!isset($configs['start'])) continue;

                $start = oes_get_field($configs['start'], $id);
                $startTimestamp = strtotime(str_replace('/', '-', $start));
                $end = (isset($configs['end']) ? oes_get_field($configs['end'], $id) : false);

                /* prepare label */
                if (isset($configs['label']) && !empty($configs['label']) && $configs['label'] != 'none' &&
                    $startLabel = oes_get_field($configs['label'], $id)) {
                    $endLabel = 'hidden';
                } else {
                    $startLabel = (isset($configs['start_label']) ? oes_get_field($configs['start_label'], $id) : false);
                    $endLabel = (isset($configs['end_label']) ? oes_get_field($configs['end_label'], $id) : false);
                }


                /**
                 * Filters the start label.
                 *
                 * @param string $startLabel The value.
                 * @param string $start The start value.
                 */
                $startLabel = apply_filters('oes_timeline/start_date', $startLabel, $start);


                /**
                 * Filters the end label.
                 *
                 * @param string $endLabel The value.
                 * @param string $end The end value.
                 */
                $endLabel = apply_filters('oes_timeline/end_date', $endLabel, $end);


                /* prepare name */
                $name = (isset($configs['name']) ? oes_get_field($configs['name'], $id) : '');
                if(empty($name)) $name = oes_get_display_title($id);


                /**
                 * Filters the name.
                 *
                 * @param string $name The name.
                 */
                $name = apply_filters('oes_timeline/name', $name);


                if ($startTimestamp)
                    $this->data[date('Y', $startTimestamp)][$startTimestamp][] = [
                        'start' => $start,
                        'end' => $end,
                        'start_label' => $startLabel,
                        'end_label' => $endLabel,
                        'permalink' => get_permalink($id),
                        'name' => $name,
                        'range' => (!empty($start) && !empty($end))
                    ];
            }
        }


        /**
         * Set config for post type.
         *
         * @param string $post_type The post type.
         * @return array Return config.
         */
        function get_config(string $post_type): array
        {
            global $oes_language;
            $config = get_options($post_type, $oes_language);

            /* add format */
            foreach (['start', 'end'] as $part)
                if (isset($config[$part])) {
                    $field = oes_get_field_object($config[$part]);
                    if (isset($field['return_format']))
                        $config[$part . '_format'] = $field['return_format'];
                }

            return $config;
        }


        /**
         * Get html representation of the timeline.
         *
         * @return string Return the html representation of the timeline.
         */
        function html(): string
        {

            /* store years in global variable */
            global $oes_timeline_years;

            $content = '';
            if (!empty($this->data)) {

                $content = '<div class="oes-timeline-container"><div class="oes-timeline-outer">';

                /* sort data before looping */
                ksort($this->data);
                foreach ($this->data as $year => $yearData) {

                    $oes_timeline_years[] = $year;
                    $content .= '<div class="oes-timeline-year" id="oes_timeline_year_' . $year . '">' . $year . '</div>';

                    ksort($yearData);
                    foreach ($yearData as $date) {
                        foreach ($date as $event) {

                            $label = empty($event['start_label']) ?
                                $event['start'] :
                                (($event['start_label'] == 'hidden') ? '' : $event['start_label']);
                            if ($event['range']) {
                                $endLabel = empty($event['end_label']) ?
                                    $event['end'] :
                                    (($event['end_label'] == 'hidden') ? '' : $event['end_label']);
                                if (!empty($endLabel)) $label .= ' - ' . $endLabel;
                            }


                            /**
                             * Filters the label.
                             *
                             * @param string $label The label.
                             * @param array $event The event.
                             */
                            $label = apply_filters('oes_timeline/label', $label, $event);


                            /**
                             * Filters the name.
                             *
                             * @param string $name The label.
                             * @param array $event The event.
                             */
                            $name = apply_filters('oes_timeline/name', $event['name'], $event);


                            if (!empty($label) && !empty($name))
                                $content .= sprintf('<div class="oes-timeline-event-wrapper">' .
                                    '<div class="oes-timeline-event %s">' .
                                    '<div><span class="oes-timeline-event-title"><a href = "%s">%s</a></span>%s</div>' .
                                    '</div>' .
                                    '</div>',
                                    ($event['range'] ? 'oes-timeline-range' : ''),
                                    $event['permalink'],
                                    $label,
                                    $name
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
