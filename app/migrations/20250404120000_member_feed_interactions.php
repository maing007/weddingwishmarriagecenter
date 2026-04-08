<?php

return [
    "
    CREATE TABLE IF NOT EXISTS member_feed_interactions (
        viewer_user_id INT UNSIGNED NOT NULL,
        target_user_id INT UNSIGNED NOT NULL,
        viewed_at DATETIME NULL DEFAULT NULL,
        approved_at DATETIME NULL DEFAULT NULL,
        deferred_at DATETIME NULL DEFAULT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (viewer_user_id, target_user_id),
        KEY idx_mfi_target (target_user_id),
        KEY idx_mfi_viewer_deferred (viewer_user_id, deferred_at),
        KEY idx_mfi_viewer_approved (viewer_user_id, approved_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ",
];
