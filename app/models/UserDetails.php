<?php

class UserDetails
{
    protected $db;

    /** Columns allowed on INSERT (excludes id, created_at). */
    private const INSERTABLE_COLUMNS = [
        'lead', 'gender', 'first_name', 'second_name', 'email', 'password', 'mobile_number', 'phone', 'time_to_call',
        'contact_person_name', 'contact_person_relation', 'marital_status', 'total_children', 'status_children',
        'mother_tongue', 'language_known', 'dob', 'religion', 'maslak', 'caste', 'sub_caste', 'education', 'employed_in',
        'annual_income', 'occupation', 'designation', 'work_detail', 'registration_fee', 'final_fee', 'country', 'state',
        'city', 'area', 'address', 'location_pin', 'house_type', 'house_size', 'house_size_marla', 'residence', 'height',
        'weight', 'eating_habits', 'smoking', 'drinking', 'body_type', 'skin_tone', 'blood_group', 'about_us', 'hobby',
        'birth_place', 'birth_time', 'profile_by', 'reference', 'family_type', 'father_name', 'father_occupation',
        'mother_name', 'mother_occupation', 'family_status', 'no_of_brothers', 'no_of_married_brother', 'no_of_sisters',
        'no_of_married_sister', 'family_details', 'looking_for', 'partner_complexion', 'partner_from_age', 'partner_to_age',
        'partner_from_height', 'partner_to_height', 'partner_body_type', 'partner_eating_habit', 'partner_smoking_habit',
        'partner_drinking_habit', 'partner_mother_tongue', 'expectations', 'partner_religion', 'partner_caste',
        'partner_caste_exception', 'partner_manglik', 'partner_star', 'partner_sect', 'partner_maslak',
        'partner_maslak_exception', 'partner_denomination', 'partner_division', 'partner_gotra', 'partner_education',
        'partner_employed_in', 'partner_occupation', 'partner_designation', 'partner_annual_income', 'partner_country',
        'partner_state', 'partner_city', 'partner_country_exception', 'partner_area', 'partner_house_size_from',
        'partner_house_size_to', 'partner_residence_status', 'photo1_status', 'photo_visibility', 'photo2_url', 'photo3_url',
        'photo4_url', 'photo5_url', 'photo6_url', 'id_proof_status', 'id_proof_file', 'cv_file', 'country_code',
        'user_status', 'featured_status', 'matri_id',
    ];

    public function __construct()
    {
        $this->db = Database::getInstance()->pdo();
    }

    public function emailExists(string $email, ?int $exceptUserId = null): bool
    {
        $email = strtolower(trim($email));
        if ($email === '' || strpos($email, '@') === false) {
            return false;
        }
        $sql = 'SELECT 1 FROM user_details WHERE LOWER(TRIM(email)) = :e LIMIT 1';
        $params = [':e' => $email];
        if ($exceptUserId !== null && $exceptUserId > 0) {
            $sql = 'SELECT 1 FROM user_details WHERE LOWER(TRIM(email)) = :e AND id != :id LIMIT 1';
            $params[':id'] = $exceptUserId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * @return array<string, mixed>
     */
    public function filterDataForInsert(array $data): array
    {
        $allowed = array_flip(self::INSERTABLE_COLUMNS);
        $out = [];
        foreach ($data as $key => $value) {
            if (!isset($allowed[$key])) {
                continue;
            }
            if (is_array($value)) {
                $value = json_encode(array_values($value), JSON_UNESCAPED_UNICODE);
            }
            if ($value === '') {
                $value = null;
            }
            $out[$key] = $value;
        }

        return $out;
    }

    /**
     * @return array{ok:bool, id:?int, error:string}
     */
    public function insertSafe(array $data): array
    {
        $row = $this->filterDataForInsert($data);
        if ($row === []) {
            return ['ok' => false, 'id' => null, 'error' => 'No valid fields to save.'];
        }

        $columns = array_keys($row);
        $colsSql = implode(', ', array_map(static function ($c) {
            return '`' . str_replace('`', '', $c) . '`';
        }, $columns));
        $placeholders = implode(', ', array_map(static function ($c) {
            return ':' . $c;
        }, $columns));
        $sql = "INSERT INTO user_details ({$colsSql}) VALUES ({$placeholders})";

        try {
            $stmt = $this->db->prepare($sql);
            foreach ($row as $key => $value) {
                if ($value === null) {
                    $stmt->bindValue(':' . $key, null, PDO::PARAM_NULL);
                } else {
                    $stmt->bindValue(':' . $key, $value);
                }
            }
            if (!$stmt->execute()) {
                return ['ok' => false, 'id' => null, 'error' => 'Database could not save this profile. Please try again.'];
            }
        } catch (PDOException $e) {
            return ['ok' => false, 'id' => null, 'error' => $this->mapPdoException($e)];
        }

        $id = (int) $this->db->lastInsertId();

        return ['ok' => $id > 0, 'id' => $id > 0 ? $id : null, 'error' => $id > 0 ? '' : 'Member was not assigned an ID.'];
    }

    private function mapPdoException(PDOException $e): string
    {
        $code = $e->errorInfo[1] ?? null;
        $state = (string) ($e->errorInfo[0] ?? '');
        $msg = $e->getMessage();
        if ($code === 1062 || ($state === '23000' && stripos($msg, 'Duplicate') !== false)) {
            return 'This email is already registered. Use a different email or edit the existing member.';
        }
        if ($state === '22007' || $code === 1366 || stripos($msg, 'Incorrect') !== false) {
            return 'A date, time, or text value is invalid. Check date of birth, birth time, and numeric fields.';
        }
        if ($state === '01000' || stripos($msg, 'Data truncated') !== false) {
            return 'One of the values is too long or not allowed for its field (e.g. gender or dropdown).';
        }

        return 'Could not save member: please review all steps and try again.';
    }

    public function create($data)
    {
        $r = $this->insertSafe($data);

        return $r['ok'] ? $r['id'] : false;
    }
}
