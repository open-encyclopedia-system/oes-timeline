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

        public string $option_prefix = 'oes_timeline';
        public bool $encoded = true;
        public bool $post_type_dependent = false;


        //Implement parent
        function information_html(): string
        {
            return '<div class="oes-tool-information-wrapper"><p>' .
                __('Choose the timeline colors (use css variables or hex notation).' , 'oes') .
                '</p><p>' .
                __('To configure a timeline for an OES post type use the OES schema options.' , 'oes') .
                '</p></div>';
        }


        //Overwrite parent
        function set_table_data_for_display()
        {
            $option = json_decode(get_option('oes_timeline', ''), true);
            $this->table_data = [
                [
                    'rows' => [
                        [
                        'cells' => [
                            [
                                'type' => 'th',
                                'value' => '<strong>' . __('Main Color', 'oes') .
                                    '</strong><code class="oes-object-identifier">-oes-timeline-color</code>'
                            ],
                            [
                                'class' => 'oes-table-transposed',
                                'value' => oes_html_get_form_element('text',
                                    'oes_timeline[color]',
                                    'oes_timeline-color',
                                    $option['color'] ?? 'var(--wp--preset--color--text)')
                            ]
                        ]
                    ],
                        [
                            'cells' => [
                                [
                                    'type' => 'th',
                                    'value' => '<strong>' . __('Secondary Color', 'oes') .
                                        '</strong><code class="oes-object-identifier">-oes-timeline-color2</code>'
                                ],
                                [
                                    'class' => 'oes-table-transposed',
                                    'value' => oes_html_get_form_element('text',
                                        'oes_timeline[color2]',
                                        'oes_timeline-color2',
                                        $option['color2'] ?? 'var(--wp--preset--color--inactive)')
                                ]
                            ]
                        ],
                        [
                            'cells' => [
                                [
                                    'type' => 'th',
                                    'value' => '<strong>' . __('Background Color', 'oes') .
                                        '</strong><code class="oes-object-identifier">-oes-timeline-background</code>'
                                ],
                                [
                                    'class' => 'oes-table-transposed',
                                    'value' => oes_html_get_form_element('text',
                                        'oes_timeline[background]',
                                        'oes_timeline-background',
                                        $option['background'] ?? 'var(--wp--preset--color--inactive)')
                                ]
                            ]
                        ],
                        [
                            'cells' => [
                                [
                                    'type' => 'th',
                                    'value' => '<strong>' . __('Year Color', 'oes') .
                                        '</strong><code class="oes-object-identifier">-oes-timeline-year</code>'
                                ],
                                [
                                    'class' => 'oes-table-transposed',
                                    'value' => oes_html_get_form_element('text',
                                        'oes_timeline[year]',
                                        'oes_timeline-year',
                                        $option['year'] ?? 'var(--wp--preset--color--inactive)')
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
    }

    // initialize
    \OES\Admin\Tools\register_tool('\OES\Timeline\Options', 'timeline');

endif;