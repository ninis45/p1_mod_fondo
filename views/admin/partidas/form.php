<div ng-controller="InputCtrl" class="panel panel-body">
    <?php echo form_open(uri_string(), ' ng-init="anio='.$anio.'" data-mode="'.$this->method.'"'); ?>
    <fieldset>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Actividad POA</label>
                    <select name="id_actividad_poa" class="form-control" ng-init="f_actividad.selected='<?=$partida->id_actividad_poa?>'" ng-model="f_actividad.selected">
                        <option value=""> [ ELEGIR ] </option>
                        <?=cmb_actividad_poa($partida->id_actividad_poa);?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Concepto/Proyecto</label>
                    <select name="id_concepto" ng-options="f_concepto.nombre for f_concepto in f_conceptos track by f_concepto.id" class="form-control" ng-init="f_concepto.selected={id:'<?=$partida->id_concepto?>'}" ng-model="f_concepto.selected">
                        <option value=""> [ ELEGIR ] </option>
                        
                    
                    </select>
                </div>
                
                
                
                
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>No.Partida</label>
                    
                    <?=form_input('no_partida',$partida->no_partida,'class="form-control"');?>
                </div>
            </div>
        </div>
        <div class="form-group">
                    <label>Nombre</label>
                    <?=form_input('nombre',$partida->nombre,'class="form-control"');?>
        </div>
         <div class="form-group">
                    <label>Descripción</label>
                    <?=form_textarea('descripcion',$partida->descripcion,'class="form-control"');?>
        </div>
       
   </fieldset>     
        
        <div class="divider"></div>
    
       <div class="buttons">
    	<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))) ?>
       </div>
    <?php echo form_close();?>
</div>