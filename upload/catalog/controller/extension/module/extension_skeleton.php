<?php

class ControllerExtensionModuleExtensionSkeleton extends Controller {
    
    const FILE = __FILE__;
    
    private $route;
    private $id;

    public function __construct($registry)
    {
        parent::__construct($registry);
        
        $this->route = basename(dirname($this::FILE)) . '/' . basename($this::FILE, '.php');
        
        $this->id = str_replace("/", "_", $this->route);
        
        $this->route = "extension/" . $this->route;

        // Load language and necessary models
        //$this->load->language($this->route);
        //$this->load->model($this->route);
    }        

    public function index() {
        
    }
}
