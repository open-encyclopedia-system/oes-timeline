<?php

namespace OES\Monadic;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('\OES\Admin\Module_Page')) oes_include('admin/pages/class-module_page.php');

if (!class_exists('Timeline_Module_Page')) :

    class Timeline_Module_Page extends \OES\Admin\Module_Page
    {
        /** @inheritdoc */
        public function help_tab(): void
        {

            $screen = get_current_screen();
            if ($screen->id === 'oes-settings_page_oes_timeline'
                || ($screen->id === 'oes-settings_page_oes_settings_schema' && (($_GET['type'] ?? '') == 'timeline'))) {

                $screen->add_help_tab([
                    'id' => 'oes_timeline_help',
                    'title' => 'Timeline',
                    'content' =>  '<p>' .
                        __('The OES Timeline feature provides a visual representation of events over time. It displays a ' .
                            'chronological sequence of content—such as historical events, project milestones, or other ' .
                            'date-based entries—using a customisable layout.', 'oes-timeline') .
                        '</p><p>' .
                        __('Each item on the timeline is defined by one or more date fields from a custom post type. ' .
                            'Optional labels and a second date field can be used to represent time spans or additional ' .
                            'context.', 'oes-timeline') .
                        '</p><p>' .
                        __('Timelines are ideal for showcasing content that benefits from temporal context, helping users ' .
                            'understand progression or historical relationships at a glance.', 'oes-timeline') . '</p>'

                ]);
                $screen->add_help_tab([
                    'id' => 'oes_timeline_enable',
                    'title' => 'Enable',
                    'content' => '<p>' .
                        __('Timeline creation support can be selectively enabled for specific post types via the OES schema ' .
                            'settings interface, see ', 'oes-timeline') .
                        '<a href="' . esc_url(admin_url('admin.php?page=oes_settings_schema')) . '">' .
                        __('OES Settings / Schema', 'oes-timeline') . '</a>' . '.' .
                        '</p>' .
                        '<p>' .
                        __('When enabling timeline functionality for a post type, you must check a specific option ' .
                            'labeled “Enable Timeline” in the general schema definition of the post type. Once this is enabled, ' .
                            'the post type will be treated as timeline-compatible and will expose the necessary logic for ' .
                            'mapping metadata via the post type˚s schema.', 'oes-timeline') .
                        '</p>'
                ]);
                $screen->add_help_tab([
                    'id' => 'oes_timeline_schema',
                    'title' => 'Schema',
                    'content' => '<p>' .
                        __('If enabled for specific post types, the configuration can be done in the Timeline tab of ',
                            'oes-timeline') .
                        '<a href="' . esc_url(admin_url('admin.php?page=oes_settings_schema')) . '">' .
                        __('OES Settings / Schema', 'oes-timeline') . '</a>' . '.' .
                        '</p><p>' .
                        __('A collection of events can be visualised using a timeline. Each event must have at least one ' .
                            'date field and may include a second date field to represent a time span. Optionally, a ' .
                            'specific date can be supplemented with a label.', 'oes-timeline') .
                        '</p><p>' .
                        __('If the <b>Use as Archive</b> option is enabled for a post type, the default archive page will ' .
                            'be replaced by a timeline – provided the OES theme is active.', 'oes-timeline') .
                        '</p><p>' .
                        __('Timeline data is defined using fields from individual event entries:', 'oes-timeline') .
                        '</p>' .
                        '<ul>' .
                        '<li>' . __('<b>Name</b>: Select a field to be used as the event name. It will appear in the timeline ' .
                            'alongside the event label.', 'oes-timeline') . '</li>' .
                        '<li>' . __('<b>Start</b>: This field represents the event\'s main date.', 'oes-timeline') . '</li>' .
                        '<li>' . __('<b>End</b>: If defined and populated, the combination of Start and End will be ' .
                            'displayed as a time span.', 'oes-timeline') . '</li>' .
                        '<li>' . __('<b>Label</b>: Select a field to be used as the event label.', 'oes-timeline') . '</li>' .
                        '<li>' . __('<b>Start Label</b> / <b>End Label</b>: Optional fields to define custom labels for the ' .
                            'Start and End dates instead of <b>Label</b>.', 'oes-timeline') . '</li>' .
                        '</ul>'
                ]);
                $screen->add_help_tab([
                    'id' => 'oes_timeline_design',
                    'title' => 'Design',
                    'content' =>  '<p>' .
                        __('You can change the design by adding custom CSS to your project, or by setting colours in ',
                            'oes-timeline') . ' ' .
                        '<a href="' . esc_url(admin_url('admin.php?page=oes_timeline')) . '">' .
                        __('OES Settings / Timeline', 'oes-timeline') . '</a>' . '.' .
                        '</p><p>' .
                        __('The following CSS variables control the appearance of the timeline:', 'oes-timeline') .
                        '</p>' .
                        '<ul>' .
                        '<li><strong>Event</strong><code>--oes-timeline-color</code>: ' .
                        __('Main event colour. Used for event text and markers.', 'oes-timeline') . '</li>' .
                        '<li><strong>Lines</strong><code>--oes-timeline-color2</code>: '
                        . __('Line colour. Used for connectors and inactive elements.', 'oes-timeline') . '</li>' .
                        '<li><strong>Icons</strong><code>--oes-timeline-background</code>: ' .
                        __('Icon background colour.', 'oes-timeline') . '</li>' .
                        '<li><strong>Years</strong><code>--oes-timeline-year</code>: ' .
                        __('Year label colour.', 'oes-timeline') . '</li>' .
                        '</ul>' .
                        '<p>' . __('Default values vary depending on the active theme. You can override them using CSS or ' .
                            'theme custom properties.', 'oes-timeline') .
                        '</p>'

                ]);

                $screen->set_help_sidebar('<p><strong>Example </strong></p>' .
                    '<p><a href="https://demo.open-encyclopedia-system.org/timeline/" target="_blank">OES Demo Timeline</a></p>');
            }
        }
    }

    new Timeline_Module_Page([
        'name' => 'Timeline',
        'schema_enabled' => true,
        'types' => []
    ]);

endif;