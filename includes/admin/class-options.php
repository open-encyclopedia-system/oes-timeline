<?php

namespace OES\Timeline;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('\OES\Admin\Tools\Module')) oes_include('admin/tools/config/class-config-module.php');

if (!class_exists('Options')) :

    /**
     * Class Options
     *
     * The general map options.
     */
    class Options extends \OES\Admin\Tools\Module
    {
        /** @inheritdoc */
        public string $option_prefix = 'oes_timeline';

        /** @inheritdoc */
        public bool $encoded = true;

        /** @inheritdoc */
        public bool $post_type_dependent = false;

        /** @inheritdoc */
        function information_html(): string
        {
            return '<div class="oes-tool-information-wrapper"><p>' .
                __('Choose the timeline colors (use css variables or hex notation).' , 'oes-timeline') .
                '</p></div>';
        }

        /** @inheritdoc */
        function set_table_data_for_display()
        {
            $option = json_decode(get_option('oes_timeline', ''), true);
            $options = [
                'color' => [
                    'title' => __('Events', 'oes-timeline'),
                    'default' => 'var(--wp--preset--color--text)'
                ],
                'color2' => [
                    'title' => __('Lines', 'oes-timeline'),
                    'default' => 'var(--wp--preset--color--inactive)'
                ],
                'background' => [
                    'title' => __('Icons', 'oes-timeline'),
                    'default' => 'var(--wp--preset--color--inactive)'
                ],
                'year' => [
                    'title' => __('Years', 'oes-timeline'),
                    'default' => 'var(--wp--preset--color--inactive)'
                ]
            ];

            foreach($options as $key => $singleOption) {
                $this->add_table_row(
                    [
                        'title' => $singleOption['title'] ?? $key,
                        'key' => 'oes_timeline[' . $key . ']',
                        'value' => $option[$key] ?? ($singleOption['default'] ?? '')
                    ],
                    [
                        'subtitle' => '<code class="oes-object-identifier">--oes-timeline-' . $key . '</code>'
                    ]
                );
            }
        }
    }

    // initialize
    \OES\Admin\Tools\register_tool('\OES\Timeline\Options', 'timeline');
endif;