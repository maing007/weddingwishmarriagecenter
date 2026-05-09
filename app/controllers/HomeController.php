<?php

class HomeController extends Controller
{
    public function index(): void
    {
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();
        $recentProfiles = $userModel->getRecentPublicMembers(8);

        $this->view('home/index', [
            'title' => 'Home',
            'message' => 'Welcome to the Core PHP MVC Example!',
            'phone' => '',
            'countryCode' => '',
            'recentProfiles' => $recentProfiles,
        ]);
    }

        public function membership(): void
    {
        $this->view('home/frontend/membership', [
            'title' => 'member ship',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }

    public function About(): void
    {
        $this->view('home/frontend/about', [
            'title' => 'About Us',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }
  public function child(): void
    {
        $this->view('home/frontend/report-misuse', [
            'title' => 'Report Misuse',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }
     public function search(): void
    {
        $this->view('home/frontend/search', [
            'title' => 'Search',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }
     public function demograph(): void
    {
        $this->view('home/frontend/demograph', [
            'title' => 'Demograph',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }
     public function privacy(): void
    {
        $this->view('home/frontend/privacy', [
            'title' => 'Privacy Policy',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }
    public function Faq(): void
    {
        $this->view('home/frontend/faq', [
            'title' => 'FAQ',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }
     public function register(): void
    {
        $this->view('home/frontend/register', [
            'title' => 'register',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }
     public function carees(): void
    {
        require __DIR__ . '/../views/home/frontend/carees/carees_header.php';
         require __DIR__ . '/../views/home/frontend/carees/carees.php';
         require __DIR__ . '/../views/home/frontend/carees/carees_footer.php';
    }
      public function sucess1(): void
    {
        $this->view('home/frontend/Sucess-stories/success1', [
            'title' => 'sucess1',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }
      public function sucess2(): void
    {
        $this->view('home/frontend/Sucess-stories/success2', [
            'title' => 'sucess2',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }
      public function sucess3(): void
    {
        $this->view('home/frontend/Sucess-stories/success3', [
            'title' => 'sucess3',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }
    public function contact(): void
    {
        $this->view('home/frontend/contact', [
            'title' => 'Contact Us',
            'message' => 'This is a simple MVC backend built with plain PHP and MySQL (no frameworks).'
        ]);
    }

    public function notFound(): void
    {
        $this->view('home/frontend/404', [
            'title' => '404 Not Found',
            'message' => 'The page you are looking for could not be found.'
        ]);
    }
}
