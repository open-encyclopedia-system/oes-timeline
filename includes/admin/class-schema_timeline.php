<?php

namespace OES\Admin\Tools;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('Schema')) oes_include('admin/tools/config/class-config-schema.php');

if (!class_exists('Schema_Timeline')) :

    /**
     * Class Schema_Timeline
     *
     * The timeline schema for a post type.
     */
    class Schema_Timeline extends Schema
    {

        public array $options = [
            'name' => 'oes_timeline',
            'encoded' => true
        ];


        //Implement parent
        function additional_html(): string
        {
            return '<div class="oes-tool-information-wrapper"><p>' .
                __('A collection of events can be visualised with a timeline. Each event must have at least one date ' .
                    'field and can have a second date field to represent a time span. ' .
                    'A specific date can additionally be supplemented by a date label.', 'oes') .
                '</p><p>' .
                __('If the option <strong>Use as Archive</strong> is active for a post type, then the corresponding ' .
                    'archive is replaced with a timeline (if the OES theme is active).', 'oes') .
                '</p><p>' .
                __('The data for the timeline is defined via the fields of the individual events. A field for the ' .
                    '<strong>Name</strong> can be selected. The name is displayed in the timeline behind the event ' .
                    'label. <strong>Start</strong> defines the event time. If a field is defined for ' .
                    '<strong>End</strong> and the event has a valid value for the field, then ' .
                    '<strong>Start</strong> and <strong>End</strong> are understood as a time span. A label ' .
                    'can be defined for both fields <strong>Start Label</strong> and <strong>End Label</strong>. ' .
                    'To exclude a field from being displayed enter "hidden" as label.', 'oes') .
                '</p></div>';
        }


        //Overwrite parent
        function set_table_data_for_display()
        {

            /* get options */
            $optionsJSON = get_option($this->options['name'] . '-' . $this->object);
            $options = $optionsJSON ? json_decode($optionsJSON, true) : [];

            /* get all fields for this post type */
            $allFields = oes_get_all_object_fields($this->object);

            /* prepare html for title options */
            $textOptions = ['none' => '-'];
            $dateOptions = ['none' => '-'];
            foreach ($allFields as $fieldKey => $singleField)
                if (in_array($singleField['type'], ['text', 'textarea', 'wysiwyg']))
                    $textOptions[$fieldKey] = empty($singleField['label']) ? $fieldKey : $singleField['label'];
                elseif ($singleField['type'] == 'date_picker')
                    $dateOptions[$fieldKey] = empty($singleField['label']) ? $fieldKey : $singleField['label'];


            $this->table_data[] = [
                'rows' => [
                    [
                        'cells' => [
                            [
                                'type' => 'th',
                                'value' => '<strong>' . __('Use as archive', 'oes') . '</strong>'
                            ],
                            [
                                'class' => 'oes-table-transposed',
                                'value' => oes_html_get_form_element('checkbox',
                                    $this->options['name'] . '[archive]',
                                    $this->options['name'] . '-archive',
                                    $options['archive'] ?? false
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
                                    $this->options['name'] . '[name]',
                                    $this->options['name'] . '-name',
                                    $options['name'] ?? '',
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
                                    $this->options['name'] . '[start]',
                                    $this->options['name'] . '-start',
                                    $options['start'] ?? '',
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
                                    $this->options['name'] . '[end]',
                                    $this->options['name'] . '-end',
                                    $options['end'] ?? '',
                                    ['options' => $dateOptions]
                                )
                            ]
                        ]
                    ],
                    [
                        'cells' => [
                            [
                                'type' => 'th',
                                'value' => '<strong>' . __('Label (Field)', 'oes') . '</strong>'
                            ],
                            [
                                'class' => 'oes-table-transposed',
                                'value' => oes_html_get_form_element('select',
                                    $this->options['name'] . '[label]',
                                    $this->options['name'] . '-label',
                                    $options['label'] ?? '',
                                    ['options' => $textOptions]
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
                                    $this->options['name'] . '[start_label]',
                                    $this->options['name'] . '-start_label',
                                    $options['start_label'] ?? '',
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
                                    $this->options['name'] . '[end_label]',
                                    $this->options['name'] . '-end_label',
                                    $options['end_label'] ?? '',
                                    ['options' => $textOptions]
                                )
                            ]
                        ]
                    ]]
            ];
        }
    }

    // initialize
    register_tool('\OES\Admin\Tools\Schema_Timeline', 'schema-timeline');

endif;