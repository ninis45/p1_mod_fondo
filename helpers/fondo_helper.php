<?php defined('BASEPATH') OR exit('No direct script access allowed.');

function option_actividades()
{
    $result = ci()->db->get('cat_actividad_poa')->result();
    $array  = array();
    
    foreach($result as $actividad)
    {
        $array[$actividad->id] =  $actividad->no_actividad.'-'.$actividad->nombre;
    }
    
    return $array;
}
function pref_partida($text='')
{
    
    return substr($text,0,2);
    
}

function pref_centro($text='',$pref='02')
{
    
    
    
    return $pref.substr($text,-3,-1);
    
}
function pref_actividad($text='')
{
    
    
    
    return substr($text,1,3);
    
}


function es_activo($anio,$mes)
{
        ci()->config->load('fondo');
        //$anio  = $this->input->post('anio');
        $corte = ci()->config->item('corte');
        
        $mes_fondo  = strtotime($anio.'-'.$mes.'-01');
        $mes_actual = strtotime(date('Y').'-'.date('m').'-01');
        
        
        if($mes_actual > $mes_fondo)
        {
            return false;
        }
        
        return true;
    
}



?>