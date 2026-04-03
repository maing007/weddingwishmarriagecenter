<?php

class AdminSalesReportModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }

    private function baseWhere(string $search): array
    {
        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = "(first_name LIKE :search OR second_name LIKE :search OR email LIKE :search OR mobile_number LIKE :search OR matri_id LIKE :search OR city LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        return [$where, $params];
    }

    private function tabCondition(string $tab): string
    {
        switch ($tab) {
            case 'reg_receivable':
                return "COALESCE(registration_fee, 0) > 0";
            case 'reg_received':
                return "COALESCE(registration_fee, 0) > 0 AND LOWER(COALESCE(user_status, '')) IN ('approved','active','paid','received')";
            case 'rishta_receivable':
                return "COALESCE(final_fee, 0) > 0";
            case 'rishta_received':
                return "COALESCE(final_fee, 0) > 0 AND LOWER(COALESCE(featured_status, '')) IN ('approved','active','paid','received')";
            default:
                return "COALESCE(registration_fee, 0) > 0";
        }
    }

    public function getTabCounts(string $search): array
    {
        $tabs = ['reg_receivable', 'reg_received', 'rishta_receivable', 'rishta_received'];
        $counts = [];

        foreach ($tabs as $tab) {
            [$where, $params] = $this->baseWhere($search);
            $where[] = $this->tabCondition($tab);
            $sql = "SELECT COUNT(*) AS total FROM user_details WHERE " . implode(' AND ', $where);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $counts[$tab] = (int)$stmt->fetchColumn();
        }

        return $counts;
    }

    public function getRows(string $search, string $tab, int $limit, int $offset): array
    {
        [$where, $params] = $this->baseWhere($search);
        $where[] = $this->tabCondition($tab);

        $sql = "SELECT id, matri_id, first_name, second_name, email, mobile_number, city, registration_fee, final_fee, user_status, featured_status, created_at
                FROM user_details
                WHERE " . implode(' AND ', $where) . "
                ORDER BY id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalRows(string $search, string $tab): int
    {
        [$where, $params] = $this->baseWhere($search);
        $where[] = $this->tabCondition($tab);

        $sql = "SELECT COUNT(*) AS total FROM user_details WHERE " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
