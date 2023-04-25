<?php

namespace OES\Admin\Tools;

use function OES\ACF\get_all_object_fields;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('Module')) oes_include('/includes/admin/tools/config/class-config-module.php');

if (!class_exists('Timeline_Options_Post_Type')) :

    /**
     * Class Timeline_Options_Categories
     *
     * The timeline categories.
     */
    class Timeline_Options_Categories extends Module
    {

        public string $option_prefix = 'oes_timeline';
        public string $option = 'categories';
        public bool $encoded = true;


        //Implement parent
        function information_html(): string
        {
            return '<div class="oes-tool-information-wrapper"><p>' .
                __('To configure a timeline for a specific post type, at least one category must be defined for that ' .
                    'post type.', 'oes') .
                '</p><p>' .
                __('A <strong>Category Title</strong> can be defined for each category.', 'oes') .
                '</p><p>' .
                __('If the option <strong>Use as Archive</strong> is active for a post type, then the corresponding ' .
                    'archive is replaced with a timeline (if the OES theme is active).', 'oes') .
                '</p><p>' .
                __('The data for the timeline is defined via the fields of the individual events. A field for the ' .
                    '<strong>Name</strong> can be selected; the name is displayed in the timeline behind the event ' .
                    'label. <strong>Start</strong> defines the event time. If a field is defined for ' .
                    '<strong>End</strong> and the event has a valid value for the field, then ' .
                    '<strong>Start</strong> and <strong>End</strong> are understood as a time span. A label ' .
                    'can be defined for both fields <strong>Start Label</strong> and <strong>End Label</strong>.' .
                    'To exclude a field from being displayed enter "hidden" as label.', 'oes') .
                '</p></div>';
        }


        //Overwrite parent
        function empty(): string
        {
            return '<div class="oes-tool-information-wrapper"><p>' .
                __('To configure a timeline for a specific post type, at least one category must be defined for that ' .
                    'post type.', 'oes') .
                '</p></div>';
        }


        //Overwrite parent
        function set_table_data_for_display()
        {

            /* get global OES instance */
            $oes = OES();
            if (!empty($oes->post_types)) {

                foreach ($oes->post_types as $postTypeKey => $postTypeData) {

                    $option = get_option('oes_timeline-' . $postTypeKey);
                    $categories = get_option('oes_timeline-' . $postTypeKey . '-categories');

                    if ($option && $option > 0) {


                        $nestedTable = [];
                        for ($i = 1; $i < $option + 1; $i++) {

                            /* category id */
                            $category = 'cat' . $i;

                            /* category */
                            $categoryArray = $categories ? json_decode($categories, true) : [];

                            /* get all fields for this post type */
                            $allFields = get_all_object_fields($postTypeKey, false, false);

                            /* prepare html for title options */
                            $textOptions = ['none' => '(None)'];
                            $dateOptions = ['none' => '(None)'];
                            foreach ($allFields as $fieldKey => $singleField)
                                if (in_array($singleField['type'], ['text', 'textarea', 'wysiwyg']))
                                    $textOptions[$fieldKey] = empty($singleField['label']) ? $fieldKey : $singleField['label'];
                                elseif ($singleField['type'] == 'date_picker')
                                    $dateOptions[$fieldKey] = empty($singleField['label']) ? $fieldKey : $singleField['label'];

                            $nestedTable[] = [
                                'type' => 'trigger',
                                'cells' => [
                                    [
                                        'value' => sprintf('<strong>%s %s: %s</strong>',
                                            __('Category ', 'oes'),
                                            $i,
                                            $categoryArray[$category]['title'] ?? '')
                                    ]
                                ]
                            ];

                            $nestedTable[] = [
                                'type' => 'target',
                                'nested_tables' => [
                                    [
                                        'rows' => [
                                            [
                                                'cells' => [
                                                    [
                                                        'type' => 'th',
                                                        'value' => '<strong>' . __('Category Title', 'oes') . '</strong>'
                                                    ],
                                                    [
                                                        'class' => 'oes-table-transposed',
                                                        'value' => oes_html_get_form_element('text',
                                                            'oes_timeline[' . $postTypeKey . '][' . $category . '][title]',
                                                            'oes_timeline-' . $postTypeKey . '-' . $category . '-title',
                                                            $categoryArray[$category]['title'] ?? $category
                                                        )
                                                    ]
                                                ]
                                            ],
                                            [
                                                'cells' => [
                                                    [
                                                        'type' => 'th',
                                                        'value' => '<strong>' . __('Use as archive', 'oes') . '</strong>'
                                                    ],
                                                    [
                                                        'class' => 'oes-table-transposed',
                                                        'value' => oes_html_get_form_element('checkbox',
                                                            'oes_timeline[' . $postTypeKey . '][' . $category . '][archive]',
                                                            'oes_timeline-' . $postTypeKey . '-' . $category . '-archive',
                                                            $categoryArray[$category]['archive'] ?? false
                                                        )
                                                    ]
                                                ]
                                            ],
                                            [
                                                'cells' => [
                                                    [
                                                        'type' => 'th',
                                                        'value' => '<strong>' . __('Name', 'oes') . '</strong>'
                                                    ],
                                                    [
                                                        'class' => 'oes-table-transposed',
                                                        'value' => oes_html_get_form_element('select',
                                                            'oes_timeline[' . $postTypeKey . '][' . $category . '][name]',
                                                            'oes_timeline-' . $postTypeKey . '-' . $category . '-name',
                                                            $categoryArray[$category]['name'] ?? '',
                                                            ['options' => $textOptions]
                                                        )
                                                    ]
                                                ]
                                            ],
                                            [
                                                'cells' => [
                                                    [
                                                        'type' => 'th',
                                                        'value' => '<strong>' . __('Start (Field)', 'oes') . '</strong>'
                                                    ],
                                                    [
                                                        'class' => 'oes-table-transposed',
                                                        'value' => oes_html_get_form_element('select',
                                                            'oes_timeline[' . $postTypeKey . '][' . $category . '][start]',
                                                            'oes_timeline-' . $postTypeKey . '-' . $category . '-start',
                                                            $categoryArray[$category]['start'] ?? '',
                                                            ['options' => $dateOptions]
                                                        )
                                                    ]
                                                ]
                                            ],
                                            [
                                                'cells' => [
                                                    [
                                                        'type' => 'th',
                                                        'value' => '<strong>' . __('End (Field)', 'oes') . '</strong>'
                                                    ],
                                                    [
                                                        'class' => 'oes-table-transposed',
                                                        'value' => oes_html_get_form_element('select',
                                                            'oes_timeline[' . $postTypeKey . '][' . $category . '][end]',
                                                            'oes_timeline-' . $postTypeKey . '-' . $category . '-end',
                                                            $categoryArray[$category]['end'] ?? '',
                                                            ['options' => $dateOptions]
                                                        )
                                                    ]
                                                ]
                                            ],
                                            [
                                                'cells' => [
                                                    [
                                                        'type' => 'th',
                                                        'value' => '<strong>' . __('Start Label (Field)', 'oes') . '</strong>'
                                                    ],
                                                    [
                                                        'class' => 'oes-table-transposed',
                                                        'value' => oes_html_get_form_element('select',
                                                            'oes_timeline[' . $postTypeKey . '][' . $category . '][start_label]',
                                                            'oes_timeline-' . $postTypeKey . '-' . $category . '-start_label',
                                                            $categoryArray[$category]['start_label'] ?? '',
                                                            ['options' => $textOptions]
                                                        )
                                                    ]
                                                ]
                                            ],
                                            [
                                                'cells' => [
                                                    [
                                                        'type' => 'th',
                                                        'value' => '<strong>' . __('End Label (Field)', 'oes') . '</strong>'
                                                    ],
                                                    [
                                                        'class' => 'oes-table-transposed',
                                                        'value' => oes_html_get_form_element('select',
                                                            'oes_timeline[' . $postTypeKey . '][' . $category . '][end_label]',
                                                            'oes_timeline-' . $postTypeKey . '-' . $category . '-end_label',
                                                            $categoryArray[$category]['end_label'] ?? '',
                                                            ['options' => $textOptions]
                                                        )
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ];
                        }


                        $this->table_data[] = [
                            'rows' => [
                                [
                                    'type' => 'trigger',
                                    'cells' => [
                                        [
                                            'value' => '<strong>' . ($postTypeData['label'] ?? $postTypeKey) . '</strong>' .
                                                '<code class="oes-object-identifier">' . $postTypeKey . '</code>'
                                        ]
                                    ]
                                ],
                                [
                                    'type' => 'target',
                                    'nested_tables' => [[
                                        'rows' => $nestedTable
                                    ]]
                                ]
                            ]
                        ];
                    }
                }
            }
        }

    }

    // initialize
    register_tool('\OES\Admin\Tools\Timeline_Options_Categories', 'timeline-categories');

endif;