<?php defined('BASEPATH') or exit('No direct script access allowed');

class Fondo_m extends MY_Model {

	private $folder;

	public function __construct()
	{
		parent::__construct();
		$this->_table = 'default_fondo';
        $this->load->library('files/files');
		
	}
    
    function get_fondo($id=0,$select='*')
    {
       
        ///$result = $this->join('cat_conceptos','cat_conceptos.id=fondo.id_concepto')->get_by('fondo.id',$id);
        
        $result = $this->select($select)->get_by('fondo.id',$id);
        if($result)
        {
            $file_xml = Files::get_file($result->xml);
            $file_pdf = Files::get_file($result->pdf);
            
            
            $result->attach = array(
                 'pdf' => $file_pdf['status']?$file_pdf['data']:false,
                 'xml' => $file_xml['status']?$file_xml['data']:false
            );
        }
        
        return $result;
    }
    function reporte($base_where)
    {
        
            
        $items=$this->select('*,fondo.id AS id,centros.nombre AS nombre_centro,directores.nombre AS nombre_director,centros.tipo AS tipo_centro')
                ->order_by('ordering_count')
                ->where($base_where)
                ->join('fondo_partidas','fondo_partidas.id=fondo.id_partida')
                ->join('centros','centros.id=fondo.id_centro')
                ->join('directores','directores.id=fondo.id_director')//Tenia left
                ->join('cat_actividad_poa','cat_actividad_poa.id=fondo.id_actividad_poa')
                ->join('cat_conceptos','cat_conceptos.id=fondo.id_concepto')
                ->join('proveedores','proveedores.id=fondo.id_proveedor')
                
                
                ->get_all();
                
                
        return $items;
    }
    /*function updated($id,$input)
    {
        
        
        
       
        return parent::update($id,$data);
        
    }*/
    /*function create($input)
    {
        $data=array(
            'id_proveedor'     => $input['id_proveedor'],            
            'id_actividad_poa' => $input['id_actividad_poa'],
            'id_concepto' => $input['id_concepto'],
            'id_partida' => $input['id_partida'],
            'id_centro' => $input['id_centro'],
            'id_director'      => $input['id_director'],
            'motivo'           => $input['autorizado']?NULL:$input['motivo'],
            'mes'       => $input['mes'],
            'no_factura'       => $input['no_factura'],
            'concepto'       => $input['concepto'],
            'importe'          => str_replace(',','',$input['importe']),
            
            
            'xml'              => isset($input['xml'])?$input['xml']:NULL,
            'pdf'              => isset($input['pdf'])?$input['pdf']:NULL,
                
            'estatus' => $input['estatus'],
            'created_on' => now()
        );
        
        
        return $this->insert($data);
    }*/
 }
 ?>