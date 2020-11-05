<?php
/**
 * Plugin Name: MAM Linkbuilding System
 * Plugin URI: https://github.com/AliSal92/mam-linkbuilding
 * Description: Use to manage our Link building clients and link building resources easily and more effectively to make it easier for everyone to manage the clients and the resources in hand.
 * Version: 1.0
 * Author: AliSal
 * Text Domain: mam-linkbuilding
 * Author URI: https://github.com/AliSal92/
 * MAM Linkbuilding System is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * MAM Linkbuilding System is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MAM Linkbuilding System. If not, see <http://www.gnu.org/licenses/>.
 */

namespace MAM;

use MAM\Plugin\Init;


/**
 * Prevent direct access
 */
defined('ABSPATH') or die('</3');


/**
 * Require once the Composer Autoload
 */
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * Initialize and run all the core classes of the plugin
 */
if ( class_exists( 'MAM\Plugin\Init' ) ) {
    Init::register_services();
}