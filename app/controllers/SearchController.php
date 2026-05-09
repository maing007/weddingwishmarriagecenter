<?php

require_once __DIR__ . '/../models/SearchModel.php';

class SearchController
{
    private $model;

    public function __construct()
    {
        $this->model = new SearchModel();
    }

    public function index()
    {
        $keyword = trim((string)($_GET['q'] ?? ''));
        if ($keyword !== '') {
            $profiles = $this->model->keywordSearch($keyword);
            $title = 'Search results';
            require VIEW_PATH . '/partials/header.php';
            require VIEW_PATH . 'home/frontend/search_results.php';
            require VIEW_PATH . '/partials/footer.php';
            return;
        }

        $title = 'Search';
        $filterOptions = $this->model->getFilterOptions();
        $religions = $filterOptions['religions'];
        $sects = $filterOptions['sects'];
        $mother_tongues = $filterOptions['mother_tongues'];
        $education = $filterOptions['educations'];
        $occupations = $filterOptions['occupations'];
        $countries = $filterOptions['countries'];
        $states = $filterOptions['states'];
        $cities = $filterOptions['cities'];
        $areas = $filterOptions['areas'];
        $house_types_list = $filterOptions['house_types'];
        $body_types = $filterOptions['body_types'];
        $complexions = $filterOptions['complexions'];
        $annual_incomes = $filterOptions['annual_incomes'];
        require VIEW_PATH . '/partials/header.php';
        require VIEW_PATH . 'home/frontend/search.php';
        require VIEW_PATH . '/partials/footer.php';
    }

    public function search()
    {
        $mode = (string)($_POST['search_mode'] ?? 'advanced');
        $profiles = [];

        if ($mode === 'id') {
            $profiles = $this->model->searchById((string)($_POST['txt_id_search'] ?? ''));
        } elseif ($mode === 'name') {
            $profiles = $this->model->searchByName((string)($_POST['keyword'] ?? ''));
        } else {
            $filters = [
                'gender' => $_POST['gender'] ?? null,
                'from_age' => $_POST['from_age'] ?? null,
                'to_age' => $_POST['to_age'] ?? null,
                'from_height' => $_POST['from_height'] ?? null,
                'to_height' => $_POST['to_height'] ?? null,
                'religion' => $_POST['religion'] ?? [],
                'caste' => $_POST['caste'] ?? [],
                'sect' => $_POST['sect'] ?? [],
                'mother_tongue' => $_POST['mother_tongue'] ?? [],
                'education' => $_POST['education'] ?? [],
                'occupation' => $_POST['occupation'] ?? [],
                'country' => $_POST['country'] ?? [],
                'state' => $_POST['state'] ?? [],
                'city' => $_POST['city'] ?? [],
                'area' => $_POST['house_area'] ?? [],
                'house_type' => $_POST['house_type'] ?? null,
                'house_size_from' => $_POST['house_size_from'] ?? null,
                'house_size_to' => $_POST['house_size_to'] ?? null,
                'marital_status' => $_POST['looking_for'] ?? [],
                'employed_in' => $_POST['employee_in'] ?? [],
                'annual_income' => $_POST['income'] ?? [],
                'eating_habits' => $_POST['diet'] ?? [],
                'drinking' => $_POST['drink'] ?? [],
                'smoking' => $_POST['smoking'] ?? [],
                'body_type' => $_POST['bodytype'] ?? [],
                'complexion' => $_POST['complexion'] ?? [],
                'photo_search' => $_POST['photo_search'] ?? null,
                'name' => $_POST['name'] ?? null,
            ];
            $profiles = $this->model->advancedSearch($filters);
        }

        $title = 'Search results';
        require VIEW_PATH . '/partials/header.php';
        require VIEW_PATH . 'home/frontend/search_results.php';
        require VIEW_PATH . '/partials/footer.php';
    }

    public function profile($id)
    {
        $profile = $this->model->getProfile($id);
        if (!$profile) {
            die('Profile not found');
        }

        $title = trim(($profile->first_name ?? '') . ' ' . ($profile->second_name ?? ''));
        if ($title === '') {
            $title = 'Profile';
        }

        require VIEW_PATH . '/partials/header.php';
        require VIEW_PATH . 'home/frontend/profile_view.php';
        require VIEW_PATH . '/partials/footer.php';
    }

    public function handleSearchRequest()
    {
        $this->search();
    }
}
