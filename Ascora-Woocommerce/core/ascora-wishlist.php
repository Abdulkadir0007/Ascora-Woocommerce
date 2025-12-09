<?php

declare(strict_types=1);

function ascora_is_in_wishlist($product_id)
{
    $user_id = get_current_user_id();
    $key     = $user_id ? "user_$user_id" : 'guest_' . ascora_get_guest_id();
    $list    = get_transient($key) ?: [];

    return in_array($product_id, $list);
}
