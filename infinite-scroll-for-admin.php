<?php
/*
Plugin Name: Infinite Scroll for Admin
Plugin URI: https://github.com/helgatheviking/Infinite-Scroll-for-Admin
Description: Use infinite scroll in the WordPress backend
Version: 1.0.1
Author: Kathy Darling
Author URI: http://www.kathyisawesome.com
License: GPL2

    Copyright 2013  Kathy Darling  (email: kathy.darling@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


// don't load directly
if ( ! function_exists( 'is_admin' ) ) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

/**
 * Make translation ready
 *
 * @since 1.0b
 * @return void
 */
function infinite_scroll_for_admin_text_domain() {
    load_plugin_textdomain( 'infinite-scroll-for-admin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
add_action( 'admin_init', 'infinite_scroll_for_admin_text_domain' );

/**
 * Load infinite scroll on particular pages
 *
 * @since 1.0b
 * @return void
 */

function infinite_scroll_for_admin_scripts( $hook ) {


    if ( in_array( $hook, array( 'edit.php', 'edit-tags.php', 'edit-comments.php', 'users.php', 'upload.php' ) ) ) {

        $min = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

        wp_enqueue_script( 'infinite_scroll', plugins_url( 'js/jquery.infinitescroll' . $min . '.js' , __FILE__ ), array('jquery'), '2.0b2', true );

        $l10n = array( 'msgText' => __( 'Loading...', 'infinite-scroll-for-admin' ),
                        'finishedMsg' => __( 'The end.', 'infinite-scroll-for-admin' ) );

        wp_localize_script( 'infinite_scroll', 'Infinite_Scroll_Admin', $l10n );

        add_action( 'admin_print_footer_scripts', 'infinite_scroll_for_admin_init_scripts' );

    }

}
add_action( 'admin_enqueue_scripts', 'infinite_scroll_for_admin_scripts' );

/**
 * Initiate infinite scroll on particular pages
 *
 * @since 1.0b
 * @return void
 */

function infinite_scroll_for_admin_init_scripts(){ ?>

    <style>
    #infscr-loading { text-align: center; }
    </style>

    <script type="text/javascript">

    (function ($) {

        // little conditional so we can re-use script on both edit.php and edit-comments.php
        if ( $('#the-comment-list').length ) {
            list = '#the-comment-list';
        } else {
            var list = '#the-list';
        }

        var colspan = $( list + ' tr:first-child').find('td').length;
        var contentSelector = list;
        var itemSelector = list + ' > tr';

       $(list).infinitescroll({
            loading: {
                finishedMsg: '<em>' + Infinite_Scroll_Admin.finishedMsg + '</em>',
                msg: $('<tr id="infscr-loading"><td colspan="' + colspan + '"><img alt="Loading..." src="' + "<?php echo admin_url( 'images/wpspin_light.gif' ); ?>" + '" /><div>' + Infinite_Scroll_Admin.msgText + '</div></td></tr>')
            },
            nextSelector: '.pagination-links a.next-page',
            navSelector: '.pagination-links',
            contentSelector: contentSelector,
            itemSelector: itemSelector,
            prefill: true,
            maxPage: $('.paging-input .total-pages').html(),
            debug: false
        });

    })(jQuery)

    </script>
<?php
}

