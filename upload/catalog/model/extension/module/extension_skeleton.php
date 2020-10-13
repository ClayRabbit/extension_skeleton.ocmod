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
    /* for shipping module
    function getQuote($address) {
        if (!$this->config->get($this->id . '_geo_zone_id')) {
            $status = true;
        } else {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get($this->id . '_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
            if ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        }

        $method = basename($this->route);
        $quote_data = array();

        if ($status) {
            $this->load->language($this->route);

            $weight = $this->cart->getWeight();

            $cost = '';
            
            // ...

            if ((string)$cost != '') {
                $quote_data[$method] = array(
                    'code'         => "$method.$method",
                    'title'        => $this->language->get('text_description'),
                    'cost'         => $cost,
                    'tax_class_id' => $this->config->get($this->id . '_tax_class_id'),
                    'text'         => $this->currency->format($this->tax->calculate($cost, $this->config->get($this->id . '_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
                );
            }
        }
        
        $method_data = array();

        if ($quote_data) {
            $method_data = array(
                    'code'       => $method,
                    'title'      => $this->language->get('text_title'),
                    'quote'      => $quote_data,
                    'sort_order' => $this->config->get($this->id . '_sort_order'),
                    'error'      => false
            );
        }

        return $method_data;
    }
    */
    /*
	public function functionName() {
		$enabled = $this->config->get($this->id . '_status');
        $setting = $this->config->get($this->id . '_setting');

        $this->load->language($this->route);
    }
    */
}
