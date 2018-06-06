
<div class="content" ng-controller="IndexCtrl">
        <legend class="lead text-success"><?php echo lang('fondo:title') ?>  <?=$anio?></legend>

    	<?php echo form_open('admin/fondo/'.$anio, 'class="form-inline" method="get" ') ?>
    		
    			
                <div class="form-group col-md-5">
    				<label for="f_concepto">Centro</label>
                    
                    <?=form_dropdown('f_centro',array(''=>' [ Todos ] ')+$centros,false,'class="form-control"')?>
    			</div>
    			<div class="form-group col-md-5">
    				<label for="f_keywords"><?php echo lang('global:keywords') ?></label>
    				<?php echo form_input('f_keywords', '', 'style="width: 60%;" class="form-control" placeholder="Buscar ID o Concepto"') ?>
    			</div>
    
    			<button class="md-raised btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
    			
    			
    		
    	<?php echo form_close() ?>
        <hr />
        <?php if($items):?>
       
        <?php echo form_open('admin/fondo/action');?>
        
        <div class="ui-tab-container ui-tab-vertical">
           
            
        	<uib-tabset justified="false" class="ui-tab" active="active"  >
                   <?php $index = 0;?>
                   <?php foreach($data as $mes=>$item){?>
        	        <uib-tab index="<?=$index?>"  heading="<?=month_long($mes)?>"    select="set_mes(<?=$index?>)"   >
                          <div style="min-height: 600px;">
                          <h4 class="text-success"><?=month_long($mes).' '.$anio?></h4>
                          <hr />
                         <uib-accordion close-others="oneAtATime"  class="ui-accordion">
                            
                            <?php foreach($item as $centro => $items){?>
                            <?php $total_importe = 0; ?>
                            
                            <uib-accordion-group    heading="<?=$centro?> (<?=count($items)?>)"  > 
                               
                                <p class="text-right text-muted">Total registros: <?=count($items)?> </p>
                                 <table   class="table" summary="catalogos"  width="100%">
                                     <thead>
                                         <tr>
                                           <!--th width="3%">
                                           		<label>
                                                <?php echo  form_checkbox(array(
                                                            'value'=>'1',
                                                            'ng-model'=>'checked_all',
                                                            'class'=>'check-all',
                                                            
                                                            ));?>
                                                         <span class="lbl"></span>	
                                                </label>
                                           </th-->
                                           <th width="8%">ID</th>
                                          
                                          
                                           <th>#Factura</th>
                                           <th>Concepto</th>
                                           
                                           <th width="5%">XML</th>
                                           <th width="8%">Importe</th>
                                           <th width="16%">Acciones</th>
                                         </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($items as $fondo){?>
                                                <?php $total_importe+= $fondo->importe;?>
                                                <tr class="<?=is_numeric($fondo->autorizado)?($fondo->autorizado==0?'danger':'success'):''?>">
                                                    <!--td align="center" class="center">
                                      	 
                                                      <?php echo  form_checkbox(array(
                                                                  'ng-checked'=>'checked_all',
                                                                  'name'=>'action_to[]',
                                                                  'value'=>$row->id
                                                                  
                                                            ));
                                                 
                                                      ?>	
                                                      	 
                                                     	
                                                   </td>
                                                   <td><?=$fondo->id?></td-->
                                                   <td><a href="<?=base_url('admin/fondo/details/'.$fondo->id)?>"><?=$fondo->id?></a></td>
                                                  
                                                   <td><?=$fondo->no_factura?></td>
                                                   <td ><?=$fondo->concepto?></td>
                                                  
                                                   <td><?=$fondo->xml?'<i class="fa fa-check-square text-success"></i>':''?></td>
                                                 
                                                   <td class="text-right"><?=number_format($fondo->importe,2)?></td>
                                                   <td class="center">
                                                       
                                                        <?php echo anchor('admin/fondo/edit/'.$fondo->id, '<i class="fa fa-pencil"></i>', 'class="button btn-icon  btn-icon-sm btn-primary" ui-wave ') ?> 
                                                        
                                                        
                                                        <?php echo anchor('admin/fondo/delete/'.$anio.'/'.$fondo->id,'<i class="fa fa-trash"></i>', 'confirm-action class="button btn-icon  confirm btn-icon-sm btn-danger" ui-wave') ?>
                                                   </td>
                                                </tr>
                                            
                                            <?php }?>
                                        
                                        </tbody>
                                         <tfoot>
                                                  <tr>
                                                    <th colspan="4" class="text-right">Total:</th>
                                                    <th class="text-right"><?=number_format($total_importe,2)?></th>
                                                  
                                                    <th></th>
                                                  </tr>
                                          </tfoot>
                                    </table>
                            </uib-accordion-group>
                            <?php }?>
                         </uib-accordion>
                         </div>
                    </uib-tab>
                    <?php $index++;?>
                    <?php }?>
                    
            </uib-tabset>
        
        </div>
        
        <?php // $this->load->view('admin/partials/pagination') ?>
        <div class="divider"></div>
        <!--div class="table_action_buttons">
	
			
				<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))) ?>
			
		</div-->
        <input type="hidden" value="admin/fondo/<?=$anio?>" name="url_return" /> 
        <?php echo form_close();?>
        <?php else:?>
        <section class="alert alert-info text-center">
    				<?php echo lang('global:not_found');?>
        </section>
        <?php endif;?>
</div>