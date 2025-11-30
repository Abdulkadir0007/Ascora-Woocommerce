<?php
/**
 * My-Account Dashboard (Ascora)
 *
 * @package Ascora_WC
 */

defined('ABSPATH') || exit;
?>

<p><?php printf(esc_html__('Hello %s (not %s? %s)', 'ascora-wc'), '<strong>' . esc_html($current_user->display_name) . '</strong>', '<strong>' . esc_html($current_user->display_name) . '</strong>', '<a href="' . esc_url(wc_logout_url()) . '">' . esc_html__('Log out', 'ascora-wc') . '</a>'); ?>
</p>

<p><?php esc_html_e('From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.', 'ascora-wc'); ?>
</p>

<ul class="ascora-dashboard-links">
    <li><a
            href="<?php echo esc_url(wc_get_endpoint_url('orders')); ?>"><?php esc_html_e('Orders', 'ascora-wc'); ?></a>
    </li>
    <li><a
            href="<?php echo esc_url(wc_get_endpoint_url('edit-address')); ?>"><?php esc_html_e('Addresses', 'ascora-wc'); ?></a>
    </li>
    <li><a
            href="<?php echo esc_url(wc_get_endpoint_url('edit-account')); ?>"><?php esc_html_e('Account details', 'ascora-wc'); ?></a>
    </li>
</ul>