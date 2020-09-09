<?php

namespace controllers;

use \engine\SiteInfo;
use \models\CPanel as CPanel;

class CPanelController extends Controller
{
    protected $path;
    
    public function __construct()
    {
        parent::__construct();
        $file = pathinfo(__FILE__, PATHINFO_FILENAME);
        $this->path = $this->getDirectory($file);
        $this->model = new CPanel();
    }

    public function index()
    {
        $out['sessions'] = array();
        $out['sessions']['today'] = 0;
        $out['sessions']['week'] = 0;
        $out['sessions']['alltime'] = 0;
        $visits = $this->model->getVisits();
        
        foreach ($visits as $k => $v) {
            // Today
            if ($v['date'] === date('Y-m-d')) $out['sessions']['today'] = $v['session']; 
            // Week
            if ($v['date'] <= date('Y-m-d') && $v['date'] >= date('Y-m-d', strtotime('-7 days'))) $out['sessions']['week'] += $v['session'];
            // All time
            $out['sessions']['alltime'] += $v['session'];
        }
        $cpanel = $this->getFile($this->path, __FUNCTION__);
        echo $this->callTemplate($cpanel, $out);
    }

    public function header()
    {
        $header = $this->getFile($this->path, __FUNCTION__);
        $siteInfo = new SiteInfo();
        $out = array();
        $out['sitename'] = $siteInfo->getName();
        echo $this->callTemplate($header, $out);
    }

    public function footer()
    {
        $footer = $this->getFile($this->path, __FUNCTION__);
        $out = array();
        $out['debug_mode'] = $this->config_flags->debug_mode;
        echo $this->callTemplate($footer, $out);
    }

    public function postsIndex()
    {
        $cpanel = $this->getFile($this->path, __FUNCTION__);
        $out = array();
        $out['post_list'] = $this->model->getPosts();
        echo $this->callTemplate($cpanel, $out);
    }

    public function categoriesIndex()
    {
        $cpanel = $this->getFile($this->path, __FUNCTION__);
        $out = array();
        $out['categories_list'] = $this->model->getCategories();
        echo $this->callTemplate($cpanel, $out);
    }

    public function postEditor()
    {
        $postCreate = $this->getFile($this->path, __FUNCTION__);
        $out = array();
        $home = new \models\Home();
        if (!empty($_GET['id'])) {
            $out['post_id'] = $_GET['id'];
            $out['post'] = $home->getPost($_GET['id']);
        }
        $out['categories'] = $home->getCategories();
        $out['author'] = $_SESSION['users']['username'];
        $out['debug_mode'] = $this->config_flags->debug_mode;
        echo $this->callTemplate($postCreate, $out); 
    }

    public function categoryEditor()
    {
        $categoryCreate = $this->getFile($this->path, __FUNCTION__);
        $out = array();
        $home = new \models\Home();
        if (!empty($_GET['id'])) {
            $out['category_id'] = $_GET['id'];
            $out['category'] = $home->getCategory($_GET['id']);
        }
        $out['debug_mode'] = $this->config_flags->debug_mode;
        echo $this->callTemplate($categoryCreate, $out); 
    }

    public function postEditorSubmit()
    {
        if (!empty($_GET['id'])) {
            $post = array();
            $post['title'] = $_POST['title'];
            $post['category'] = $_POST['category'];
            $post['short_content'] = $_POST['short_content'];
            $post['content'] = $_POST['content'];
            $this->model->editPost($_GET['id'], $post);
        } else {
            $this->model->createPost($_POST);
        }
        
        $cpanel = $this->getFile($this->path, 'postsIndex');
        $out = array();
        $out['post_list'] = $this->model->getPosts();
        echo $this->callTemplate($cpanel, $out);
    }

    public function categoryEditorSubmit()
    {
        if (!empty($_GET['id'])) {
            $category = array();
            $category['name'] = $_POST['name'];
            $this->model->editCategory($_GET['id'], $category);
        } else {
            $this->model->createCategory($_POST);
        }
        
        $cpanel = $this->getFile($this->path, 'categoriesIndex');
        $out = array();
        $out['categories_list'] = $this->model->getCategories();
        echo $this->callTemplate($cpanel, $out);
    }

    public function postDelete()
    {
        $post_id = $_GET['id'];
        $this->model->deletePost($post_id);
        $cpanel = $this->getFile($this->path, 'postsIndex');
        $out = array();
        $out['post_list'] = $this->model->getPosts();
        echo $this->callTemplate($cpanel, $out);
    }

    public function categoryDelete()
    {
        $category_id = $_GET['id'];
        $this->model->deleteCategory($category_id);
        $cpanel = $this->getFile($this->path, 'categoriesIndex');
        $out = array();
        $out['categories_list'] = $this->model->getCategories();
        echo $this->callTemplate($cpanel, $out);
    }
}