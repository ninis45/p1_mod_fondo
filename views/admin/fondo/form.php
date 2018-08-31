<div ng-controller="InputCtrl" class="panel panel-body">
    <div class="lead text-success"><?=lang('fondo:'.$this->method)?></div>
    <?php if($fondo->xml_messages){?>
        
        <!--uib-alert  type="danger" >    
        <?php foreach(json_decode($fondo->xml_messages) as $valid){?>
            <?php if($valid->code==0){ ?>
           <i class="fa <?=$valid->code?'fa-check-circle text-success':'fa-exclamation-triangle text-danger';?>"></i> <?=$valid->message;?>
            <?php }?>
        <?php }?>
        </uib-alert-->
        
    <?php }else if($fondo->xml && !$_POST){?>
    <div class="alert alert-warning">
        <?=sprintf(lang('fondo:unvalide'),$fondo->id,$fondo->attach['xml']->id)?>
    </div>
    <?php }?>
    
    <?php if(is_numeric($fondo->autorizado)==false && $this->method == 'details'){ ?>
        <div class="alert alert-info"><?=sprintf(lang('fondo:unautorized'),$fondo->id)?></div>
    <?php }?>
    <?php echo form_open_multipart(uri_string(), 'name="frm_fondo" ng-init="anio='.$anio.'" data-mode="'.$this->method.'"',array('id'=>$id)); ?>
    <div class="ui-tab-container ui-tab-horizontal">
        
        
    	<uib-tabset justified="false" class="ui-tab">
    	        <uib-tab heading="Información General">
                    <fieldset>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>No. Factura</label>
                                    <?=form_input('no_factura',$fondo->no_factura,'class="form-control" '.($this->method == 'details'?'disabled':''));?>
                                </div>
                                <div class="form-group">
                                    <label>Proveedor</label>
                                    
                                    <?=form_dropdown('id_proveedor',array(''=>' [ Elegir ] ')+$proveedores,$fondo->id_proveedor,'class="form-control" '.($this->method == 'details'?'disabled':''));?>
                                </div>
                                
                               
                                 <div class="form-group">
                                    <label>Importe</label>
                                    
                                    <input type="text"  format-decimal class="form-control" ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" required ng-init="f_importe='<?=$fondo->importe?number_format($fondo->importe,2,'.',''):'0.00'?>'"  name="importe" data-ng-model="f_importe" <?=$this->method == 'details'?'disabled':''?>/> 
                                    <p class="text-danger" ng-show="frm_fondo.importe.$error.pattern">El importe debe ser un número entero o decimal con dos dígitos </p>
                                </div>
                                <input type="hidden" value="<?=$anio?>" name="anio" />
                                
                            </div>
                            <div class="col-md-6">
                               
                                
                                <div class="form-group">
                                    <label>Centro</label>
                                    
                                    <?=form_dropdown('id_centro',array(''=>' [ Elegir ] ')+$centros,$fondo->id_centro,'class="form-control" ng-init="f_centro.selected=\''.$fondo->id_centro.'\'" ng-model="f_centro.selected" '.($this->method == 'details'?'disabled':''));?>
                                </div>
                                <div class="form-group">
                                        <label>Director</label>
                                        <select name="id_director" ng-options="f_director.nombre+' - '+f_director.vigencia for f_director in f_directores track by f_director.id"  <?=$this->method == 'details'?'':'ng-disabled="!f_directores"'?>  class="form-control" ng-init="f_director.selected={id:'<?=$fondo->id_director;?>'}" ng-model="f_director.selected" <?=$this->method == 'details'?'disabled':''?>>
                                            <option value=""> [ Elegir ] </option>
                                            
                                        </select>
                                </div>
                                <div class="form-group">
                                        <label>Mes</label>
                                        <?php echo form_dropdown('mes',$months,$fondo->mes?$fondo->mes:date('m'),'class="form-control" '.($this->method == 'details'?'disabled':''));?>
                                        <input type="hidden" value="<?=$anio?>" name="anio" />
                                </div>
                                 
                            </div>
                        </div>
                        
                         <div class="form-group">
                                    <label>Concepto</label>
                                    <?=form_textarea('concepto',$fondo->concepto,'class="form-control" '.($this->method == 'details'?'disabled':''));?>
                         </div>
                         <div class="row">
                        
                             <div class="col-lg-3 form-group panel" ng-init="f_autorizado=<?=is_numeric($fondo->autorizado)?$fondo->autorizado:1?>">
                                        <label>Autorizado</label>
                                        <label class="radio-inline">
                                        
                                            <input type="radio" value="1"  name="autorizado" ng-click="f_autorizado=1" ng-checked="f_autorizado==1" <?=$this->method == 'details'?'disabled':''?> />Si
                                            
                                        </label>
                                        <label class="radio-inline">
                                        
                                            <input type="radio" value="0"  name="autorizado" ng-click="f_autorizado=0"  ng-checked="f_autorizado==0" <?=$this->method == 'details'?'disabled':''?>/>No
                                        </label>
                             </div>
                             <div class="col-lg-9  form-group" ng-if="!f_autorizado">
                                <label>Motivo</label>
                                <?=form_input('motivo',$fondo->motivo,'class="form-control"  ');?>
                                <p class="help-block">Exponga la razón o motivo por el cual no fue autorizado</p>
                             </div>
                          </div>
                         
                       
                   </fieldset>  
                 </uib-tab>
                 <uib-tab heading="Datos de Partida">
                        <div class="form-group" ng-init="f_actividad.selected='<?=$fondo->id_actividad_poa?>'">
                            <label>Actividad POA</label>
                            
                            <?=form_dropdown('id_actividad_poa',array(''=>'Selecciona la actividad')+option_actividades(),$fondo->id_actividad_poa,'class="form-control"  ng-model="f_actividad.selected" '.($this->method=='details'?'disabled':''))?>
                        </div>
                        <div class="form-group">
                            <label>Proyecto/Concepto</label>
                            
                            
                           
                            
                            
                            <select class="form-control" ng-options="f_concepto.nombre for f_concepto in f_conceptos track by f_concepto.id" name="id_concepto"  ng-init="f_concepto.selected={id:'<?=$fondo->id_concepto?>'}"   ng-model="f_concepto.selected" <?=$this->method=='details'?'disabled':'ng-disabled="!f_conceptos"'?>  >
                                 <option value=""> [ ELEGIR ] </option>
                            </select>
                             
                            
                        </div>
                        
                        <div class="form-group">
                            <label>Partida</label>
                            <select class="form-control" ng-options="f_partida.nombre for f_partida in f_partidas track by f_partida.id " name="id_partida"  ng-init="f_partida.selected={id:'<?=$fondo->id_partida?>'}" ng-model="f_partida.selected" <?=$this->method=='details'?'disabled':'ng-disabled="!f_partidas"'?>>
                                <option value=""> [ ELEGIR ] </option>
                                
                            </select>
                            
                        </div>
                        
                 </uib-tab>
                 <uib-tab heading="Archivo XML y PDF">
                    
                    <div class="alert alert-info">
                        - Validador de forma y sintaxis de Comprobantes Fiscales Digitales v3.0 y v3.2<br />
                        - Para mayor información consultar <a target="_blank" href="https://www.consulta.sat.gob.mx/sicofi_web/moduloECFD_plus/ValidadorCFDI/Validador%20cfdi.html">https://www.consulta.sat.gob.mx/sicofi_web/moduloECFD_plus/ValidadorCFDI/Validador%20cfdi.html</a>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Archivo XML</label>
                                <?php $validacion =false;?>
                                <?php if($fondo->xml):?>
                                     <?php $validacion = json_decode($fondo->xml_messages);?>
                                     
                                     <div class="block-file" ng-if="hide.xml">
                                         
                                         <a class="file_link" uib-tooltip="Descargar xml" href="<?=base_url('files/download/'.$fondo->attach['xml']->id);?>">
                                            <i class="fa fa-file text-success"></i>
                                            <span><?=$fondo->attach['xml']->name;?></span>
                                         </a>
                                         <input type="hidden" name="xml" value="<?=$fondo->xml?>" />
                                         |
                                         <a  href="javasctipt:;"  class="file_remove" role="button" ng-click="hide.xml=false;" tabindex="0">
                                            Eliminar 
                                         </a> 
                                         |
                                         
                                            <a title="Validar nuevamente el XML" href="<?=base_url('admin/fondo/valid_xml/'.$fondo->id.'/'.$fondo->attach['xml']->id)?>">Validar</a>
                                         
                                         
                                         
                                         <input type="hidden" value="<?=$fondo->xml?>" name="xml" /> 
                                         
                                         
                                     </div>
                                    
                                    
                                   
                                <?php endif;?>
                                <?=form_upload('xml_file','','accept=".xml"')?>
                                     <hr />
                                     <?php if($validacion){ ?>
                                     <div class="well">
                                         <h5>Acerca del archivo XML</h5>
                                         <ul class="list-unstyled">
                                                      <?php foreach(json_decode($fondo->xml_messages) as $valid){?>
                                                      <li> - <i class="fa <?=$valid->code?'fa-check-circle text-success':'fa-exclamation-triangle text-danger';?>"></i> <?=$valid->message;?></li>
                                                      <?php }?>
                                                      
                                         </ul>
                                     </div>
                                     <?php }?>
                                <p class="help-block">Para versiones 3.2 y 3.3</p>
                            </div>
                        
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Archivo PDF</label>
                                <?php if($fondo->pdf):?>
                                <div class="block-file"  ng-if="hide.pdf">
                                     <a class="file_link"   href="<?=base_url('files/download/'.$fondo->attach['pdf']->id);?>">
                                            <i class="fa fa-file"></i>
                                            <span><?=$fondo->attach['pdf']->name;?></span>
                                     </a>
                                      |
                                      <span  class="file_remove" role="button" ng-click="hide.pdf=false;" tabindex="0">
                                            Eliminar 
                                      </span>
                                     <input type="hidden" name="pdf"  value="<?=$fondo->pdf?>" />
                                </div>
                                <?php endif;?>
                                <?=form_upload('pdf_file',NULL,'accept=".pdf"')?>
                            </div>
                        
                        </div>
                    </div>
                    
                    
                    
                    
                 </uib-tab>
            </uib-tabset>
        </div>   
        
        <div class="divider"></div>
    
       <div class="buttons">
    	     <?php $this->load->view('admin/partials/buttons', array('buttons' => array($this->method !='details'?'save':'', 'cancel'))) ?>
       </div>
    <?php echo form_close();?>
</div>