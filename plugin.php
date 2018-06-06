<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Search Plugin
 *
 * Use the search plugin to display search forms and content
 *
 * @author  PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Search\Plugins
 */
class Plugin_Fondo extends Plugin
{
	public $version = '1.0.0';
    
    
    public function listing()
    {
        $autorizado = $this->attribute('autorizado','1');
        $user       = $this->current_user->id;
        
        
        $result = $this->db->where(array(
                                   'autorizado' => $autorizado,
                                   'user_id'    => $user
                             ))
                            ->get('fondo')->result();
                            
                            
        return $result;
    }
    
    public function form()
    {
        //$action             = $this->attribute('action', current_url());
        $this->load->library('form_validation');
		$this->load->helper('form');
        $field_list = array(
        
            'factura'=>array(
            
                'label' => 'No. de factura',
                'type'  => 'input',
                'rules' => 'trim|required'
            ),
            'importe'=>array(
            
                'label' => 'Importe',
                'type'  => 'input',
                'rules' => 'trim|required'
            ),
            'concepto'=>array(
            
                'label' => 'Concepto',
                'type'  => 'text',
                'rules' => 'trim|required'
            ),
            'proveedor'=>array(
            
                'label'   => 'Proveedor',
                'type'    => 'dropdown',
                'options' => 'func:functions/option_proveedores',
                'rules' => 'trim|required'
            ),
            
            'actividad'=>array(
            
                'label'   => 'Actividad',
                'type'    => 'dropdown',
                'options' => 'func:fondo/option_actividades',
                'rules' => 'trim|required'
            ),
            'pdf'=>array(
            
                'label'   => 'Archivo PDF',
                'type'    => 'file',
                
                'rules' => 'trim'
            ),
            'xml'=>array(
            
                'label'   => 'Archivo XML',
                'type'    => 'file',
                
                'rules' => 'trim'
            )
        );
        
        
        $output  = form_open_multipart();
        //$output .= '<div class="row">';
       	
        foreach ($field_list as $field => $properties)
		{
		 
		  $output .= '<div class="form-group">';
          $output .= '<label>'.$properties['label'].'</label>';
		  switch($properties['type'])
          {
                case 'input':
				
                    
                    $output .= form_input($field,'','class=""');
				break;
				case 'text':
					///$form_meta[$field]['type'] = 'input';
                    $output .= form_textarea(array('name'=>$field,'class'=>'','rows'=>3));
				break;
				case 'file':
					///$form_meta[$field]['type'] = 'input';
                    $output .= form_upload($field);
				break;
				case 'dropdown':
                    	if (substr($properties['options'], 0, 5) == 'func:')
            			{
            				$func = substr($properties['options'], 5);
            
            				if (($pos = strrpos($func, '/')) !== false)
            				{
            					$helper	= substr($func, 0, $pos);
            					$func	= substr($func, $pos + 1);
                               
            					if ($helper)
            					{
            						ci()->load->helper($helper);
            					}
            				}
                            
                           
            				if (is_callable($func))
            				{
            					// @todo: add support to use values scalar, bool and null correctly typed as params
            					$options = call_user_func($func);
            				}
            				else
            				{
            				    $options = array();
            					//$setting->options = array('=' . lang('global:select-none'));
            				}
            			}
                        
                        $output .= form_dropdown($field,array(''=>'Elegir')+$options,null,'onchange="list_'.$field.'()"');
                        $output .='<script>function list_'.$field.'(){$.post(\''.base_url('conceptos/partida').'\')}</script>';
					//$form_meta[$field]['type'] = 'textarea';
				break;
          }
          
          $output .= '</div>';
        }
		//$output .='</div>';
		$output .= form_close();
        
        
        return $output;
    }
} 
?>