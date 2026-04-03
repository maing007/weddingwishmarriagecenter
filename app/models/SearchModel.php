<?php

class SearchModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getFilterOptions(): array
    {
        return [
            'religions' => $this->distinctList('religion'),
            'castes' => $this->distinctList('caste'),
            'sects' => $this->distinctList('maslak'),
            'mother_tongues' => $this->distinctList('mother_tongue'),
            'educations' => $this->distinctList('education'),
            'occupations' => $this->distinctList('occupation'),
            'countries' => $this->distinctList('country'),
            'states' => $this->distinctList('state'),
            'cities' => $this->distinctList('city'),
            'body_types' => $this->distinctList('body_type'),
            'complexions' => $this->distinctList('skin_tone'),
        ];
    }

    public function searchById(string $idOrMatri): array
    {
        $term = trim($idOrMatri);
        if ($term === '') {
            return [];
        }

        $sql = "SELECT * FROM user_details WHERE id = :id OR matri_id = :matri LIMIT 50";
        $stmt = $this->db->pdo()->prepare($sql);
        $stmt->execute([
            ':id' => ctype_digit($term) ? (int)$term : -1,
            ':matri' => $term,
        ]);

        return $this->normalizeRows($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function searchByName(string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return [];
        }

        $k = '%' . $name . '%';
        $stmt = $this->db->pdo()->prepare(
            "SELECT * FROM user_details
             WHERE first_name LIKE :k OR second_name LIKE :k
             ORDER BY created_at DESC
             LIMIT 60"
        );
        $stmt->execute([':k' => $k]);

        return $this->normalizeRows($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function keywordSearch(string $keyword): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return [];
        }

        $k = '%' . $keyword . '%';
        $params = [':k' => $k];
        $religionExtra = '';
        if (strcasecmp($keyword, 'Muslim') === 0) {
            $religionExtra = ' OR religion LIKE :km OR religion LIKE :ki ';
            $params[':km'] = '%Muslim%';
            $params[':ki'] = '%Islam%';
        }

        $sql = "SELECT * FROM user_details
                WHERE first_name LIKE :k
                   OR second_name LIKE :k
                   OR religion LIKE :k
                   OR maslak LIKE :k
                   OR caste LIKE :k
                   OR sub_caste LIKE :k
                   OR mother_tongue LIKE :k
                   OR country LIKE :k
                   OR state LIKE :k
                   OR city LIKE :k
                   OR about_us LIKE :k
                   {$religionExtra}
                ORDER BY created_at DESC
                LIMIT 80";

        $stmt = $this->db->pdo()->prepare($sql);
        $stmt->execute($params);

        return $this->normalizeRows($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function advancedSearch(array $filters): array
    {
        $sql = "SELECT * FROM user_details WHERE 1=1";
        $params = [];

        if (!empty($filters['gender'])) {
            $sql .= ' AND gender = :gender';
            $params[':gender'] = $filters['gender'];
        }

        if (!empty($filters['from_age']) && !empty($filters['to_age'])) {
            $sql .= ' AND TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN :from_age AND :to_age';
            $params[':from_age'] = (int)$filters['from_age'];
            $params[':to_age'] = (int)$filters['to_age'];
        }

        if (!empty($filters['from_height'])) {
            $sql .= ' AND CAST(height AS UNSIGNED) >= :from_height';
            $params[':from_height'] = (int)$filters['from_height'];
        }
        if (!empty($filters['to_height'])) {
            $sql .= ' AND CAST(height AS UNSIGNED) <= :to_height';
            $params[':to_height'] = (int)$filters['to_height'];
        }

        $multiMap = [
            'religion' => 'religion',
            'caste' => 'caste',
            'sect' => 'maslak',
            'mother_tongue' => 'mother_tongue',
            'education' => 'education',
            'occupation' => 'occupation',
            'country' => 'country',
            'state' => 'state',
            'city' => 'city',
            'body_type' => 'body_type',
            'complexion' => 'skin_tone',
        ];

        foreach ($multiMap as $inputKey => $column) {
            if (empty($filters[$inputKey]) || !is_array($filters[$inputKey])) {
                continue;
            }
            $vals = array_values(array_filter(array_map('trim', $filters[$inputKey]), static function ($v) {
                return $v !== '';
            }));
            if (empty($vals)) {
                continue;
            }

            $ph = [];
            foreach ($vals as $i => $val) {
                $p = ':' . $inputKey . '_' . $i;
                $ph[] = $p;
                $params[$p] = $val;
            }
            $sql .= ' AND ' . $column . ' IN (' . implode(',', $ph) . ')';
        }

        if (!empty($filters['photo_search'])) {
            $sql .= " AND (COALESCE(NULLIF(photo2_url,''), NULLIF(photo1_status,'')) IS NOT NULL)";
        }

        if (!empty($filters['name'])) {
            $k = '%' . trim((string)$filters['name']) . '%';
            $sql .= ' AND (first_name LIKE :name_k OR second_name LIKE :name_k)';
            $params[':name_k'] = $k;
        }

        $sql .= ' ORDER BY created_at DESC LIMIT 100';

        $stmt = $this->db->pdo()->prepare($sql);
        $stmt->execute($params);

        return $this->normalizeRows($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getProfile($id)
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM user_details WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    private function distinctList(string $column): array
    {
        $allowed = [
            'religion', 'caste', 'maslak', 'mother_tongue', 'education', 'occupation',
            'country', 'state', 'city', 'body_type', 'skin_tone'
        ];
        if (!in_array($column, $allowed, true)) {
            return [];
        }

        $sql = "SELECT DISTINCT {$column} AS v
                FROM user_details
                WHERE {$column} IS NOT NULL AND TRIM({$column}) <> ''
                ORDER BY {$column} ASC
                LIMIT 300";
        $stmt = $this->db->pdo()->query($sql);
        return array_map(static function ($r) {
            return (string)$r['v'];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function normalizeRows(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $obj = (object)$r;
            $obj->last_name = $r['second_name'] ?? '';
            $obj->avatar_path = (string)($r['photo2_url'] ?? $r['photo1_status'] ?? '');
            $obj->age = null;
            if (!empty($r['dob']) && $r['dob'] !== '0000-00-00') {
                try {
                    $obj->age = (new DateTime())->diff(new DateTime($r['dob']))->y;
                } catch (Exception $e) {
                    $obj->age = null;
                }
            }
            $out[] = $obj;
        }
        return $out;
    }
}
