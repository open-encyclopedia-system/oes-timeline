<?php

namespace OES\Timeline;

if (!defined('ABSPATH')) exit;

if (!class_exists('Event')) :

    /**
     * Class Event
     *
     * Represents a timeline event derived from a WordPress post.
     *
     * @package OES\Timeline
     */
    class Event
    {
        /** @var string WordPress post ID representing the timeline event. */
        public string $event_ID;

        /** @var string URL to the post. */
        public string $permalink = '';

        /** @var string Title or label of the event. */
        public string $name = '';

        /** @var int Timestamp derived from the event start date. */
        public int $timestamp = 0;

        /** @var string Raw start date value. */
        public string $start = '';

        /** @var string Raw end date value. */
        public string $end = '';

        /** @var string Label for the start date, formatted. */
        public string $start_label = '';

        /** @var string Label for the end date, formatted. */
        public string $end_label = '';

        /** @var string Final label combining start/end label. */
        public string $label = '';

        /** @var bool Whether the event represents a date range. */
        public bool $range_flag = false;

        /** @var array Field configuration for this post type. */
        public array $configs = [];

        /**
         * Constructor for the Event class.
         *
         * @param string $id      The WordPress post ID.
         * @param array  $configs Field configuration for this event's post type.
         * @param array  $args    Additional arguments (optional).
         */
        public function __construct(string $id, array $configs, array $args = [])
        {
            $this->event_ID = $id;
            $this->configs = $configs;

            $this->set_additional_args($args);
            $this->set_start();
            $this->set_end();
            $this->set_timestamp();
            $this->set_permalink();
            $this->set_range_flag();
            $this->set_name();
            $this->set_labels();
            $this->additional_action();
        }

        /**
         * Additional args (implemented in child classes)
         * @param array $args
         * @return void
         */
        protected function set_additional_args(array $args = []): void
        {
        }

        /**
         * Set the event's start value from field.
         */
        protected function set_start(): void
        {
            $this->start = $this->get_field('start');
        }

        /**
         * Set the event's end value from field.
         */
        protected function set_end(): void
        {
            $this->end = $this->get_field('end');
        }

        /**
         * Converts the start date into a UNIX timestamp.
         */
        protected function set_timestamp(): void
        {
            $this->timestamp = strtotime(str_replace('/', '-', $this->start));
        }

        /**
         * Sets the permalink for the post.
         */
        protected function set_permalink(): void
        {
            $this->permalink = get_permalink($this->event_ID);
        }

        /**
         * Determines whether the event is a range.
         */
        protected function set_range_flag(): void
        {
            $this->range_flag = (!empty($this->start) && !empty($this->end));
        }

        /**
         * Sets the name of the event, either from a configured field or the post title.
         */
        protected function set_name(): void
        {
            $name = $this->get_field('name');
            if (empty($name)) {
                $name = oes_get_display_title($this->event_ID);
            }

            /**
             * Filters the name of the timeline event.
             *
             * @param string $name Event name.
             */
            $this->name = apply_filters('oes_timeline/name', $name);
        }

        /**
         * Sets the label values (start, end, full) for display.
         */
        protected function set_labels(): void
        {
            $startLabel = $this->get_field('label');
            $endLabel = $this->get_field('label');

            $this->set_start_label($startLabel);
            $this->set_end_label(empty($startLabel) ? $endLabel : 'hidden');

            $this->label = $this->generate_label();
        }

        /**
         * Set the start label
         * @param string $startLabel
         * @return void
         */
        protected function set_start_label(string $startLabel): void
        {
            $this->start_label = apply_filters('oes_timeline/start_date', $startLabel, $this->start);
        }

        /**
         * Set the end label
         * @param string $endLabel
         * @return void
         */
        protected function set_end_label(string $endLabel): void
        {
            $this->end_label = apply_filters('oes_timeline/end_date', $endLabel, $this->end);
        }

        /**
         * Additional action (implemented in child classes)
         * @return void
         */
        protected function additional_action(): void
        {
        }

        /**
         * Returns the display label for this event.
         *
         * @return string The generated label.
         */
        public function generate_label(): string
        {
            $label = $this->start_label === 'hidden' ? '' : $this->start_label;

            if ($this->range_flag) {
                $endLabel = $this->end_label === 'hidden' ? '' : $this->end_label;
                if (!empty($endLabel)) {
                    $label .= ' - ' . $endLabel;
                }
            }

            /**
             * Filters the final timeline label.
             *
             * @param string $label The combined label.
             * @param Event  $event The event object.
             */
            return apply_filters('oes_timeline/label', $label, $this);
        }

        /**
         * Retrieves a configured field.
         *
         * @param string $configKey The key in the config array.
         * @param mixed  $default   Default value if field not found or disabled.
         * @return string
         */
        protected function get_field(string $configKey, $default = ''): string
        {
            if (!isset($this->configs[$configKey]) || $this->configs[$configKey] === 'none') {
                return $default;
            }

            return (string) oes_get_field($this->configs[$configKey], $this->event_ID);
        }

        /**
         * Constructs the HTML representation of the timeline event.
         *
         * @return string Rendered HTML string.
         */
        public function get_event_HTML(): string
        {
            if (empty($this->label) || empty($this->name)) {
                return '';
            }

            return sprintf(
                '<div class="oes-timeline-event-wrapper oes-archive-wrapper oes-ignore-alphabet-filter oes-post-filter-wrapper oes-post-filter-%1$s" data-post="%1$s" data-oes-id="%1$s">
                    <div class="oes-timeline-event %2$s">
                        <div class="oes-timeline-event-container">
                            <span class="oes-timeline-event-title"><a href="%3$s">%4$s</a></span>
                            <span class="oes-timeline-event-text">%5$s</span>
                        </div>
                    </div>
                </div>',
                esc_attr($this->event_ID),
                esc_attr($this->range_flag ? 'oes-timeline-range' : ''),
                esc_url($this->permalink),
                $this->label,
                $this->name
            );
        }
    }

endif;
