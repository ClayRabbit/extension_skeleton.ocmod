<?php

class ControllerExtensionModuleExtensionSkeleton extends Controller {
    
    const FILE = __FILE__;
    const VERSION = '1.0';

    private $route;
    private $id;
    private $type;
    private $error = array();
    private $data = array();
    private $token;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->type = basename(dirname($this::FILE));
        
        if ((float)VERSION >= 3) {
            $this->route = $this->type . '/' . basename($this::FILE, '.php');
            $this->id = str_replace("/", "_", $this->route);
        } else {
            $this->id = basename($this::FILE, '.php');
            $this->route = $this->type . '/' . $this->id;
        }
        
        $this->route = "extension/" . $this->route;

        // Load language and necessary models
        
        if ((float)VERSION >= 3) {
            $this->load->language('common/column_left');
            $this->load->language($this->route);
            $this->token = 'user_token';
        } else {
            $this->data += $this->load->language('common/column_left');
            $this->data += $this->load->language($this->route);
            $this->token = 'token';
        }
        
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
                'param' => 0,
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
        $data = $this->data;
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $return_url = $this->url->link(((float)VERSION >= 3 ? 'marketplace' : 'extension') . '/extension', $this->token . '=' . $this->session->data[$this->token] . '&type=' . $this->type, true);
        
        $settings = $this->model_setting_setting->getSetting($this->id);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            
            foreach ($this->request->post AS $key => $value) {
				if (isset($settings[$key])) {
					$this->model_setting_setting->editSettingValue($this->id, $key, $value);
				} else {
					if (!is_array($value)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$this->config->get('config_store_id') . "', `code` = '" . $this->db->escape($this->id) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$this->config->get('config_store_id') . "', `code` = '" . $this->db->escape($this->id) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value, true)) . "', serialized = '1'");
					}
				}
			}

            $this->session->data['success'] = $this->language->get('text_success');

            if (isset($this->request->post[$this->id . '_status'])) {
				$this->response->redirect($return_url);
			} else {
				$settings = $this->model_setting_setting->getSetting($this->id);
			}
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['extension_id'] = $this->id;
        $data['extension_ver'] = $this::VERSION;

        $data['action'] = $this->url->link($this->route, $this->token . '=' . $this->session->data[$this->token], true);

        $data['cancel'] = $return_url;

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->token . '=' . $this->session->data[$this->token], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $return_url
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $data['action']
        );

        $on_off = array($this->language->get('text_disabled'), $this->language->get('text_enabled'));
        
        if ($settings) {
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
					} elseif ($setting == 'status' AND !isset($data['setting_options'][$setting])) {
					        $data['setting_options'][$setting] = $on_off;
					} elseif ($setting == 'geo_zone_id' AND !isset($data['setting_options'][$setting])) {
					        $this->load->model('localisation/geo_zone');
					        $data['setting_options'][$setting] = array('0' => $this->language->get('text_all_zones'))
								+ array_column($this->model_localisation_geo_zone->getGeoZones(), 'name', 'geo_zone_id');
					} elseif ($setting == 'tax_class_id' AND !isset($data['setting_options'][$setting])) {
					        $this->load->model('localisation/tax_class');
					        $data['setting_options'][$setting] = array('0' => $this->language->get('text_none'))
								+ array_column($this->model_localisation_tax_class->getTaxClasses(), 'title', 'tax_class_id');
                    } else {
                        $value = htmlentities(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                    }                    
                    $data['extension_settings'][$setting] = $value;
                }
            }
            //$data['setting_options']['on_off_param'] = $on_off;
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
