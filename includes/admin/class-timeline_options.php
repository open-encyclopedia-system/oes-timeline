<?php

namespace OES\Admin\Tools;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('Module')) oes_include('/includes/admin/tools/config/class-config-module.php');

if (!class_exists('Timeline_Options')) :

    /**
     * Class Timeline_Options
     *
     * The general timeline options.
     */
    class Timeline_Options extends Module
    {

        public string $option_prefix = 'oes_timeline';


        //Implement parent
        function information_html(): string
        {
            return '<div class="oes-tool-information-wrapper"><p>' .
                __('To configure a timeline for a specific post type, at least one category must be defined for that ' .
                    'post type.', 'oes') .
                '</p></div>';
        }


        //Overwrite parent
        function set_table_data_for_display()
        {
            $oes = OES();

            $rows = [];
            foreach ($oes->post_types as $postTypeKey => $postType)
                $rows[] = [
                    'cells' => [
                        [
                            'type' => 'th',
                            'value' => '<strong>' . $postType['label'] .
                                '</strong><code class="oes-object-identifier">' . $postTypeKey . '</code>'
                        ],
                        [
                            'class' => 'oes-table-transposed',
                            'value' => oes_html_get_form_element('number',
                                $this->option_prefix . '-' . $postTypeKey,
                                $this->option_prefix . '-' . $postTypeKey,
                                get_option($this->option_prefix . '-' . $postTypeKey) ?? 0,
                                ['min' => 0, 'max' => 100])
                        ]
                    ]
                ];

            $this->table_data = [
                [
                    'type' => 'thead',
                    'rows' => [
                        [
                            'class' => 'oes-config-table-separator',
                            'cells' => [
                                [
                                    'type' => 'th',
                                    'value' => '<strong>' . __('Post Type', 'oes') . '</strong>'
                                ],
                                [
                                    'type' => 'th',
                                    'value' => '<strong>' . __('# of Categories', 'oes') . '</strong>'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'rows' => $rows
                ]];
        }
    }

    // initialize
    register_tool('\OES\Admin\Tools\Timeline_Options', 'timeline');

endif;