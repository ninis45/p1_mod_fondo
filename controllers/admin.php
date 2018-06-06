<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller {
	protected $section='fondo';
	protected $error=array();
	protected $file_data=false;
	public function __construct()
	{
		parent::__construct();
        $this->lang->load(array('fondo','calendar'));
        $this->load->helper('fondo');
        $this->load->config('fondo');
        $this->load->model(array('fondo_m','proveedores/proveedores_m','conceptos/conceptos_m','partidas_m','centros/centro_m'));
         
        $this->config->load('files/files');
        $this->lang->load('files/files');
        $this->load->library(array('files/files'));
        
        $this->_path = FCPATH.rtrim($this->config->item('files:path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
  
        $this->rules = array(
			'form'=>array(
    			array(
    				'field' => 'no_factura',
    				'label' => 'No. de Factura',
    				'rules' => 'trim|required'
    				),
    		    array(
    				'field' => 'id_proveedor',
    				'label' => 'Proveedor',
    				'rules' => 'integer|required'
    				),
                 array(
    				'field' => 'id_director',
    				'label' => 'Director',
    				'rules' => 'integer|required'
    				),
                array(
    				'field' => 'id_actividad_poa',
    				'label' => 'Actividad POA',
    				'rules' => 'integer|required'
    				),
                array(
    				'field' => 'id_concepto',
    				'label' => 'Proyecto/Concepto',
    				'rules' => 'integer|required'
    				),
                array(
    				'field' => 'id_partida',
    				'label' => 'Partida',
    				'rules' => 'integer|required'
    				),
    			array(
    				'field' => 'importe',
    				'label' => 'Importe',
    				'rules' => 'trim|required'
    				),
                array(
    				'field' => 'concepto',
    				'label' => 'Concepto',
    				'rules' => 'trim|required'
    				),
                array(
    				'field' => 'id_centro',
    				'label' => 'Centro',
    				'rules' => 'trim|required'
    				),
               array(
    				'field' => 'autorizado',
    				'label' => 'Autorizado',
    				'rules' => 'trim|required'
    				),
                'mes'=>array(
    				'field' => 'mes',
    				'label' => 'Mes',
    				'rules' => 'trim|required'
    				),
                'motivo'=>array(
    				'field' => 'motivo',
    				'label' => 'Motivo',
    				'rules' => 'trim'
    				),
                
                array(
    				'field' => 'xml_messages',
    				'label' => 'XML Mensajes',
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
    				)
            ),
            'report'=>array(
            
                array(
    				'field' => 'anio',
    				'label' => 'Anio',
    				'rules' => 'trim|required'
    				),
                array(
    				'field' => 'mes',
    				'label' => 'Mes',
    				'rules' => 'trim|required'
    				)
                
            )
		);
        $this->template
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
        
    }
    
  
    
    function valid_xml($id_fondo,$id_file)
    {
        $this->load->library('facturas/factura');
        $result_valid = Factura::ValidXML($id_file);
        
       
       
        if($match = $this->db->select('*,centros.nombre AS nombre,fondo.id AS id')   
                ->where_not_in('fondo.id',$id_fondo)    
                ->join('centros','centros.id=fondo.id_centro')
                ->where('xml_uuid',$result_valid['data']['UUID'])->get('fondo')->row())
        {
            
            $result_valid['messages'][] = array('code'=>'0','message'=>sprintf(lang('fondo:error_uuid'),$match->nombre,$match->id));
        
           
        }
        
        $this->fondo_m->update($id_fondo,array(
        
              'xml_messages' => json_encode($result_valid['messages'])
        )); 
        
        redirect('admin/fondo/edit/'.$id_fondo);
    }
    function _valid_xml($field='')
    {
        
        if(!$_FILES['xml_file']['name'])
        {
            return true;
            
            
        }
        
        $this->load->library('facturas/factura');
        
        $folder=$this->file_folders_m->get_by(array('slug'=>'facturacion'));
        $result_xml = Files::upload($folder->id,false,'xml_file',false,false,false,'xml');
        
       
       
        $result_valid = Factura::ValidXML($result_xml['data']['id']);   
            
        if($result_valid['status'] == false)
        {
            Files::delete_file($result_xml['data']['id']);
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
        
        
        
        $periodos = $this->fondo_m->group_by('anio')->get_all();
        
        $this->template->title($this->module_details['name'])
            ->set('periodos',$periodos)
			->build('admin/fondo/init');
    }
    function _valida_sat()
    {
         global $data;
        $url = 'https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc?wsdl';
        $soapclient = new SoapClient($url);
        $rfc_emisor = utf8_encode($data['rfc']);
        $rfc_receptor = utf8_encode($data['rfc_receptor']);
        $impo = (double)$data['total'];
        $impo=sprintf("%.6f", $impo);
        $impo = str_pad($impo,17,"0",STR_PAD_LEFT);
        $uuid = strtoupper($data['uuid']);
        $factura = "?re=$rfc_emisor&rr=$rfc_receptor&tt=$impo&id=$uuid";
        echo "<h3>$factura</h3>";
        $prm = array('expresionImpresa'=>$factura);
        $buscar=$soapclient->Consulta($prm);
        echo "<h3>El portal del SAT reporta</h3>";
        echo "El codigo: ".$buscar->ConsultaResult->CodigoEstatus."<br>";
        echo "El estado: ".$buscar->ConsultaResult->Estado."<br>";
    }
    function remove_dom_namespace($doc, $ns) {
      $finder = new DOMXPath($doc);
      $nodes = $finder->query("//*[namespace::{$ns} and not(../namespace::{$ns})]");
      foreach ($nodes as $n) {
        $ns_uri = $n->lookupNamespaceURI($ns);
        $n->removeAttributeNS($ns_uri, $ns);
      }
    }
    function load($anio='')
    {
        $base_where = array();
        $data       = array();
        $base_where['fondo.anio'] = $anio;
        
        $f_centro = $this->input->get('f_centro');
        $f_keywords = $this->input->get('f_keywords');
        
        
        $f_centro AND $base_where['centros.id'] = $f_centro;
        $f_keywords AND $base_where['(CONCEPTO LIKE "%'.$f_keywords.'%" OR default_fondo.id LIKE "%'.$f_keywords.'%" OR mes  LIKE "%'.$f_keywords.'%")']    = NULL;
        
        
        $total_rows = $this->fondo_m//->join('fondo_partidas','fondo_partidas.id=fondo.id_partida')
                ->join('centros','centros.id=fondo.id_centro')
                //->join('cat_actividad_poa','cat_actividad_poa.id=fondo.id_actividad_poa')
                //->join('cat_conceptos','cat_conceptos.id=fondo.id_concepto')
                ->count_by($base_where);
                
        $pagination = create_pagination('admin/fondo/'.$anio, $total_rows,10,4);
            //print_r($base_where);     
                
   	    $items=$this->fondo_m->select('*,fondo.id AS id,centros.nombre AS nombre_centro')
                //->order_by('fondo.mes,fondo.id','DESC')
                //->join('fondo_partidas','fondo_partidas.id=fondo.id_partida')
                ->join('centros','centros.id=fondo.id_centro')
                //->join('cat_actividad_poa','cat_actividad_poa.id=fondo.id_actividad_poa')
                //->join('cat_conceptos','cat_conceptos.id=fondo.id_concepto')
           
                ///->limit($pagination['limit'],$pagination['offset'])
                ->get_many_by($base_where);
                
          
         //exit();       
        foreach($items as $item)
        {
            $data[$item->mes][$item->nombre_centro][] = $item;
        }
        
		$centros = $this->db->get('centros')->result();
		$this->template->title($this->module_details['name'],'Administrando '.$anio)
            ->enable_parser(true)
            ->append_js('module::fondo.controller.js')
            ->append_metadata('<script type="text/javascript"> var items = '.json_encode($data).'; </script>')
			->set('items',$items)
            ->set('data',$data)
            ->set('x1',$this->config->item('x1'))
            ->set('x3',$this->config->item('x3'))
            ->set('tg',$this->config->item('tg'))
            ->set('ff',$this->config->item('ff'))
            ->set('x4',$this->config->item('x4'))
            ->set('pagination',$pagination)
            ->set('anio',$anio)
            ->set('centros',array_for_select($centros,'id','nombre'))
			->build('admin/fondo/index');
    }
    function create($anio='')
    {
        
        role_or_die($this->section, 'create');
        
        $anio or redirect('admin/fondo');
        
        $fondo=new StdClass();
        
        
        if(!$this->input->post('autorizado'))
        {
            $this->rules['form']['motivo']['rules'].='|required';
        }
        
        
        ///$this->rules['form']['mes']['rules'] .= '|callback__verify_month';
        
        $this->form_validation->set_rules($this->rules['form']);
		
        
				
		if($this->form_validation->run())
		{
			unset($_POST['btnAction']);
            
            
            $input = $this->input->post();
            
            
            $data=array(
                'anio'         => $anio,
                'id_proveedor' => $input['id_proveedor'],            
                'id_actividad_poa' => $input['id_actividad_poa'],
                'id_concepto'      => $input['id_concepto'],
                'id_partida'       => $input['id_partida'],
                'id_centro'        => $input['id_centro'],
                'id_director'      => $input['id_director'],
                'motivo'           => $input['autorizado']?NULL:$input['motivo'],
                'mes'              => $input['mes'],
                'no_factura'       => $input['no_factura'],
                'concepto'         => $input['concepto'],
                'importe'          => str_replace(',','',number_format($input['importe'],2)),
                
                
                'xml'              => isset($input['xml'])?$input['xml']:NULL,
                'pdf'              => isset($input['pdf'])?$input['pdf']:NULL,
                    
                'autorizado'       => $input['autorizado'],
                'created_on' => now()
            );
            
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
            
            $folder=$this->file_folders_m->get_by(array('slug'=>'facturacion'));
            $result_pdf = Files::upload($folder->id,false,'pdf_file');
                 
            if($result_pdf['status'])
            {
                $data['pdf']= $result_pdf['data']['id'];
            }
            
            if($id = $this->fondo_m->insert($data))
            {
				
				$this->session->set_flashdata('success',sprintf(lang('fondo:save_success'),$this->input->post('concepto')));
				redirect('admin/fondo/edit/'.$id);
			}
            else
            {
				$this->session->set_flashdata('error',lang('global:save_error'));
				redirect('admin/fondo/create/'.$anio);
			}
			
        }
        foreach ($this->rules['form'] as $rule)
  		{
 			$fondo->{$rule['field']} = $this->input->post($rule['field']);
  		}
        
        if(!$_POST)
        {
            $fondo->autorizado= 1;
        }
        else
        {
            if(!isset($fondo->attach['xml']) && $this->input->post('xml'))
            {
                $xml = Files::get_file($this->input->post('xml'));
                $fondo->attach = array(
                    'xml' => $xml['status']?$xml['data']:false
                
                );
            }
            
        }
        
        $proveedores = $this->proveedores_m->order_by('razon_social')->get_all();        
        $centros     = $this->centro_m->get_all();
        
        
        
        $this->template->title($this->module_details['name'])
			->set('fondo',$fondo)
            ->set('anio',$anio)
            ->set('centros',array_for_select($centros,'id','nombre'))
            ->set('proveedores',array_for_select($proveedores,'id','razon_social'))
			->append_js('module::fondo.controller.js')
			->build('admin/fondo/form');
    }
    function edit($id=0)
    {
        
        role_or_die($this->section, 'edit');
        
       
       
       
        $fondo = $this->fondo_m->get_fondo($id);
        
        
       
        
        if($this->input->post('autorizado') == 0)
        {
            $this->rules['form']['motivo']['rules'].='|required';
        }
        $this->form_validation->set_rules($this->rules['form']);
		
				
		if($this->form_validation->run())
		{
			unset($_POST['btnAction']);
            
            $input = $this->input->post();
            
            $data=array(
                'id_proveedor'     => $input['id_proveedor'],            
                'id_actividad_poa' => $input['id_actividad_poa'],
                'id_concepto'      => $input['id_concepto'],
                'id_partida'       => $input['id_partida'],
                'id_director'      => $input['id_director'],
                'id_centro'        => $input['id_centro'],
                'motivo'           => $input['autorizado']?NULL:$input['motivo'],
                'mes'              => $input['mes'],
                //'anio'             => $input['anio'],
                'no_factura'       => $input['no_factura'],
                'concepto'         => $input['concepto'],
                'importe'          => str_replace(',','',$input['importe']),
                
                'xml'              => isset($input['xml'])?$input['xml']:NULL,
                'pdf'              => isset($input['pdf'])?$input['pdf']:NULL,
                //'xml_uuid'         => $input['xml_uuid']?$input['xml_uuid']:NULL,
                //'xml_messages'     => $input['xml_messages']?$input['xml_messages']:NULL,
                'autorizado'       => $input['autorizado'],
                'updated_on'       => now()
            );
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
            //if($_FILES['pdf_file']['name'])
            //{
                 $folder=$this->file_folders_m->get_by(array('slug'=>'facturacion'));
                 $result_pdf = Files::upload($folder->id,false,'pdf_file');
                 
                 //$_POST['pdf'] = $result['data']['id'];
            //}
            if($result_pdf['status'])
            {
                $data['pdf']= $result_pdf['data']['id'];
            }
            if($this->fondo_m->update($id,$data))
            {
				
				$this->session->set_flashdata('success',sprintf(lang('fondo:save_success'),$this->input->post('concepto')));
				
			}
            else
            {
				$this->session->set_flashdata('error',lang('global:save_error'));
				
			}
			redirect('admin/fondo/edit/'.$id);
        }
        elseif($_POST)
        {
            $fondo = (Object)$_POST;
            
            $fondo->attach = array('pdf'=>false,'xml'=>false);
            if(!$fondo->attach['xml'] && $this->input->post('xml'))
            {
                $xml = Files::get_file($this->input->post('xml'));
                $fondo->attach['xml'] = $xml['status']?$xml['data']:false;
                
                
            }
            
            if(!$fondo->attach['pdf'] && $this->input->post('pdf'))
            {
                $pdf = Files::get_file($this->input->post('pdf'));
                $fondo->attach['pdf'] = $pdf['status']?$pdf['data']:false;
                
                
            }
            
        }
        
        $proveedores = $this->proveedores_m->order_by('razon_social')->get_all();        
        $centros     = $this->centro_m->get_all();
        
        $this->template->title($this->module_details['name'])
			->set('fondo',$fondo)
            ->set('id',$id)
            ->set('anio',$fondo->anio)
            ->set('centros',array_for_select($centros,'id','nombre'))
            ->set('proveedores',array_for_select($proveedores,'id','razon_social'))
			->append_js('module::fondo.controller.js')
			->build('admin/fondo/form');
    }
    
    function details($id=0)
    {
        
       
        
       
       
       
        $fondo = $this->fondo_m->get_fondo($id) or redirect('admin/fondo');
        
        
        
        $proveedores = $this->proveedores_m->order_by('razon_social')->get_all();        
        $centros     = $this->centro_m->get_all();
        
        $this->template->title($this->module_details['name'],'Detalles')
			->set('fondo',$fondo)
            ->set('id',$id)
            ->set('anio',$fondo->anio)
            ->set('centros',array_for_select($centros,'id','nombre'))
            ->set('proveedores',array_for_select($proveedores,'id','razon_social'))
			->append_js('module::fondo.controller.js')
			->build('admin/fondo/form');
    }
    function delete($anio=false,$id=0)
    {
   	    role_or_die($this->section, 'create');
		$ids = ($id) ?array(0=>$id) : $this->input->post('action_to');
		$url_return = $this->input->post('url_return');
	
        
        
		
        if ( ! empty($ids))
		{
		  
   	        foreach ($ids as $id)
			{
				// Get the current page so we can grab the id too
				if ($fondo = $this->fondo_m->get($id))
				{
				    
                    //Files::delete_file($aviso->portada);
                    $this->fondo_m->delete($id);
                    $deletes[]=$fondo->concepto;
                }
            }
        }
        if ( ! empty($deletes))
		{
		    $this->session->set_flashdata('success', sprintf(lang('fondo:delete_success'), implode('", "', $deletes)));
        }
        else
        {
            $this->session->set_flashdata('error',lang('fondo:delete_error'));
        }
        redirect($url_return? $url_return:'admin/fondo/'.$anio);
    }
    
    function report()
    {
        $base_where='';
        
        $anio = $this->input->get('anio');
        $mes  = $this->input->get('mes');
        
        //$fecha_ini = explode('T',str_replace('"','',$fecha_ini));
        //$fecha_fin = explode('T',str_replace('"','',$fecha_fin));
        
        $output_array = array();
        $error        = false;
        $message      = '';
        
        
        $this->form_validation->set_rules($this->rules['report']);
		
				
		if($anio && $mes)
		{
            
            $base_where['fondo.anio'] = $anio;
            $base_where['mes']  = $mes;
            //$base_where['default_centros.tipo'] = 'Plantel';
            
            $items=$this->fondo_m->reporte($base_where);
            
            
            $this->session->set_userdata(array('base_where'=>$base_where));
            
            
                
           if($items)
           {
               foreach($items as $item)
               {
                     $output_array['listado'][$item->nombre_centro][] = $item; 
                     
                     if($error == false && is_numeric($item->autorizado)==false)
                     {
                        $error = true;
                     }
               }
           }     
           
            
            
        }
        else
        {
            $message = lang('fondo:error_required');
        }
        //print_r($output_array);
        
        $this->template->title($this->module_details['name'])
			->set('output_array',$output_array)
            ->set('anio',$anio)
            ->set('error',$error)
            ->set('message',$message)
            ->set('total_rows',count($items))
             ->set('x1',$this->config->item('x1'))
            ->set('x3',$this->config->item('x3'))
            ->set('tg',$this->config->item('tg'))
            ->set('ff',$this->config->item('ff'))
            ->set('x4',$this->config->item('x4'))
			->append_metadata($this->load->view('admin/partials/meta_chart',false),true)
            ->append_js('module::fondo.controller.js')
			->build('admin/fondo/report');
    }
    function export_importes()
    {
        $base_where   = array(
        
            'fondo.anio' => $this->input->get('anio'),
            'mes'  => $this->input->get('mes'),
        );///$this->session->userdata('base_where') OR redirect('admin/fondo/report');   
        
        
        
        $items        = $this->fondo_m->reporte($base_where);
        $listado      = array();
        $total        = array('autorizado'=>0,'rebote'=>0);
        $periodo      = array();
        $text         = 'TOTAL DE COMPROBACIÓN DE FONDO REVOLVENTE DE CENTROS EDUCATIVOS EN';
        $pre_text     = '';
        
        if($items)
        {
               foreach($items as $item)
               {
                     if(!isset($periodo[$item->anio]))
                     {
                         $periodo[$item->anio] = array();
                     }
                     if(!in_array($item->mes,$periodo[$item->anio]))
                     {
                        $periodo[$item->anio][]= $item->mes;
                     }
                     if(!isset($listado[$item->clave]))
                     {
                        $listado[$item->clave]= array(
                            'nombre_centro' => $item->nombre_centro,
                            'nombre_director'      => $item->nombre_director,
                            'clave'         => $item->clave,
                            'tipo'          => $item->tipo,
                            'autorizado'    => $item->autorizado?$item->importe:0,
                            'rebote'        => !$item->autorizado?$item->importe:0,
                            
                        ); 
                     }
                     else
                     {
                         $listado[$item->clave]['autorizado'] += $item->autorizado?$item->importe:0;
                         $listado[$item->clave]['rebote']     += !$item->autorizado?$item->importe:0;
                     }
                     
                     
                     
               }
              
        } 
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
        date_default_timezone_set('Europe/London');
        
      
        
        
        $this->load->library('Factory');
        
        $this->excel = factory::getTemplate('importe.xlsx'); 
        
        
        $inc           = 3;
        $total_rows   = count($items);
        $total_importe = 0;
        
        $index = 0;
        foreach($listado as $clave => $data)
        {
            
                
                
                //$total_importe+= $data->importe;
                
                $total['autorizado'] += $data['autorizado'];
                $total['rebote']     += $data['rebote'];
                
                $this->excel->getActiveSheet()->insertNewRowBefore($inc+$index,1);
                $this->excel->getActiveSheet()->setCellValue('A'.($inc+$index),($data['tipo']=='Plantel'?'P':'CE').pref_centro($data['clave'],''));
                
                $this->excel->getActiveSheet()->setCellValue('B'.($inc+$index), $data['nombre_centro']);
                $this->excel->getActiveSheet()->setCellValue('C'.($inc+$index), $data['nombre_director']);
                $this->excel->getActiveSheet()->setCellValue('E'.($inc+$index), number_format($data['autorizado']+$data['rebote'],2));
                $this->excel->getActiveSheet()->setCellValue('F'.($inc+$index), number_format($data['rebote'],2));
                $this->excel->getActiveSheet()->setCellValue('G'.($inc+$index), number_format($data['autorizado'],2));
                $index++;
        }
        
        
        //if(count($periodo) == 1)
        //{
            
            //$text.=  implode(',',$periodo[$item->anio]).
        //}
        //exit(print_r($periodo));
        foreach($periodo as $anio => $data_month)
        {
            
            $text.=' '.strtoupper(implode(',',$data_month)).'/'.$anio;
            
           
        }
        $this->excel->getActiveSheet()->setCellValue('B'.($inc+$index),$text);
        $this->excel->getActiveSheet()->setCellValue('E'.($inc+$index),number_format($total['autorizado']+$total['rebote'],2));
        $this->excel->getActiveSheet()->setCellValue('F'.($inc+$index),number_format($total['rebote'],2));
        $this->excel->getActiveSheet()->setCellValue('G'.($inc+$index),number_format($total['autorizado'],2));
        
        $this->excel->getActiveSheet()->removeRow(2,1);
        
        /*******Imprimo contenido del Excel*********/
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="IE_'.$base_where['mes'].'_'.$base_where['cat_conceptos.anio'].'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
    }
    function export_partidas()
    {
        
        //$this->session->userdata('base_where') OR redirect('admin/fondo/report');   
        $base_where   = array(
        
            'fondo.anio' => $this->input->get('anio'),
            'mes'  => $this->input->get('mes'),
        );
        
        
        
        
        $base_where['autorizado'] = '1';       
        $items        = $this->fondo_m->reporte($base_where) OR redirect('admin');
       
        $salt_centro = false;
        $salt_pasivo = false;
        
        $listado = array();
        
        
        
        
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
        date_default_timezone_set('Europe/London');
        
      
        
        
        $this->load->library('Factory');
        
        $this->excel = factory::getTemplate('fondo_listado.xlsx');
        
        
        
        
        
        $this->load->library('Excel');
        
        
        if($items)
        {
               foreach($items as $item)
               {
                     $listado[$item->nombre_centro][] = $item; 
               }
        }  
       // Set document properties
        $this->excel->getProperties()->setCreator("Colegio de Bachilleres del Estado de Campeche")
							 ->setLastModifiedBy("Colegio de Bachilleres del Estado de Campeche")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");




// Rename worksheet
        $this->excel->getActiveSheet()->setTitle('Fondo_'.now());

        $active_sheet = $this->excel->getActiveSheet();
        
        
        $inc           = 5;
        $total_rows   = count($items);
        $total_importe = 0;
        
        $index = 0;
        foreach($listado as $plantel => $data_partida)
        {
            foreach($data_partida as $data)
            {
                
                
                $total_importe+= $data->importe;
                $this->excel->getActiveSheet()->insertNewRowBefore($inc+$index,1);
	            $this->excel->getActiveSheet()->setCellValue('A'.($inc+$index), $data->no_factura);
                $this->excel->getActiveSheet()->setCellValue('B'.($inc+$index), $data->razon_social);
                $this->excel->getActiveSheet()->setCellValue('C'.($inc+$index), ' '.pref_centro($data->clave,$data->tipo=='Plantel'?'02':'03'));
                $this->excel->getActiveSheet()->setCellValue('D'.($inc+$index), ' '.$data->no_concepto);
                $this->excel->getActiveSheet()->setCellValue('E'.($inc+$index), $data->no_partida);
                $this->excel->getActiveSheet()->setCellValue('F'.($inc+$index), $data->concepto);
                $this->excel->getActiveSheet()->setCellValue('G'.($inc+$index), '');
                $this->excel->getActiveSheet()->setCellValue('H'.($inc+$index), $data->importe);
                $this->excel->getActiveSheet()->setCellValue('I'.($inc+$index), ' '.$this->config->item('x1').pref_partida($data->no_partida).$this->config->item('x3').$data->no_partida.$this->config->item('tg').$this->config->item('ff').$this->config->item('x4').pref_centro($data->clave,$data->tipo_centro=='Plantel'?'02':'03').$data->no_componente.pref_actividad($data->no_actividad).$data->no_concepto);
                $this->excel->getActiveSheet()->setCellValue('J'.($inc+$index), $data->importe);
                $this->excel->getActiveSheet()->setCellValue('K'.($inc+$index), '0.00');
                
                
                $index++;
            }
            
            $this->excel->getActiveSheet()->insertNewRowBefore($inc+$index,1);
            $this->excel->getActiveSheet()->setCellValue('F'.($inc+$index), strtoupper($data->nombre_centro));
            $this->excel->getActiveSheet()->setCellValue('G'.($inc+$index), 'TOTAL');
            $this->excel->getActiveSheet()->setCellValue('H'.($inc+$index), $total_importe);
            $this->excel->getActiveSheet()->setCellValue('I'.($inc+$index), ' '.$data->cuenta_pasivo);
            $this->excel->getActiveSheet()->setCellValue('K'.($inc+$index), $total_importe);
            
            $total_importe = 0;
            $index++;
        }
        
        
        if(!$listado)
        {
            return false;
            
        }
        
        $this->excel->getActiveSheet()->setCellValue('F2',$base_where['mes'].'/'.$base_where['anio']);
        $this->excel->getActiveSheet()->setCellValue('J'.($index+$inc+1),'=SUM(J4:J'.($index+$inc-1).')');
        $this->excel->getActiveSheet()->setCellValue('K'.($index+$inc+1),'=SUM(K4:K'.($index+$inc-1).')');
        
        $this->excel->getActiveSheet()->removeRow(4,1);
        
        $this->excel->setActiveSheetIndex(0);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="FR_'.$base_where['mes'].'_'.$base_where['cat_conceptos.anio'].'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');

       
    }
    function export_data($table='diciembre')
    {
        $result=$this->db->get($table)->result();
        $items_update = array();
        
        foreach($result as $row)
        {
            if($row->F1== 'FACTURA') continue;
            
            
            
            if($row->F1)
            {
                $proveedor = $this->db->where('razon_social',$row->F2)->get('proveedores')->row();
                $actividad = $this->db->like('no_actividad',$row->F19)->get('cat_actividad_poa')->row();
                $concepto = $this->db->where(array('anio'=>'2016','no_concepto'=>$row->F4))->get('cat_conceptos')->row();
                $partida = $this->db->where(array('no_partida'=>$row->F5,'id_concepto'=>$concepto->id))->get('fondo_partidas')->row();
                
                
                
                
                $data= array(
                    'no_factura'       => $row->F1,
                    'id_proveedor'     => $proveedor?$proveedor ->id :0,
                    'id_actividad_poa' => $actividad->id,
                    'id_concepto' => $concepto->id,
                    'id_partida'  => $partida->id,
                    'concepto'    => $row->F6,
                    'importe'     => number_format(str_replace(',','',$row->F8),2,'.',''),
                    'mes'         => strtoupper($table),
                    'autorizado'  => 1
                );
               
                if($id = $this->fondo_m->insert($data)){
                    $items_update[]= $id;
                }
                if(!$partida)
                {
                    echo 'Error al insertar: concepto - '.$row->F4.';partida-'.$row->F5.'</br>';
                }
                
            }
            
            else
            {
                
                
                $this->set_centro($row->F6,$items_update);
                $items_update=array();
            }
            
        }
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
    function set_centro($localidad,$items)
    {
        $centro = $this->db->select('centros.id AS id, directores.id AS id_director')
                ->join('directores','directores.id_centro=centros.id','LEFT')
                ->where('localidad',$localidad)->get('centros')->row();
       
        foreach($items as $id)
        {
            
            if(!$this->fondo_m->update($id,array('id_centro'=>$centro->id,'id_director'=>$centro->id_director)))
            {
                echo 'Error al actualizar:'.$id.' - '.$localidad;
            }
        }
    }
    
    function list_concepto()
    {
        
        $base_where=array();
        
        $base_where['id_actividad_poa'] = $this->input->post('id_actividad_poa');
        $base_where['anio']              = $this->input->post('anio');
        
        $conceptos = $this->conceptos_m->order_by('no_concepto')->get_many_by($base_where);
        
        foreach($conceptos as &$concepto)
        {
            $concepto->nombre=$concepto->no_concepto.' - '.$concepto->nombre;
        }
        if($conceptos)echo json_encode($conceptos);
    }
    
    function list_partida()
    {
        
        $base_where=array();
        
        $base_where['id_concepto']=$this->input->post('id_concepto');
        
       // $base_where['id_actividad_poa']=$this->input->post('id_actividad_poa');
        
        $partidas = $this->partidas_m->order_by('no_partida')->get_many_by($base_where);
        
        foreach($partidas as &$partida)
        {
            $partida->nombre=$partida->no_partida.' - '.$partida->nombre;
        }
        
        if($partidas) echo json_encode($partidas);
    }
    public function action()
	{
		switch ($this->input->post('btnAction'))
		{
			
			case 'delete':
				$this->delete();
				break;

			default:
				redirect('admin/fondo');
				break;
		}
	}
    function list_directores()
    {
        
        $id_centro = $this->input->post('id_centro');
        
        $result = $this->db->select('id,nombre,fecha_fin,fecha_ini')
                        ->order_by('fecha_ini','DESC')
                        ->where('id_centro',$id_centro)
                        ->get('directores')
                        ->result();
        
        foreach($result as &$row)
        {
            /*$row->vigente = false;
            
            if(strtotime($row->fecha_fin) >=  strtotime(date('Y-m-d')) && strtotime($row->fecha_ini) <=  strtotime(date('Y-m-d')))
            {
                $row->vigente = true;
            }*/
            
            $row->vigencia = format_date_calendar($row->fecha_ini,'short').' al '.format_date_calendar($row->fecha_fin,'short');
            
        }
        
        if($result)echo json_encode($result);
    }
    function facturacion()
    {
        
        $xml = new DOMDocument();
        $xsl = new DOMDocument();
        $proc = new XSLTProcessor;
        
        $cer = __DIR__.'/carb801112h55.cer';
        $pem = __DIR__.'/carb801112h55.pem';
        
        $xml->load( __DIR__.'/302b30df-9fd6-432e-b0ad-1c1f3afa3ce8.xml');
        $xsl->load(__DIR__.'/cadenaoriginal_3_2.xslt');
        
        //$xml = preg_replace('{<Addenda.*/Addenda>}is', '<Addenda/>', $xml);
        //$xmk = preg_replace('{<cfdi:Addenda.*/cfdi:Addenda>}is', '<cfdi:Addenda/>', $xml);
        
        
        $proc->importStyleSheet($xsl); 
        $cadena = $proc->transformToXML($xml); //sha1($proc->transformToXML($xml));
        echo $cadena;
        //exec('openssl x509 -inform der -in '.$cer.' -out '.$pem);
    }
    
    ///Actualizacion de la columna anio del default_fondo
    function update1()
    {
        $result = $this->db->select('fondo.id,cat_conceptos.anio')
                        ->join('cat_conceptos','fondo.id_concepto=cat_conceptos.id')
                        ->get('fondo')->result();
                
        $index = 1;  
        foreach($result as $item)
        {
            echo $index.'.- Actualizado: '.$item->id.' '.$item->anio.'<br/>';
            $this->db->where('id',$item->id)->set(array(
                'anio' => $item->anio
            
            ))->update('fondo');
            $index++;
        }
        
        
    }
 }
 ?>