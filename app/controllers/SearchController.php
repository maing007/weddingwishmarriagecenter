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
                'body_type' => $_POST['body_type'] ?? [],
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

        require VIEW_PATH . '/partials/header.php';
        require VIEW_PATH . 'home/frontend/profile_view.php';
        require VIEW_PATH . '/partials/footer.php';
    }

    public function handleSearchRequest()
    {
        $this->search();
    }
}
