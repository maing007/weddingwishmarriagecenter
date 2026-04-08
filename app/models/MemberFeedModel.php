<?php

/**
 * Discovery feed: opposite-gender profiles, actions (view / approve / defer), exclusions after approve or defer.
 */
class MemberFeedModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }

    public static function normalizeGender(?string $g): string
    {
        $g = strtolower(trim((string) $g));
        if ($g === '') {
            return '';
        }
        if (strncmp($g, 'female', 6) === 0 || $g === 'f') {
            return 'female';
        }
        if (strncmp($g, 'male', 4) === 0 || $g === 'm') {
            return 'male';
        }

        return '';
    }

    public static function isOppositeGender(?string $viewerGender, ?string $targetGender): bool
    {
        $a = self::normalizeGender($viewerGender);
        $b = self::normalizeGender($targetGender);

        return $a !== '' && $b !== '' && $a !== $b;
    }

    /**
     * Opposite-gender members for discovery: not self, not admin-assigned to viewer, not approved/deferred from feed.
     *
     * @return list<array<string,mixed>>
     */
    public function getDiscoverMembers(int $viewerId, string $viewerGender, int $limit = 48): array
    {
        $vNorm = self::normalizeGender($viewerGender);
        if ($vNorm === '') {
            return [];
        }

        $lim = max(1, min(96, $limit));

        if ($vNorm === 'male') {
            $genderSql = "(LOWER(TRIM(ud.gender)) LIKE 'female%' OR LOWER(TRIM(ud.gender)) IN ('f','female'))";
        } else {
            $genderSql = "(
                LOWER(TRIM(ud.gender)) = 'male'
                OR (
                    LOWER(TRIM(ud.gender)) LIKE 'male%'
                    AND LOWER(TRIM(ud.gender)) NOT LIKE '%female%'
                )
                OR LOWER(TRIM(ud.gender)) IN ('m','male')
            )";
        }

        $sql = "
            SELECT
                ud.id,
                ud.first_name,
                ud.second_name,
                ud.gender,
                ud.dob,
                ud.religion,
                COALESCE(NULLIF(ud.photo2_url, ''), NULLIF(ud.photo1_status, '')) AS avatar
            FROM user_details ud
            WHERE ud.id != :viewer
              AND {$genderSql}
              AND NOT EXISTS (
                  SELECT 1 FROM member_assignments ma
                  WHERE ma.assigned_to = :viewer2 AND ma.assigned_member = ud.id
              )
              AND NOT EXISTS (
                  SELECT 1 FROM member_feed_interactions mfi
                  WHERE mfi.viewer_user_id = :viewer3
                    AND mfi.target_user_id = ud.id
                    AND (mfi.approved_at IS NOT NULL OR mfi.deferred_at IS NOT NULL)
              )
            ORDER BY ud.created_at DESC
            LIMIT {$lim}
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':viewer' => $viewerId,
            ':viewer2' => $viewerId,
            ':viewer3' => $viewerId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return $rows;
    }

    public function getInteraction(int $viewerId, int $targetId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM member_feed_interactions WHERE viewer_user_id = ? AND target_user_id = ? LIMIT 1'
        );
        $stmt->execute([$viewerId, $targetId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function markViewed(int $viewerId, int $targetId): void
    {
        $pdo = $this->db;
        $sql = "
            INSERT INTO member_feed_interactions (viewer_user_id, target_user_id, viewed_at)
            VALUES (:v, :t, NOW())
            ON DUPLICATE KEY UPDATE viewed_at = IFNULL(viewed_at, NOW())
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':v' => $viewerId, ':t' => $targetId]);
    }

    public function approve(int $viewerId, int $targetId): void
    {
        $pdo = $this->db;
        $sql = "
            INSERT INTO member_feed_interactions (viewer_user_id, target_user_id, approved_at)
            VALUES (:v, :t, NOW())
            ON DUPLICATE KEY UPDATE approved_at = NOW(), updated_at = CURRENT_TIMESTAMP
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':v' => $viewerId, ':t' => $targetId]);
    }

    public function defer(int $viewerId, int $targetId): void
    {
        $pdo = $this->db;
        $sql = "
            INSERT INTO member_feed_interactions (viewer_user_id, target_user_id, deferred_at)
            VALUES (:v, :t, NOW())
            ON DUPLICATE KEY UPDATE deferred_at = NOW(), updated_at = CURRENT_TIMESTAMP
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':v' => $viewerId, ':t' => $targetId]);
    }

    /**
     * True if viewer may use discovery actions on target (opposite gender, not assigned, not already approved/deferred from feed).
     */
    public function canDiscoverInteract(int $viewerId, int $targetId, string $viewerGender, string $targetGender): bool
    {
        if ($viewerId === $targetId || !self::isOppositeGender($viewerGender, $targetGender)) {
            return false;
        }
        $stmt = $this->db->prepare(
            'SELECT 1 FROM member_assignments WHERE assigned_to = ? AND assigned_member = ? LIMIT 1'
        );
        $stmt->execute([$viewerId, $targetId]);
        if ($stmt->fetchColumn()) {
            return false;
        }

        $stmt = $this->db->prepare(
            'SELECT approved_at, deferred_at FROM member_feed_interactions WHERE viewer_user_id = ? AND target_user_id = ? LIMIT 1'
        );
        $stmt->execute([$viewerId, $targetId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && (!empty($row['approved_at']) || !empty($row['deferred_at']))) {
            return false;
        }

        return true;
    }
}
