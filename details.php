<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Groups module
 *
 * @author PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Groups
 */
 class Module_Fondo extends Module
{

	public $version = '1.0';

	public function info()
	{
		$info= array(
			'name' => array(
				'en' => 'Ads',
				
				'es' => 'Fondo Revolvente',
				
			),
			'description' => array(
				'en' => 'Information, news or warning which alerts someone of something.',
				
				'es' => 'Administración de las Partidas para el  Fondo Revolvente',
				
			),
			'frontend' => false,
			'backend' => true,
			'menu' => 'admin',
            'roles' => array(
				'create', 'edit','delete','admin_fondo_partidas'
			),
            'sections'=>array(
                'fondo'=>array(
                    'name'=>'fondo:title',
                    'ng-if'=>'hide_shortcuts',
                    'uri' => 'admin/fondo/{{ anio }}',
        			'shortcuts' => array(
        				array(
        					'name' => 'fondo:create',
        					'uri' => 'admin/fondo/create/{{ anio }}',
        					'class' => 'btn btn-success'
        				),
                        array(
        					'name' => 'fondo:report',
        					'uri' => 'admin/fondo/report',
        					'class' => 'btn btn-primary'
        				),
        			)
                )
           )
		);
        
        if (function_exists('group_has_role'))
		{
			if(group_has_role('fondo', 'admin_fondo_partidas'))
			{
			    
				$info['sections']['partidas'] = array(
							'name' 	=> 'partidas:title',
							'uri' 	=> 'admin/fondo/partidas/{{ anio }}',
							'shortcuts' => array(
									'create' => array(
										'name' 	=> 'partidas:create',
										'uri' 	=> 'admin/fondo/partidas/create/{{ anio }}',
										'class' => 'btn btn-success'
									)
							)
				);
			}
		}
        
        
        return $info;
	}

	public function install()
	{
	    $this->dbforge->drop_table('fondo');
        
		$tables = array(
		    'fondo_partidas'=>array(
				'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true,),
                'id_actividad_poa' => array('type' => 'INT', 'constraint' => 11,'null'=>true),
				'nombre' => array('type' => 'TEXT', 'null' => true,),
                'descripcion' => array('type' => 'TEXT', 'null' => true,),
                'no_partida' => array('type' => 'VARCHAR', 'constraint' => 254,),
                'id_concepto' => array('type' => 'INT', 'constraint' => 11, ),
				
            ),
            'fondo'=>array(
            
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true,),
                'no_factura' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'importe' => array('type' => 'DECIMAL','constraint' =>array(10,2), 'null' => true,),
                'id_concepto' => array('type' => 'INT','constraint' => 11, 'null' => true,),
                'id_actividad_poa' => array('type' => 'INT','constraint' => 11, 'null' => true,),
                'id_partida' => array('type' => 'INT','constraint' => 11, 'null' => true,),
                'id_centro' => array('type' => 'INT','constraint' => 11, 'null' => true,),
                'autorizado' => array('type' => 'INT','constraint' => 11, 'null' => true,),
                'pdf' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'xml' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'xml_uuid' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'anio' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'mes' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'cuenta_pasivo' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'responsable' => array('type' => 'VARCHAR','constraint' => 255, 'null' => true,),
                'concepto' => array('type' => 'TEXT', 'null' => true,),
            )
			
		);
        
        if ( ! $this->install_tables($tables))
		{
			return false;
		}

        return true;
        
		

		
	}

	public function uninstall()
	{
	  
        $this->dbforge->drop_table('fondo_partidas');
        $this->dbforge->drop_table('fondo');
		return true;
	}

	public function upgrade($old_version)
	{
		return true;
	}

}
?>