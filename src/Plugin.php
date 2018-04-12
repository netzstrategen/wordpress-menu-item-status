<?php

/**
 * @file
 * Contains \Netzstrategen\MenuItemStatus\Plugin.
 */

namespace Netzstrategen\MenuItemStatus;

/**
 * Main front-end functionality.
 */
class Plugin {

  /**
   * Prefix for naming.
   *
   * @var string
   */
  const PREFIX = 'menu-item-status';

  /**
   * Gettext localization domain.
   *
   * @var string
   */
  const L10N = self::PREFIX;

  /**
   * @var string
   */
  private static $baseUrl;

  /**
   * @implements init
   */
  public static function init() {
    add_action('wp_head', __CLASS__ . '::wp_head', 100);
    add_action('wp_update_nav_menu', __CLASS__ . '::wp_update_nav_menu');
    add_filter('wp_get_nav_menu_items', __CLASS__ . '::wp_get_nav_menu_items', 10, 3);
    add_filter('walker_nav_menu_start_el', __CLASS__ . '::walker_nav_menu_start_el', 10, 4);

    add_filter('wp_edit_nav_menu_walker', function () {
      return __NAMESPACE__ . '\WalkerNavMenuEditStatus';
    });
  }

  /**
   * Adds necessary inline CSS into HTML head.
   *
   * @implements wp_head
   */
  public static function wp_head() {
    echo <<<EOD
<style>
.menu-item--hidden {
  display: none !important;
}
</style>
EOD;
  }

  /**
   * Updates post meta with menu item status.
   *
   * @implements wp_update_nav_menu
   */
  public static function wp_update_nav_menu(){
    foreach ($_POST['menu-item-status'] as $item_id => $value) {
      if (0 === $value = (int) $value) {
        update_post_meta($item_id, '_menu_item_status', $value);
      }
      if (isset($_POST['menu-item-link-status'][$item_id])) {
        $value = $_POST['menu-item-link-status'][$item_id];
        update_post_meta($item_id, '_menu_item_link_status', $value);
      }
    }
  }

  /**
   * Hides menu items if item status checkbox is checked.
   *
   * @implements wp_get_nav_menu_items
   */
  public static function wp_get_nav_menu_items($items, $menu, $args) {
    foreach ($items as $key => $item) {
      if (FALSE === (bool) get_post_meta($item->ID, '_menu_item_status', TRUE)) {
        $item->classes[] = 'menu-item--hidden';
      }
      elseif (FALSE !== $key = array_search('menu-item--hidden', $item->classes)) {
        unset($item->classes[$key]);
      }
    }
    return $items;
  }

  /**
   * Removes <a> HTML tag if item link status is checked.
   *
   * @implements walker_nav_menu_start_el
   */
  public static function walker_nav_menu_start_el($item_output, $item, $depth, $args) {
    if (FALSE === (bool) get_post_meta($item->ID, '_menu_item_link_status', TRUE)) {
      $item_output = $args->before;
      $item_output .= '<span ' . $item->attributes . '>';
      $item_output .= $args->link_before . $item->title . $args->link_after;
      $item_output .= '</span>';
      $item_output .= $args->after;
    }
    return $item_output;
  }

  /**
   * Loads the plugin textdomain.
   */
  public static function loadTextdomain() {
    load_plugin_textdomain(static::L10N, FALSE, static::L10N . '/languages/');
  }

  /**
   * The base URL path to this plugin's folder.
   *
   * Uses plugins_url() instead of plugin_dir_url() to avoid a trailing slash.
   */
  public static function getBaseUrl() {
    if (!isset(static::$baseUrl)) {
      static::$baseUrl = plugins_url('', static::getBasePath() . '/plugin.php');
    }
    return static::$baseUrl;
  }

  /**
   * The absolute filesystem base path of this plugin.
   *
   * @return string
   */
  public static function getBasePath() {
    return dirname(__DIR__);
  }

}
