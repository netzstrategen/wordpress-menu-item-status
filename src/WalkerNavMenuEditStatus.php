<?php

/**
 * @file
 * Contains \Netzstrategen\MenuItemStatus\WalkerNavMenuEditStatus.
 */

namespace Netzstrategen\MenuItemStatus;

class WalkerNavMenuEditStatus extends \Walker_Nav_Menu_Edit {

  /**
   * Injects a new form element for each menu item to control its status.
   */
  public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0) {
    parent::start_el($output, $item, $depth, $args, $id);

    $item_id = esc_attr($item->ID);
    $status = get_post_meta($item_id, '_menu_item_status', TRUE);
    $link_status = get_post_meta($item_id, '_menu_item_link_status', TRUE);
    $form_item = '
          <p class="field-checkbox description description-thin">
            <label for="edit-menu-item-status-' . $item_id . '">
              <input type="hidden" name="menu-item-status[' . $item_id . ']" value="1" />
              <input type="checkbox" id="edit-menu-item-status-' . $item_id . '" class="edit-menu-item-status" name="menu-item-status[' . $item_id . ']" value="checked"' . checked($status, 'checked', FALSE) . ' />
                ' . __('Hidden', Plugin::L10N) . '
            </label>
          </p>
';
    $form_item .= '
          <p class="field-checkbox description description-thin">
            <label for="edit-menu-item-link-status' . $item_id . '">
              <input type="hidden" name="menu-item-link-status[' . $item_id . ']" value="1" />
              <input type="checkbox" id="edit-menu-item-link-status-' . $item_id . '" class="edit-menu-item-link-status" name="menu-item-link-status[' . $item_id . ']" value="checked"' . checked($link_status, 'checked', FALSE) . ' />
                ' . __('Without link', Plugin::L10N) . '
            </label>
          </p>
';
    $output = preg_replace('@(id="menu-item-settings-' . $item_id . '">)@', '$1' . $form_item, $output);
  }

}
