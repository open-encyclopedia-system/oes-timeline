<?php

namespace OES\Timeline;


/**
 * Add color for timeline css.
 *
 * @return void
 */
function wp_head(): void
{
    global $oes; ?>
    <style type="text/css" id="oes-colors">
    body {
    <?php echo $oes->block_theme ?
'--oes-timeline-color:var(--wp--preset--color--text);' .
'--oes-timeline-font-size:var(--wp--preset--font-size--small);' :
'--oes-timeline-color:var(--oes-text-black);' .
'--oes-timeline-font-size:16px;'; ?>
    }
    </style><?php
}


/**
 * Add timeline events to single schema.
 *
 * @param array $options The current schema options.
 * @param string $type The post type schema type.
 * @param string $objectKey The object key.
 * @return array Return the modified schema options.
 */
function schema_options_single(array $options, string $type = '', string $objectKey = ''): array
{
    $options['events'] = [
        'label' => __('Timeline Events', 'oes'),
        'option_name' => 'oes_timeline-event_field-' . $objectKey,
        'multiple' => true
    ];
    return $options;
}


/**
 * Add option to anabel timeline configuration for a post type as part of the schema definition.
 *
 * @param array $configs The current configs.
 * @param string $objectKey The object key.
 * @param string $type The object schema type.
 * @param string $component The object component.
 * @return array Return the modified configs.
 */
function schema_enable(array $configs, string $objectKey, string $type = '', string $component = ''): array
{
    if ($component == 'post_types')
        $configs['timeline'] = [
            'label' => __('Enable Timeline', 'oes'),
            'type' => 'checkbox',
            'value' => get_option('oes_timeline-enabled-' . $objectKey) ?? false,
            'options' => ['hidden' => true],
            'option_key' => 'oes_timeline-enabled-' . $objectKey
        ];
    return $configs;
}


/**
 * Add tab to schema definition if timeline configuration is enabled for this post type.
 *
 * @param array $tabs The current tabs.
 * @param string $objectKey The object key.
 * @return array The modified tabs.
 */
function schema_tabs(array $tabs, string $objectKey = ''): array
{
    $enabled = true;
    if (!empty($objectKey)) $enabled = get_option('oes_timeline-enabled-' . $objectKey) ?? false;
    if ($enabled) $tabs['timeline'] = __('Timeline', 'oes');
    return $tabs;
}