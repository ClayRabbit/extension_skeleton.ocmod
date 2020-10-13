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
            ));
        }
        */
        /*
        if ($this->config->get($this->id . '_setting') === null) {
            $this->model_setting_setting->editSetting($this->id, array($this->id . '_setting' => ''));
            //$this->model_setting_setting->editSettingValue($this->id, $this->id . '_setting', array('key' => 'value'));
        }
        */
        /*
        if ($this->config->get($this->id . '_list') === null) {
            $this->model_setting_setting->editSettingValue($this->id, $this->id . '_list', array(
                array('column1', 'column2', ),
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


        if ($setting = $this->model_setting_setting->getSetting($this->id)) {
            foreach ($setting as $key => $value) {
                $setting = substr($key, strlen($this->id) + 1);
                if (isset($this->request->post[$key])) {
                    $data['extension_settings'][$setting] = $this->request->post[$key];
                } else {
                    if (is_array($value)) {
                        array_walk_recursive($value, function (&$elem) {
                            $elem = htmlentities(html_entity_decode($elem, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                        });
                    } else {
                        $value = htmlentities(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                    }                    
                    $data['extension_settings'][$setting] = $value;
                }
            }
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
}
