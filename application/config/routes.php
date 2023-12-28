<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//$route['default_controller'] 								= 'main_control';
$route['default_controller']                                = 'Cuponing_ctrl/login_ui';
$route['404_override'] 										= '';
$route['translate_uri_dashes'] 								= FALSE;
//$route['(:any)'] 											= 'main_control/access_type/$1';
$route['(:any)']                                            = 'mpdi/Mpdi_ctrl/home';

$route['cupon/login_ui']                                    = 'Cuponing_ctrl/login_ui';
// cuponing-------------------------------------------------------------------------

 
