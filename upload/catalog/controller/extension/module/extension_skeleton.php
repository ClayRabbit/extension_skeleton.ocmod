<?php

class ControllerExtensionModuleExtensionSkeleton extends Controller {
    
    const FILE = __FILE__;
    
    private $route;
    private $id;

    public function __construct($registry)
    {
        parent::__construct($registry);

        // Remove DIR_APPLICATION from file path to get route
        //$this->route = trim(substr(realpath__DIR__, strlen(DIR_APPLICATION)), '/');
        //
        // Remove "extension/" prefix and convert rest of the route to id
        //$this->id = str_replace("/", "_", substr($this->route, 10));
        
        $this->route = basename(dirname($this::FILE)) . '/' . basename($this::FILE, '.php');
        
        $this->id = str_replace("/", "_", $this->route);
        
        $this->route = "extension/" . $this->route;

        // Load language and necessary models
        //$this->load->language($this->route);
        //$this->load->model('setting/setting');
        //$this->load->model($this->route);
    }        

    public function index() {
        
    }
}
