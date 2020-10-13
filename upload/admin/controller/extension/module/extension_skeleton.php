<?php

class ControllerExtensionModuleExtensionSkeleton extends Controller {
    
    const FILE = __FILE__;
    
    private $route;
    private $id;
    private $type;
    private $error = array();

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->type = basename(dirname($this::FILE));
        
        $this->route = $this->type . '/' . basename($this::FILE, '.php');
        
        $this->id = str_replace("/", "_", $this->route);
        
        $this->route = "extension/" . $this->route;

        // Load language and necessary models
        $this->load->language('common/column_left');
        $this->load->language($this->route);
        $this->load->model('setting/setting');
        //$this->load->model($this->route);
    }        
  
    public function install() {
        /*
        if ($this->config->get($this->id . '_status') === null) {
            $this->model_setting_setting->editSetting($this->id, array(
                $this->id . '_status' => 0,
                //$this->id . '_sort_order' => '',
                //$this->id . '_geo_zone_id' => 0,
                //$this->id . '_tax_class_id' => 0,
                //$this->id . '_setting' => array(),
                //$this->id . '_list' => array(),
            ));
        }
        */
        /*
        if ($this->config->get($this->id . '_setting') === null) {
            $this->model_setting_setting->editSettingValue($this->id, $this->id . '_setting', array(
                'param' => 0;
            ));
        }
        */
        /*
        if ($this->config->get($this->id . '_list') === null) {
            $this->model_setting_setting->editSettingValue($this->id, $this->id . '_list', array(
                array('column1_name',    'column2_name', ),
                array('column1_value',   'column2_value', ),
            ));
        }
        */
    }
/*
    public function uninstall() {
    }
*/
    public function index() {
        $this->document->setTitle($this->language->get('heading_title'));
        
        $return_url = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=' . $this->type, true);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting($this->id, $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($return_url);
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['extension_id'] = $this->id;

        $data['action'] = $this->url->link($this->route, 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $return_url;

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $return_url
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $data['action']
        );


        if ($settings = $this->model_setting_setting->getSetting($this->id)) {
            $settings = $this->settingSort($settings);
            foreach ($settings as $key => $value) {
                $setting = substr($key, strlen($this->id) + 1);
                if (isset($this->request->post[$key])) {
                    $data['extension_settings'][$setting] = $this->request->post[$key];
                } else {
                    if (is_array($value)) {
                        array_walk_recursive($value, function (&$elem) {
                            $elem = htmlentities(html_entity_decode($elem, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                        });
					} elseif ($setting == 'status' AND !isset($data['setting_options']['status'])) {
					        $data['setting_options']['status'] = array($this->language->get('text_disabled'), $this->language->get('text_enabled'));
					} elseif ($setting == 'geo_zone_id' AND !isset($data['setting_options']['geo_zone_id'])) {
					        $this->load->model('localisation/geo_zone');
					        $data['setting_options']['geo_zone_id'] = array('0' => $this->language->get('text_all_zones'))
								+ array_column($this->model_localisation_geo_zone->getGeoZones(), 'name', 'geo_zone_id');
					} elseif ($setting == 'tax_class_id' AND !isset($data['setting_options']['tax_class_id'])) {
					        $this->load->model('localisation/tax_class');
					        $data['setting_options']['tax_class_id'] = array('0' => $this->language->get('text_none'))
								+ array_column($this->model_localisation_tax_class->getTaxClasses(), 'title', 'tax_class_id');
                    } else {
                        $value = htmlentities(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                    }                    
                    $data['extension_settings'][$setting] = $value;
                }
            }
            //$data['setting_options']['on_off_param'] = $data['setting_options']['status'];
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->route, $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
    
    private function settingSort($settings = array()){
		uksort($settings, function ($a, $b) {
            if (substr($a, -5) == '_list') {
                return 1;
            } elseif (substr($b, -5) == '_list') {
                return -1;
            } elseif (substr($a, -8) == '_setting') {
                return 1;
            } elseif (substr($b, -8) == '_setting') {
                return -1;
            }
            return strcmp($a, $b);
        });
        return $settings;
    }
}
