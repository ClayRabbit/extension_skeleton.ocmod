<?php
/**
 * @author	Andrey Chesnakov
 * @link	https://clayrabbit.ru
 */

class ControllerExtensionModuleExtensionSkeleton extends Controller {
    
    const FILE = __FILE__;
    const VERSION = '1.0';

    private $route;
    private $name;
    private $type;
    private $error = array();
    private $data = array();
    private $token;
    private $settings = array(
        'status' => 0,
        //'sort_order' => '',
        //'geo_zone_id' => 0,
        //'tax_class_id' => 0,
        //'setting' => array(
        //),
        //'list' => array(
        //),
    );

    public function __construct($registry)
    {
        parent::__construct($registry);
        
        if (!isset($this->data)) {
            $this->data = array();
        }

        $this->type = basename(dirname($this::FILE));
        
        if ((float)VERSION >= 3) {
            $this->route = $this->type . '/' . basename($this::FILE, '.php');
            $this->name = str_replace("/", "_", $this->route);
        } else {
            $this->name = basename($this::FILE, '.php');
            $this->route = $this->type . '/' . $this->name;
        }
        
        if ((float)VERSION >= 2.3) {
            $this->route = "extension/" . $this->route;
        }

        // Load language and necessary models
        
        if ((float)VERSION >= 3) {
            $this->load->language('common/column_left');
            $this->load->language($this->route);
            $this->token = 'user_token';
        } else {
            if ((float)VERSION >= 2) {
                $this->data += $this->load->language('common/column_left');
            }
            $this->data += $this->load->language($this->route);
            $this->token = 'token';
        }
        
        $this->load->model('setting/setting');
        //$this->load->model($this->route);
        
        // Load settings and update if new settings found
        $updated = false;
        foreach ($this->settings as $key => $value) {
            $newkey = $this->name . '_' . $key;
            $this->settings[$newkey] = $this->config->get($newkey);
            if (is_null($this->settings[$newkey])) {
                $this->settings[$newkey] = $value;
                $updated = true;
            } elseif ($key == 'setting' OR $key == 'list') {
                foreach (array_keys($value) as $k) {
                    if (!isset($this->settings[$newkey][$k])) {
                        $this->settings[$newkey][$k] = $value[$k];
                        $updated = true;
                    }
                }
            }
            if ($key == 'list') {
                foreach (array_keys($this->settings[$newkey]) as $k) {
                    if (!isset($value[$k])) {
                        unset($this->settings[$newkey][$k]);
                        $updated = true;
                    }
                }
            }            
            unset($this->settings[$key]);
        }
        if ($updated) {
            $this->model_setting_setting->editSetting($this->name, $this->settings);
        }    
    }        
/*  
    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "iq_konkurs` (
          `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `product_id` int NOT NULL,
          `customer_id` int NOT NULL,
          `info` varchar(255) NOT NULL
          `date_added` datetime NOT NULL,
          `date_modified` datetime NOT NULL,
        ) DEFAULT CHARSET=utf8;");
    }
*/
/*
    public function uninstall() {
    }
*/
    public function index() {
        $this->document->setTitle($this->language->get('heading_title'));
        
        if ((float)VERSION >= 3) {
            $return_route = 'marketplace/extension';
        } elseif ((float)VERSION >= 2.3) {
            $return_route = 'extension/extension';
        } else {
            $return_route = 'extension/module';
        }
        $return_url = $this->url->link($return_route, $this->token . '=' . $this->session->data[$this->token] . '&type=' . $this->type, true);
        
        $settings = $this->model_setting_setting->getSetting($this->name);

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            
            foreach ($this->request->post AS $key => $value) {
                if ($key == $this->name . '_list') {
                    $value = $this->listSort($value);
                }
				if (isset($settings[$key])) {
					$this->model_setting_setting->editSettingValue($this->name, $key, $value);
				} else {
					if (!is_array($value)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$this->config->get('config_store_id') . "', `code` = '" . $this->db->escape($this->name) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$this->config->get('config_store_id') . "', `code` = '" . $this->db->escape($this->name) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value, true)) . "', serialized = '1'");
					}
				}
			}

            $this->session->data['success'] = $this->language->get('text_success');

            if (isset($this->request->post[$this->name . '_status'])) {
				$this->response->redirect($return_url);
			} else {
				$settings = $this->model_setting_setting->getSetting($this->name);
			}
        }

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['extension_id'] = $this->name;
        $this->data['extension_ver'] = $this::VERSION;

        $this->data['action'] = $this->url->link($this->route, $this->token . '=' . $this->session->data[$this->token], true);

        $this->data['cancel'] = $return_url;

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', $this->token . '=' . $this->session->data[$this->token], true)
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $return_url
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->data['action']
        );

        $on_off = array($this->language->get('text_disabled'), $this->language->get('text_enabled'));
        
        if ($settings) {
            $settings = $this->settingSort($settings);
            foreach ($settings as $key => $value) {
                $setting = substr($key, strlen($this->name) + 1);
                if (isset($this->request->post[$key])) {
                    $this->data['extension_settings'][$setting] = $this->request->post[$key];
                } else {
                    if (is_array($value)) {
                        array_walk_recursive($value, function (&$elem) {
                            $elem = htmlentities(html_entity_decode($elem, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                        });
					} elseif ($setting == 'status' AND !isset($this->data['setting_options'][$setting])) {
					        $this->data['setting_options'][$setting] = $on_off;
					} elseif ($setting == 'geo_zone_id' AND !isset($this->data['setting_options'][$setting])) {
					        $this->load->model('localisation/geo_zone');
					        $this->data['setting_options'][$setting] = array('0' => $this->language->get('text_all_zones'))
								+ array_column($this->model_localisation_geo_zone->getGeoZones(), 'name', 'geo_zone_id');
					} elseif ($setting == 'tax_class_id' AND !isset($this->data['setting_options'][$setting])) {
					        $this->load->model('localisation/tax_class');
					        $this->data['setting_options'][$setting] = array('0' => $this->language->get('text_none'))
								+ array_column($this->model_localisation_tax_class->getTaxClasses(), 'title', 'tax_class_id');
                    } else {
                        $value = htmlentities(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                    }                    
                    $this->data['extension_settings'][$setting] = $value;
                }
            }
            //$this->data['setting_options']['on_off_param'] = $on_off;
        }

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['column_left'] = $this->load->controller('common/column_left');
        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->route . ((float)VERSION < 2.2 ? '.tpl' : ''), $this->data));
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
    private function listSort($list = array()){
        $arr = $args = $result = array();
        
        foreach ($list as $name => $column) {
            $args[] = $column;
            $args[] = SORT_ASC;
            for ($i = 0; $i < count($column); $i++) {
                $arr[$i][$name] = $column[$i];
            }
        }
        $args[] = &$arr;
        call_user_func_array('array_multisort', $args);
        
        for ($i = 0; $i < count($arr); $i++) {
            foreach ($arr[$i] as $key => $val) {
                $result[$key][$i] = $val;
            }
        }
        
        return $result;
    }    
}
