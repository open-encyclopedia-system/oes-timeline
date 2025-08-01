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

        /** @inheritdoc */
        function set_table_data_for_display()
        {

            // Prepare option lists for text and date fields
            $textFieldOptions = ['none' => '-'];
            $dateFieldOptions = ['none' => '-'];
            $allFields = oes_get_all_object_fields($this->object);

            foreach ($allFields as $fieldKey => $singleField) {
                switch ($singleField['type']) {
                    case 'text':
                    case 'textarea':
                    case 'wysiwyg':
                        $textFieldOptions[$fieldKey] = $this->get_field_label($fieldKey, $singleField);
                        break;

                    case 'date_picker':
                        $dateFieldOptions[$fieldKey] = $this->get_field_label($fieldKey, $singleField);
                        break;

                    default:
                        break;
                }
            }

            // Get saved option values
            $values = $this->get_option_array($this->options['name'] . '-' . $this->object);

            $options = [
                'archive' => [
                    'title' => __('Use as archive', 'oes-timeline'),
                    'type' => 'checkbox',
                    'default' => false,
                ],
                'name' => [
                    'title' => __('Name', 'oes-timeline'),
                    'select_type' => 'text',
                    'default' => 'none',
                ],
                'start' => [
                    'title' => __('Start', 'oes-timeline'),
                    'select_type' => 'date',
                    'default' => 'none',
                ],
                'end' => [
                    'title' => __('End', 'oes-timeline'),
                    'select_type' => 'date',
                    'default' => 'none',
                ],
                'label' => [
                    'title' => __('Label (Field)', 'oes-timeline'),
                    'select_type' => 'text',
                    'default' => 'none',
                ],
                'start_label' => [
                    'title' => __('Start Label (Field)', 'oes-timeline'),
                    'select_type' => 'text',
                    'default' => 'none',
                ],
                'end_label' => [
                    'title' => __('End Label (Field)', 'oes-timeline'),
                    'select_type' => 'text',
                    'default' => 'none',
                ],
            ];

            foreach ($options as $key => $config) {
                $args = $config['args'] ?? [];

                // If the option requires a select input, set the options accordingly
                if (isset($config['select_type'])) {
                    $args['options'] = ($config['select_type'] === 'date') ? $dateFieldOptions : $textFieldOptions;
                    $type = 'select';
                } else {
                    $type = $config['type'] ?? 'select';
                }

                $value = $values[$key] ?? $config['default'];

                $this->add_table_row(
                    [
                        'title' => $config['title'] ?? $key,
                        'key' => $this->options['name'] . '[' . $key . ']',
                        'value' => $value,
                        'type' => $type,
                        'args' => $args
                    ]
                );
            }
        }

        /**
         * Get label from field or return field key.
         */
        protected function get_field_label(string $fieldKey, array $field): string{
            return empty($field['label']) ? $fieldKey : $field['label'];
        }
    }

    // initialize
    register_tool('\OES\Admin\Tools\Schema_Timeline', 'schema-timeline');

endif;