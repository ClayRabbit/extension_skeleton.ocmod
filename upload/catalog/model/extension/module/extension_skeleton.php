<?php

class ModelExtensionModuleExtensionSkeleton extends Model {
    
    const FILE = __FILE__;
    
    private $route;
    private $id;
    
    public function __construct($registry)
    {
        parent::__construct($registry);
        
        $this->route = basename(dirname($this::FILE)) . '/' . basename($this::FILE, '.php');
        
        $this->id = str_replace("/", "_", $this->route);
        
        $this->route = "extension/" . $this->route;

        //$this->load->model('setting/setting');
    }   
    
	//public function functionName() {
		//$enabled = $this->config->get($this->id . '_status');
        //$setting = $this->config->get($this->id . '_setting');

        //$this->load->language($this->route);
    //}
}
