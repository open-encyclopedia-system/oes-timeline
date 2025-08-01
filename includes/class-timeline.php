<?php

namespace OES\Timeline;

if (!defined('ABSPATH')) exit;

if (!class_exists('Timeline')) :

    /**
     * Class Timeline
     *
     * Prepares and renders a timeline of WordPress posts.
     */
    class Timeline
    {
        /** @var array Configuration per post type. */
        public array $configs = [];

        /** @var array Timeline data grouped by year and timestamp. */
        public array $data = [];

        /** @var array Additional arguments passed to the constructor. */
        public array $additional = [];

        /** @var string Fully-qualified class name used for individual timeline events. */
        public string $event_class = '\OES\Timeline\Event';

        /**
         * Timeline constructor.
         *
         * @param array $ids  Array of post IDs.
         * @param array $args Optional parameters for customization.
         */
        public function __construct(array $ids = [], array $args = [])
        {
            $this->set_additional_parameters($args);
            $this->set_event_class();
            $this->prepare_data($ids);
        }

        /**
         * Store additional constructor parameters.
         */
        protected function set_additional_parameters(array $args): void
        {
            $this->additional = $args;
        }

        /**
         * Dynamically resolve the event class for rendering timeline entries.
         */
        protected function set_event_class(): void
        {
            $this->event_class = oes_get_project_class_name('\OES\Timeline\Event');
        }

        /**
         * Prepare timeline entries based on post IDs.
         *
         * @param array $ids Post IDs or WP_Post objects.
         */
        protected function prepare_data(array $ids = []): void
        {
            $lastPostType = null;
            $configs = [];

            foreach ($ids as $postInput) {
                $post = $postInput instanceof \WP_Post ? $postInput : get_post($postInput);
                if (!$post || $post->post_status !== 'publish') {
                    continue;
                }

                // Load config only when post type changes
                if ($lastPostType !== $post->post_type) {
                    $lastPostType = $post->post_type;
                    $configs = $this->get_config($lastPostType);
                }

                if (empty($configs['start'])) {
                    continue;
                }

                $eventArgs = $this->prepare_event_args($post->ID);
                $event = new $this->event_class($post->ID, $configs, $eventArgs);
                $timestamp = $event->timestamp;

                if ($timestamp) {
                    $year = date('Y', $timestamp);
                    $this->data[$year][$timestamp][] = $event;
                }
            }
        }

        /**
         * Get configuration for a post type.
         */
        protected function get_config(string $postType): array
        {
            if (isset($this->configs[$postType])) {
                return $this->configs[$postType];
            }

            global $oes_language;
            $config = get_options($postType, $oes_language);

            // Add return format info for date fields
            foreach (['start', 'end'] as $fieldKey) {
                if (!empty($config[$fieldKey])) {
                    $field = oes_get_field_object($config[$fieldKey]);
                    if (!empty($field['return_format'])) {
                        $config["{$fieldKey}_format"] = $field['return_format'];
                    }
                }
            }

            $this->configs[$postType] = $config;
            return $config;
        }

        /**
         * Prepare arguments passed to the event class.
         * @param int $postID
         * @return array
         */
        protected function prepare_event_args(int $postID): array
        {
            return [];
        }

        /**
         * Render the timeline as HTML.
         */
        public function html(): string
        {
            global $oes_timeline_years;

            if (empty($this->data)) {
                return '';
            }

            $content = '<div class="oes-timeline-container"><div class="oes-timeline-outer">';

            ksort($this->data); // Sort years
            foreach ($this->data as $year => $entries) {
                $oes_timeline_years[] = $year;
                $content .= '<div class="oes-timeline-year" id="oes_timeline_year_' . esc_attr($year) . '">' .
                    esc_html($year) .
                    '</div>';

                ksort($entries); // Sort timestamps
                foreach ($entries as $events) {
                    foreach ($events as $event) {
                        $content .= $event->get_event_HTML();
                    }
                }
            }

            $content .= '</div></div>';
            return $content;
        }
    }

endif;
