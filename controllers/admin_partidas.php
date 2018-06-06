<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Blog Fields
 *
 * Manage custom blogs fields for
 * your blog.
 *
 * @author 		PyroCMS Dev Team
 * @package 	PyroCMS\Core\Modules\Users\Controllers
 */
class Admin_partidas extends Admin_Controller {

	protected $section = 'partidas';

	// --------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
        
        role_or_die('fondo', 'admin_fondo_partidas');
        
        $this->lang->load('fondo');
        $this->load->model(array('conceptos/conceptos_m','partidas_m','fondo_m'));
        $this->template->enable_parser(true);
        $this->rules = array(
			
			array(
				'field' => 'nombre',
				'label' => 'Nombre',
				'rules' => 'trim|required'
				),
            array(
				'field' => 'id_actividad_poa',
				'label' => 'Actividad POA',
				'rules' => 'integer|required'
				),
		    array(
				'field' => 'id_concepto',
				'label' => 'Concepto',
				'rules' => 'integer|required'
				),
			array(
				'field' => 'no_partida',
				'label' => 'Nó. de Partida',
				'rules' => 'trim|required'
				),
            array(
				'field' => 'descripcion',
				'label' => 'Descripción',
				'rules' => 'trim'
				)
		);
    }
    
    function load($anio=false)
    {
        $base_where=array();
        
        $anio or redirect('admin/fondo');
        
        $base_where['anio']= $anio;
        
        $this->input->get('f_concepto') AND $base_where['id_concepto']= $this->input->get('f_concepto');
        $this->input->get('f_keywords') AND $base_where['(default_fondo_partidas.nombre LIKE "%'.$this->input->get('f_keywords').'%" OR default_fondo_partidas.descripcion LIKE "%'.$this->input->get('f_keywords').'%")']= NULL;
        
        
        
        	// Create pagination links
		$total_rows = $this->partidas_m->join('cat_conceptos','cat_conceptos.id=fondo_partidas.id_concepto')->count_by($base_where);
		$pagination = create_pagination('admin/fondo/partidas/'.$anio, $total_rows,10,5);
        
        
        //print_r($pagination);
        
        
        $items = $this->partidas_m->select('*,fondo_partidas.id AS id,fondo_partidas.nombre AS nombre')
                        ->join('cat_conceptos','cat_conceptos.id=fondo_partidas.id_concepto')
                        
                        ->limit($pagination['limit'],$pagination['offset'])
                        ->get_many_by($base_where);
        
        //$conceptos = $this->conceptos_m->select('id,CONCAT(no_concepto,"-",nombre)AS nombre',false)->dropdown('id','nombre');
        
        //print_r($conceptos);
        //exit();
       	$this->template->title($this->module_details['name'])
			->set('items',$items)
            ->set('pagination',$pagination)
            ->set('conceptos',array_for_select())
            ->set('anio',$anio)
			//->append_js('module::banner.js')
			->build('admin/partidas/index');
    }
    function create($anio)
    {
        
        $partida=new StdClass();
            
            
        $this->form_validation->set_rules($this->rules);
		
				
		if($this->form_validation->run())
		{
			unset($_POST['btnAction']);
            
            $input=array(
  	             'nombre'     => $this->input->post('nombre'),
			     'id_concepto'=> $this->input->post('id_concepto'),	
                 'id_actividad_poa' => $this->input->post('id_actividad_poa'),			    
			     'descripcion'=> $this->input->post('descripcion'),
                 'no_partida'=> $this->input->post('no_partida'),	
                
            );
			if($id = $this->partidas_m->insert($input))
            {
				
				$this->session->set_flashdata('success',sprintf(lang('partidas:save_success'),$input['nombre']));
				
			}else{
				$this->session->set_flashdata('error',lang('partidas:save_error'));
				
			}
			redirect('admin/fondo/partidas/'.$anio);
            
        }
        
        
        foreach ($this->rules as $rule)
  		{
 			$partida->{$rule['field']} = $this->input->post($rule['field']);
  		}
		
        $conceptos=$this->conceptos_m->order_by('nombre')->get_all();
        
 	    $this->template->title($this->module_details['name'])
            ->set('partida',$partida)
            ->set('conceptos',array_for_select($conceptos,'id','nombre'))
			->set('anio',$anio)
			->append_js('module::partida.controller.js')
			->build('admin/partidas/form');
    }
    function edit($id=0)
    {
        if(!$partida= $this->partidas_m->join('cat_conceptos','cat_conceptos.id=fondo_partidas.id_concepto')->get_by('fondo_partidas.id',$id))
		{
		    
			$this->session->set_flashdata('error', lang('global:not_found_edit'));
			redirect('admin/fondo/partidas');
		}
        
        $this->form_validation->set_rules($this->rules);
		
				
		if($this->form_validation->run())
		{
			unset($_POST['btnAction']);
            
            $input=array(
  	             'nombre'           => $this->input->post('nombre'),
			     'id_concepto'      => $this->input->post('id_concepto'),	
                 'id_actividad_poa' => $this->input->post('id_actividad_poa'),			    
			     'descripcion'      => $this->input->post('descripcion'),
                 'no_partida'       => $this->input->post('no_partida'),	
                
            );
			if($this->partidas_m->update($id,$input))
            {
				
				$this->session->set_flashdata('success',sprintf(lang('partidas:save_success'),$input['nombre']));
				
			}
            else
            {
				$this->session->set_flashdata('error',lang('partidas:save_error'));
				
			}
			redirect('admin/fondo/partidas/edit/'.$id);
            
        }
       
        
         $this->template->title($this->module_details['name'])
            ->set('partida',$partida)
           
			->set('anio',$partida->anio)
			->append_js('module::partida.controller.js')
			->build('admin/partidas/form');
    }
    function delete($id=0)
    {
        role_or_die('partidas', 'delete');
		$ids = ($id) ?array(0=>$id) : $this->input->post('action_to');
        
        if ( ! empty($ids))
		{
		  
   	        foreach ($ids as $id)
			{
				
                $total_fondo = $this->fondo_m->count_by(array('id_partida'=>$id));
                
				if ($total_fondo == 0 && $partida = $this->partidas_m->get($id))
				{
				    
                    //Files::delete_file($aviso->portada);
                    $this->partidas_m->delete($id);
                    $deletes[]=$partida->nombre;
                }
            }
        }
        if ( ! empty($deletes))
		{
		    $this->session->set_flashdata('success', sprintf(lang('partidas:delete_success'), implode('", "', $deletes)));
        }
        else
        {
            $this->session->set_flashdata('error',lang('partidas:delete_error'));
        }
        redirect('admin/fondo/partidas');
    }
    //Exporta de tabla a tabla
    function exportBD()
    {
        $partidas = $this->db->select('*,fondo_partidas.nombre AS nombre,fondo_partidas.id AS id')
                    ->where('anio',2016)
                    ->join('cat_conceptos','cat_conceptos.id=fondo_partidas.id_concepto')
                    ->get('fondo_partidas')->result();
        
        foreach($partidas AS $index=>$partida)
        {
            $concepto = $this->db->where(array(
                'anio'=>2017,
                'no_concepto'=>$partida->no_concepto
            ))->get('cat_conceptos')->row();
            
            $this->partidas_m->insert(array(
                'id_concepto' => $concepto->id,
                'no_partida' => $partida->no_partida,
                'nombre' => $partida->nombre,
                'descripcion' => $partida->descripcion
            
              ));
            //echo ($index+1).'-'.($partida->nombre?$partida->nombre:$partida->no_partida).'-'.$concepto->id.'</br>';
        }
    }
    function export()
    {
         $result = $this->db->get('federal2016')->result();
         $no_concepto = false;
         $inc = 1;
         foreach($result as $row)
         {
            
              if($row->F4=='No. DE PARTIDA') continue;
              
              if(!$row->F2)
              {
                   
              }
              else
              {
                  $no_concepto = $row->F2;
              }
              
              
              $concepto = $this->db->where(array('anio'=>'2016','no_concepto'=>$no_concepto))->get('cat_conceptos')->row();
              
              if(!$concepto)
              {
                 $no_concepto = '<span style="color:00FFFF;">'.$no_concepto.'</span>';
              }
              /*$this->partidas_m->insert(array(
                'id_concepto' => $concepto->id,
                'no_partida' => $row->F4,
                'nombre' => $row->F5,
                'descripcion' => $row->F6
            
              ));*/
              echo $inc.' '.$no_concepto.'-'.$row->F4.'-'.$row->F5.'<br/>';
              $inc++;
              //$no_concepto = $row->F2;
              
              
         }
    }
    function export2018()
    {
         $result = $this->db->get('federal2018')->result();
         $no_concepto = false;
         $inc = 1;
         return false;
         foreach($result as $row)
         {
            
              if($row->F9=='No. DE PARTIDA' || !$row->F9) continue;
              
              if(!$row->F7)
              {
                   
              }
              else
              {
                  $no_concepto = $row->F7;
              }
              
              
              $concepto = $this->db->where(array('anio'=>'2018','no_concepto'=>$no_concepto))->get('cat_conceptos')->row();
              
              if(!$concepto)
              {
                 $no_concepto = '<span style="color:00FFFF;">'.$no_concepto.'</span>';
              }
              $this->partidas_m->insert(array(
                'id_concepto' => $concepto->id,
                'id_actividad_poa' => $concepto->id_actividad_poa,
                'no_partida' => $row->F9,
                'nombre' => $row->F10,
                'descripcion' => $row->F6
            
              ));
              echo $inc.' '.$no_concepto.'-'.$row->F9.'-'.$row->F10.'<br/>';
              $inc++;
              //$no_concepto = $row->F2;
              
              
         }
    }
}
?>