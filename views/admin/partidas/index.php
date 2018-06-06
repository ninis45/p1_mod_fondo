<section class="item">
    <?php //print_r($pagination);?> 
	<div class="content">
    
        <legend><?php echo lang('global:filters') ?></legend>

    	<?php echo form_open('admin/fondo/partidas/'.$anio, 'class="form-inline" method="get" ', array('f_module' => $module_details['slug'])) ?>
    		
    			
                <div class="form-group col-md-5">
    				<label for="f_concepto">Concepto</label>
                    <select name="f_concepto" class="form-control" style="width: 80%;">
                        <option value=""> [ TODOS ] </option>
    				    <?php echo cmb_concepto(false,NULL,NULL,array('anio'=>$anio));  ?>
                    </select>
    			</div>
    			<div class="form-group col-md-5">
    				<label for="f_keywords"><?php echo lang('global:keywords') ?></label>
    				<?php echo form_input('f_keywords', '', 'style="width: 60%;" class="form-control"') ?>
    			</div>
    
    			<button class="md-raised btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
    			
    			
    		
    	<?php echo form_close() ?>
        <hr />
        <?php if($items):?>
        <p class="text-right text-muted">Total registros: <?=$pagination['total_rows']?> </p>
        <table   class="table" summary="catalogos"  width="100%" ng-controller="TableCtrl">
             <thead>
                 <tr>
                    <th width="20%">No.</th>
                   <th width="20%">Nombre</th>
                 
                   <th>Descripción</th>
                  
                  
                   <th width="14%">Acciones</th>
                 </tr>
            </thead>
            <tbody> 
              <?php foreach($items as $row){?>      	
              <tr class="row-table">
                  
                   <td><?=$row->no_partida;?></td>
                   <td><?=$row->nombre?></td>
                   <td><?=$row->descripcion?></td>
                  
                   <td class="center">
                         
                        <?php echo anchor('admin/fondo/partidas/edit/'.$row->id, lang('buttons:edit'), 'class="button edit"') ?> |
                        <?php echo anchor('admin/fondo/partidas/delete/'.$row->id, lang('buttons:delete'), 'class="button" ng-click="confirm_delete($event)"') ?>
                   </td>
             </tr>
             <?php }?>
          </tbody>
        </table>
        	<?php $this->load->view('admin/partials/pagination') ?>
        <?php else:?>
        <div class="alert alert-info text-center">
        				<?php echo lang('global:not_found');?>
        </div>
        <?php endif;?>
    </div>
</section>
