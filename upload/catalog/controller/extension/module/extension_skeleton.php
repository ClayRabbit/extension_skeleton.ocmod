<?php

class ControllerExtensionModuleExtensionSkeleton extends Controller {
    
    const FILE = __FILE__;
    
    private $route;
    private $id;

    public function __construct($registry)
    {
        parent::__construct($registry);
        
        if ((float)VERSION >= 3) {
            $this->route = basename(dirname($this::FILE)) . '/' . basename($this::FILE, '.php');
            $this->id = str_replace("/", "_", $this->route);
        } else {
            $this->id = basename($this::FILE, '.php');
            $this->route = basename(dirname($this::FILE)) . '/' . $this->id;
        }
        
        $this->route = "extension/" . $this->route;

        // Load language and necessary models

        if ((float)VERSION >= 3) {
            //$this->load->language($this->route);
        } else {
            //$this->data += $this->load->language($this->route);
        }

        //$this->load->model($this->route);
    }        

    public function index() {
        
    }
}
