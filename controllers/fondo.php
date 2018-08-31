<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * The public controller for the Pages module.
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Pages\Controllers
 */
class Fondo extends Public_Controller
{

	/**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct();
        $this->load->helper('fondo');
        $this->load->library('files/files');
        $this->lang->load('fondo');
        $this->lang->load('calendar');
        
        $this->config->load('fondo');
        $this->load->model(array(
            'fondo_m',
            'proveedores/proveedores_m',
            'conceptos/conceptos_m',
            'partidas_m',
            'centros/centro_m',
            'files/file_folders_m'
            ));
        $this->validation_rules = array(
        
            array(
    				'field' => 'no_factura',
    				'label' => 'No. de Factura',
    				'rules' => 'trim|required'
    				),
    		    array(
    				'field' => 'proveedor',
    				'label' => 'Proveedor',
    				'rules' => 'integer|required'
    				),
                 array(
    				'field' => 'id_director',
    				'label' => 'Director',
    				'rules' => 'integer'
    				),
                array(
    				'field' => 'actividad',
    				'label' => 'Actividad POA',
    				'rules' => 'integer'
    				),
                array(
    				'field' => 'proyecto',
    				'label' => 'Proyecto/Concepto',
    				'rules' => 'integer'
    				),
                array(
    				'field' => 'id_partida',
    				'label' => 'Partida',
    				'rules' => 'integer'
    				),
    			array(
    				'field' => 'importe',
    				'label' => 'Importe',
    				'rules' => 'trim|numeric|required'
    				),
                array(
    				'field' => 'concepto',
    				'label' => 'Concepto',
    				'rules' => 'trim|required'
    				),
                array(
    				'field' => 'id_centro',
    				'label' => 'Centro',
    				'rules' => 'trim'
    				),
               array(
    				'field' => 'autorizado',
    				'label' => 'Autorizado',
    				'rules' => 'trim'
    				),
                array(
    				'field' => 'mes',
    				'label' => 'Mes',
    				'rules' => 'trim|required|callback__verify_month'
    				),
                'motivo'=>array(
    				'field' => 'motivo',
    				'label' => 'Motivo',
    				'rules' => 'trim'
    				),
                
                array(
    				'field' => 'xml_messages',
    				'label' => 'XML Validacion',
    				'rules' => 'trim'
    				),
                array(
    				'field' => 'xml_uuid',
    				'label' => 'UUID',
    				'rules' => 'trim'
    				),
                 array(
    				'field' => 'pdf',
    				'label' => 'PDF',
    				'rules' => 'trim'
    				),
                 array(
    				'field' => 'xml',
    				'label' => 'XML',
    				'rules' => 'trim|callback__valid_xml'
    				),
                 array(
    				'field' => 'attach',
    				'label' => 'Attach',
    				'rules' => ''
    				),
        );
        $this->template->set_breadcrumb('Fondo revolvente','fondo',true);
        
        $this->template->enable_parser(true)
            ->months = array(
        
           '01'  => 'ENERO',
            '02' => 'FEBRERO',
            '03'  => 'MARZO',
            '04'  => 'ABRIL',
            '05'   => 'MAYO',
            '06'  => 'JUNIO',
            '07'  => 'JULIO',
            '08' => 'AGOSTO',
            '09' => 'SEPTIEMBRE',
            '10'    => 'OCTUBRE',
            '11'  => 'NOVIEMBRE',
            '12'  => 'DICIEMBRE',
            
        );
        
        
        if($this->current_user == false)
        {
            $this->session->set_userdata('redirect_to', current_url());
            redirect('users/login');
        }
        
        $this->director = $this->db->where('user_id',$this->current_user->id)->get('directores')->row();
        
        $this->template->set('director',$this->director);
    }
    function _verify_month($field)
    {
        $this->config->load('fondo');
        $anio  = $this->input->post('anio');
        $corte = $this->config->item('corte');
        
        $time_config = strtotime($anio.'-'.$field.'-'.$corte.' 23:59:59');
        
        if($time_config < now())
        {
            $this->form_validation->set_message('_verify_month', lang('fondo:error_month'));
            return false;
        }
        
        return true;
        
        
    }
    function _valid_xml($field)
    {
        
        if(!$_FILES['file_xml']['name'])
        {
            if(!$field)
            {
                $this->form_validation->set_message('_valid_xml',lang('fondo:xml_not'));
                return false;
                
            }
            
            return true;
            
            
            
            
        }
        
        $this->load->library('facturas/factura');
        
        $folder=$this->file_folders_m->get_by(array('slug'=>'facturacion'));
        $result_xml = Files::upload($folder->id,false,'file_xml');
        
       
        
        $result_valid = Factura::ValidXML($result_xml['data']['id']);   
            
        if($result_valid['status'] == false)
        {
            $this->form_validation->set_message('_valid_xml',$result_valid['messages'][0]['message']);
            return false;
        }
        
        if($match = $this->db->select('*,centros.nombre AS nombre,fondo.id AS id')   
                ->where_not_in('fondo.id',$this->input->post('id'))    
                ->join('centros','centros.id=fondo.id_centro')
                ->where('xml_uuid',$result_valid['data']['UUID'])->get('fondo')->row())
        {
            
            $result_valid['messages'][] = array('code'=>'0','message'=>sprintf(lang('fondo:error_uuid'),$match->nombre,$match->id));
        
           
        }
        $_POST['xml_messages']   = json_encode($result_valid['messages']);        
        $_POST['xml']            = $result_xml['data']['id'];        
        $_POST['xml_uuid']       = $result_valid['data']['UUID'];
        
        
        return true;
       
    }
    function index()
    {
        $anio     = $this->input->get('anio');
        $mes      = $this->input->get('mes');
        $keywords = $this->input->get('keywords');
        
        $base_where = array(
            'id_director' => $this->director->id
        );
        
        if($anio)
        {
            $base_where['fondo.anio'] = $anio;
            
        }
        if($mes)
        {
            $base_where['mes'] = $mes;
            
        }
        if($keywords)
        {
            $base_where['(concepto LIKE \'%'.$keywords.'%\' OR no_factura LIKE \'%'.$keywords.'%\')'] = null;
        }
        
        $items = false;
        $anios = $this->fondo_m->where(array(
                            'id_director' => $this->director->id
                        ))->group_by('anio')
                        ->get_all();
        if($this->director)
        {
            
            $total_rows = $this->fondo_m
                            ->order_by('id','DESC')
                            ->count_by($base_where);
                            
            $pagination = create_pagination('fondo/p', $total_rows,NULL,3);
            
            $items      = $this->fondo_m->where($base_where)
                            //->group_by('fondo.anio')
                            ->order_by('id','DESC')
                            ->limit($pagination['limit'],$pagination['offset'])
                            ->get_all();
                            
                            
            foreach($items as &$item)
            {
                $mes_fondo  = strtotime($item->anio.'-'.$item->mes.'-01');
                $mes_actual = strtotime(date('Y').'-'.date('m').'-01');
                
                $item->es_activo = $mes_actual > $mes_fondo?0:1;
            }
        }
        
        /*if($items)
        {
            foreach($items as $item)
            {
                if(!in_array($item->anio,$anios))
                {
                    $anios[$item->anio] = $item->anio;
                }
            }
        }*/
        $this->template->title($this->module_details['name'])
                    ->set('items',$items)
                    ->set('base_where',$base_where)
                    ->set('anios',array_for_select($anios,'anio','anio'))
                    ->set('pagination',$pagination)
                    ->build('index');
    }
    function edit($id=0)
    {
         $fondo= $this->fondo_m->get_fondo($id,'*,id_actividad_poa AS actividad,id_concepto AS proyecto,id_proveedor as proveedor') OR show_404();
         
         
         
         if(!$this->director)
         {
            //No tiene asignada cuenta de director
         }
         if(is_numeric($fondo->autorizado)==false || $fondo->autorizado==1)
         {
            $this->session->set_flashdata('error',lang('global:not_found'));
            
            redirect('fondo/');
         }
         
         if(es_activo($fondo->anio,$fondo->mes) == false)
         {
            $this->session->set_flashdata('error',lang('fondo:error_mes'));
            
            redirect('fondo/');
         }
         
         if((strtotime(date('Y').'-'.date('m').'-'.$this->config->item('corte')) - now()) >0 )                
            $corte = strtotime(date('Y').'-'.date('m').'-'.$this->config->item('corte'));
            
         else
            $corte = strtotime(date('Y').'-'.(date('m')+1).'-'.$this->config->item('corte'));
         
         
         
         
         
        
        $this->form_validation->set_rules($this->validation_rules);       
				
		if($this->form_validation->run())
		{
		    unset($_POST['btnAction']);
            $data = array(
                'id_director' => $this->director->id,
                'id_centro'   => $this->director->id_centro,
                'no_factura'   => $this->input->post('no_factura'),
                'id_proveedor' => $this->input->post('proveedor'),
                
               // 'id_actividad_poa' => $this->input->post('actividad'),
               // 'id_concepto'      => $this->input->post('proyecto')?$this->input->post('proyecto'):null,
               // 'id_partida'       => $this->input->post('partida')?$this->input->post('partida'):null,
                
                'concepto' => $this->input->post('concepto'),
               
                'mes'      => $this->input->post('mes'),
                'importe'  => number_format($this->input->post('importe'),2,'.',''),
                
                
                'pdf'  => $this->input->post('pdf'),
                
                'xml'           => $this->input->post('xml'),
                'xml_uuid'      => $this->input->post('xml_uuid'),
                //'xml_messages'  => $this->input->post('xml_messages'),
                
                'autorizado' => null,
                'updated_on'   => now()
            
            );
             $folder=$this->file_folders_m->get_by(array('slug'=>'facturacion'));
             
            if($_FILES['file_pdf']['name'])
            {
                
                 $result = Files::upload($folder->id,false,'file_pdf',false,false,false,'pdf');
                 if($result['status'])
                    $data['pdf'] = $result['data']['id'];
            }
            
            if($_FILES['file_xml']['name'])
            {
                
                 $result = Files::upload($folder->id,false,'file_xml',false,false,false,'xml');
                 
                 if($result['status'])
                    $data['xml'] = $result['data']['id'];
            }
            
            
            //$_POST['anio'] = $anio;
            
            if($this->fondo_m->update($id,$data))
            {
				
				$this->session->set_flashdata('success',sprintf(lang('fondo:save_success'),$this->input->post('concepto')));
				
			}
            else
            {
				$this->session->set_flashdata('error',lang('global:save_error'));
				
			}
			redirect('fondo/details/'.$id);
        }
        
        
        $proveedores = $this->proveedores_m->order_by('razon_social')->get_all();  
        
        ///Catalogos
        
        $conceptos = $this->db->select('id,CONCAT(no_concepto," - ",nombre) AS nombre',false)
                            ->where('id_actividad_poa',$fondo->actividad)
                            ->get('cat_conceptos')->result();  
                            
                            
        $partidas = $this->db->select('id,CONCAT(no_partida," - ",nombre) AS nombre',false)
                            ->where('id_concepto',$fondo->id_concepto)
                            ->get('fondo_partidas')->result();  
        
            
        $this->template->title($this->module_details['name'],'Modificar solicitud')
            ->set_breadcrumb('Modificar solicitud')
            ->set('corte',$corte)
			->set('fondo',$fondo)
            ->set('anio',$fondo->anio)
            ->append_metadata('<script type="text/javascript">var fondo='.json_encode($fondo).',option_partidas='.json_encode($partidas).';option_conceptos = '.json_encode($conceptos).';</script>')
            ->append_css('module::form.css')
            ->append_js('module::front/form.js')
  	        ->set('proveedores',array_for_select($proveedores,'id','razon_social'))
            ->build('form');
    }
    function details($id=0)
    {
        
         $fondo= $this->fondo_m->get_fondo($id,'*,id_actividad_poa AS actividad,id_concepto AS proyecto,id_proveedor as proveedor') OR show_404();
         
         
        
        $this->form_validation->set_rules($this->validation_rules);       
				
		
        
        
        $proveedores = $this->proveedores_m->order_by('razon_social')->get_all();  
        
        ///Catalogos
        
        $conceptos = $this->db->select('id,CONCAT(no_concepto," - ",nombre) AS nombre',false)
                            ->where('id_actividad_poa',$fondo->actividad)
                            ->get('cat_conceptos')->result();  
                            
                            
         $partidas = $this->db->select('id,CONCAT(no_partida," - ",nombre) AS nombre',false)
                            ->where('id_concepto',$fondo->id_concepto)
                            ->get('fondo_partidas')->result();  
                            
                            
          if((strtotime(date('Y').'-'.date('m').'-'.$this->config->item('corte')) - now()) >0 )                
            $corte = strtotime(date('Y').'-'.date('m').'-'.$this->config->item('corte'));
            
        else
            $corte = strtotime(date('Y').'-'.(date('m')+1).'-'.$this->config->item('corte'));
      
        $this->template->title($this->module_details['name'],'Modificar concepto')
            ->set_breadcrumb('Detalles de la solicitud')
            ->set('corte',$corte)
			->set('fondo',$fondo)
            ->set('anio',$fondo->anio)
            ->append_metadata('<script type="text/javascript">var fondo='.json_encode($fondo).',option_partidas='.json_encode($partidas).';option_conceptos = '.json_encode($conceptos).';</script>')
            ->append_css('module::form.css')
            ->append_js('module::front/form.js')
  	        ->set('proveedores',array_for_select($proveedores,'id','razon_social'))
            ->build('form');
    }
    function create($anio=false)
    {
         $fondo=new StdClass();
         
         if(!$anio)
             $anio = date('Y'); 
         
         $this->form_validation->set_rules($this->validation_rules);
		
        
				
		if($this->form_validation->run())
		{
		    unset($_POST['btnAction']);
            $input = $this->input->post();
            
            $data = array(
                'id_director' => $this->director->id,
                'id_centro'   => $this->director->id_centro,
                'no_factura'   => $this->input->post('no_factura'),
                'id_proveedor' => $this->input->post('proveedor'),
                'concepto' => $this->input->post('concepto'),
                
                //'id_actividad_poa' => $this->input->post('actividad')?$this->input->post('actividad'):null,
                //'id_concepto'      => $this->input->post('proyecto')?$this->input->post('proyecto'):null,
                //'id_partida'       => $this->input->post('partida')?$this->input->post('partida'):null,
                
                'anio'     => $anio,
                'mes'      => $this->input->post('mes'),
                'importe'  => number_format($this->input->post('importe'),2,'.',''),
                'pdf'      => $this->input->post('pdf'),
               
                'xml'           => $this->input->post('xml'),
                'xml_uuid'      => $this->input->post('xml_uuid'),
                'xml_messages'  => $this->input->post('xml_messages'),
                
                'autorizado'  => null,
                'created_on'   => now()
            
            );
            $folder=$this->file_folders_m->get_by(array('slug'=>'facturacion'));
             
            if($_FILES['file_pdf']['name'])
            {
                
                 $result = Files::upload($folder->id,false,'file_pdf',false,false,false,'pdf');
                 if($result['status'])
                    $data['pdf'] = $result['data']['id'];
            }
            
            /*if($_FILES['file_xml']['name'])
            {
                
                 $result = Files::upload($folder->id,false,'file_xml',false,false,false,'xml');
                 
                 if($result['status'])
                    $data['xml'] = $result['data']['id'];
            }*/
            
            if(isset($input['xml'])==false)
            {
                $data['xml_uuid']     = null;
                $data['xml_messages'] = null;
            }
            if($input['xml_uuid'] )
            {
                $data['xml_uuid']     = $input['xml_uuid'];
                $data['xml_messages'] = $input['xml_messages'];
            }
            
            if($id = $this->fondo_m->insert($data))
            {
				
				$this->session->set_flashdata('success',sprintf(lang('fondo:save_success'),$this->input->post('concepto')));
					redirect('fondo/details/'.$id.'?add=1');
			}
            else
            {
				$this->session->set_flashdata('error',lang('global:save_error'));
					redirect('fondo/create');
			}
		
        }
         foreach ($this->validation_rules as $rule)
  		{
 			$fondo->{$rule['field']} = $this->input->post($rule['field']);
           
            if($_POST && !isset($fondo->attach['xml']))
            {
                $xml = Files::get_file($this->input->post('xml'));
                $fondo->attach = array(
                    'xml' => $xml['status']?$xml['data']:false
                
                );
            }
  		}
        
           
      
       
        
        ///Catalogos
        $proveedores = $this->proveedores_m->order_by('razon_social')->get_all(); 
        
        $conceptos = $this->db->select('id,CONCAT(no_concepto," - ",nombre) AS nombre',false)
                            ->where('id_actividad_poa',$fondo->actividad)
                            ->get('cat_conceptos')->result();  
                            
                            
         $partidas = $this->db->select('id,CONCAT(no_partida," - ",nombre) AS nombre',false)
                            
                            ->where('id_concepto',$fondo->id_concepto)
                            ->get('fondo_partidas')->result();  
                          
            
            
        if((strtotime(date('Y').'-'.date('m').'-'.$this->config->item('corte')) - now()) >0 )                
            $corte = strtotime(date('Y').'-'.date('m').'-'.$this->config->item('corte'));
            
        else
            $corte = strtotime(date('Y').'-'.(date('m')+1).'-'.$this->config->item('corte'));
        $this->template->title($this->module_details['name'],'Nueva solicitud')
            ->set_breadcrumb('Nueva solicitud')
             ->append_metadata('<script type="text/javascript">var fondo='.json_encode($fondo).',option_partidas='.json_encode($partidas).';option_conceptos = '.json_encode($conceptos).';</script>')
            ->set('corte',$corte)
			->set('fondo',$fondo)
            ->set('anio',$anio)
            ->append_css('module::form.css')
            ->append_js('module::front/form.js')
  	        ->set('proveedores',array_for_select($proveedores,'id','razon_social'))
            ->build('form');
    }
     function list_concepto()
    {
        
        $base_where=array(
            'activo' => 1
        );
        
        $base_where['id_actividad_poa']  = $this->input->get('actividad');
        $base_where['anio']              = $this->input->get('anio');
        
        $conceptos = $this->conceptos_m->order_by('no_concepto')->get_many_by($base_where);
        
        foreach($conceptos as &$concepto)
        {
            $concepto->nombre=$concepto->no_concepto.' - '.$concepto->nombre;
        }
        if($conceptos)return  $this->template->build_json($conceptos);
    }
    
    function list_partida()
    {
        
         $base_where=array(
           
        );
        
        $base_where['id_concepto']=$this->input->get('concepto');
        
       // $base_where['id_actividad_poa']=$this->input->post('id_actividad_poa');
        
        $partidas = $this->partidas_m->order_by('no_partida')->get_many_by($base_where);
        
        foreach($partidas as &$partida)
        {
            $partida->nombre=$partida->no_partida.' - '.$partida->nombre;
        }
        
        if($partidas) return  $this->template->build_json($partidas);
    }
    function upload()
    {
        $this->load->library('facturas/factura');
        $result = array();
        $folder = $this->file_folders_m->get_by_path('facturacion');
        
        $result = Files::upload($folder->id,false,'file',false,false,false,$this->input->post('type'));
        
        if($result['status'])
        {
            $result['message'] = '<p class="text-success">- '.$result['message'].'</p>';
        }
        if($this->input->post('type') == 'xml' && $result['status'])
        {
            $xml_file = Factura::ValidXML($result['data']['id']);
            
           
            foreach($xml_file['messages'] as $message)
            {
                //print_r($message);
                
                $result['message'] .=  '<p class="'.($message['code']==0?'text-danger':'text-success').'">- '.$message['message'].'</p>';
            }
        }
        
        return $this->template->build_json($result);
        
    }
}
?>