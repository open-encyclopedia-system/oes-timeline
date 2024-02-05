<?php

/**
 * Plugin Name: OES Timeline (OES Core Module)
 * Plugin URI: http://www.open-encyclopedia-system.org/
 * Description: Display a chronological sequence of events (post type that includes date fields) with a timeline.
 * Version: 1.2.0
 * Author: Maren Welterlich-Strobl, Freie Universität Berlin, Center für Digitale Systeme an der Universitätsbibliothek
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('oes/plugins_loaded', function () {

    /* check if OES Core Plugin is activated */
    if (!function_exists('OES')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning is-dismissible"><p>' .
                __('The OES Core Plugin is not active.', 'oes') . '</p></div>';
        });
    } else {

        /* exit early if OES Plugin was not completely initialized */
        global $oes;
        if (!$oes || !property_exists($oes, 'initialized') || !$oes->initialized) return;

        include_once(__DIR__ . '/includes/admin/functions-admin.php');
        include_once(__DIR__ . '/includes/admin/class-schema_timeline.php');
        include_once(__DIR__ . '/includes/class-timeline.php');
        include_once(__DIR__ . '/includes/functions.php');

        add_action('wp_head','OES\Timeline\wp_head', 99);
        add_filter('oes/schema_general', 'OES\Timeline\schema_enable', 10, 4);
        add_filter('oes/schema_tabs', 'OES\Timeline\schema_tabs', 10, 2);
        add_filter('oes/schema_options_single', 'OES\Timeline\schema_options_single', 10, 3);
        add_action('oes/theme_archive_list', 'OES\Timeline\theme_archive_list');
        add_action('wp_enqueue_scripts', 'OES\Timeline\enqueue_scripts');

        /* blocks */
        register_block_type(__DIR__ . '/includes/blocks/single/build');

        add_shortcode('oes_timeline', 'OES\Timeline\html');
        add_shortcode('oes_timeline_anchors', 'OES\Timeline\anchors_html');
    }
}, 14);