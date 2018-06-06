<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
$route['fondo/p(/:num)?']           = 'fondo/index$1';

$route['fondo/admin/partidas/(:num)/(:any)']			= 'admin_partidas/load/$1/$2';
$route['fondo/admin/partidas/(:num)']			= 'admin_partidas/load/$1';
$route['fondo/admin/partidas(/:any)?']			= 'admin_partidas$1';

$route['fondo/admin/(:num)/(:any)']			= 'admin/load/$1/$2';

$route['fondo/admin/(:num)']			= 'admin/load/$1';



?>