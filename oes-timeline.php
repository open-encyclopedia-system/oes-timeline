<?php

namespace OES\Timeline;

/**
 * OES Timeline (OES Core Module)
 *
 * @wordpress-plugin
 * Plugin Name:        OES Timeline (OES Core Module)
 * Plugin URI:         https://www.open-encyclopedia-system.org/
 * Description:        Display a chronological sequence of events (post type including date fields) with a timeline. Requires OES Core.
 * Version:            1.3.0
 * Author:             Maren Welterlich-Strobl, Freie Universität Berlin, FUB-IT
 * Author URI:         https://www.it.fu-berlin.de/die-fub-it/mitarbeitende/mstrobl.html
 * Requires at least:  6.5
 * Tested up to:       6.8.2
 * Requires PHP:       8.1
 * Tags:               timeline, events, chronological, visualization, plugin-addon, encyclopedia
 * License:            GPLv2 or later
 * License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */


if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('OES_TIMELINE_PLUGIN_URL', plugin_dir_url(__FILE__));

add_action('oes/plugins_loaded', function () {

    if (!function_exists('OES')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning is-dismissible"><p>' .
                __('The OES Core Plugin is not active.', 'oes') . '</p></div>';
        });
    } else {

        global $oes;
        if (!$oes || !property_exists($oes, 'initialized') || !$oes->initialized) {
            return;
        }

        if (is_admin()) {
            include_once __DIR__ . '/includes/admin/class-options.php';
            include_once __DIR__ . '/includes/admin/class-schema_timeline.php';
            include_once __DIR__ . '/includes/admin/class-module_page.php';
        }

        include_once __DIR__ . '/includes/class-timeline.php';
        include_once __DIR__ . '/includes/class-event.php';
        include_once __DIR__ . '/includes/functions.php';

        add_action('wp_head', __NAMESPACE__ . '\\wp_head', 99);
        add_action('oes/theme_archive_list', __NAMESPACE__ . '\\theme_archive_list');
        add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts');

        // Blocks
        add_action('init', function (){
            register_block_type(__DIR__ . '/includes/blocks/single/build');
        });

        // Shortcodes
        add_shortcode('oes_timeline', __NAMESPACE__ . '\\html');
        add_shortcode('oes_timeline_anchors', __NAMESPACE__ . '\\anchors_html');

        do_action('oes/timeline_plugin_loaded');
    }
}, 14);