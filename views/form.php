<section>
     <div class="container">
         <header><h2>Fondo revolvente</h2></header>
         {{ theme:partial name="notices" }}
         <div class="row">
            
            <div class="col-md-6 col-md-offset-1">
                
                <?php if(validation_errors()){?>
                    <div class="alert alert-danger"><?=validation_errors()?></div>
                <?php }?>
                
               
                <?=form_open_multipart('','id="form"');?>
                <ul class="nav nav-tabs" id="tabs">
                        <li class="active"><a href="#tab-general" data-toggle="tab">Datos generales</a></li>
                        <!--li><a href="#tab-partida" class="disabled" data-toggle="tab">Partidas</a></li-->
                        <li><a href="#tab-documento" data-toggle="tab">Documentos</a></li>
                </ul>
                
                <div class="tab-content my-account-tab-content">
                        <div class="tab-pane active" id="tab-general">
                                    <div class="form-group">
                                        <label>* No. Factura</label>
                                        <?=form_input('no_factura',$fondo->no_factura,'class="form-control" '.($this->method=='details'?'disabled':''));?>
                                        
                                        <?=form_error('no_factura','<span class="text-danger">','</span>')?>
                                    </div>
                                    <div class="form-group">
                                        <label>* Proveedor</label>
                                        
                                        <?=form_dropdown('proveedor',array(''=>' [ Elegir ] ')+$proveedores,$fondo->proveedor,'class="selectize" '.($this->method=='details'?'disabled':''));?>
                                        <?=form_error('proveedor','<span class="text-danger">','</span>')?>
                                    </div>
                                    
                                   
                                     <div class="form-group">
                                        <label>* Importe</label>
                                        
                                        <input type="text" value="<?=$fondo->importe?number_format($fondo->importe,2,'.',''):'0.00'?>"  class="form-control"  name="importe" <?=$this->method=='details'?'disabled':''?> /> 
                                        <?=form_error('importe','<span class="text-danger">','</span>')?>
                                    </div>
                                    <div class="form-group">
                                        <label>* Concepto</label>
                                        <?=form_textarea('concepto',$fondo->concepto,($this->method=='details'?'disabled':''));?>
                                        <?=form_error('concepto','<span class="text-danger">','</span>')?>
                                    </div>
                                    <hr />
                                     <div class="form-group">
                                        <label>* Año</label>
                                        <?=form_input('anio_input',$anio,'class="form-control" disabled');?>
                                     </div>
                                    <div class="form-group">
                                        <label>* Mes</label>
                                        <?php echo form_dropdown('mes',array(''=>'Selecciona  el mes')+$months,$fondo->mes?$fondo->mes:date('m'),'class="form-control selectize" '.($this->method=='details'?'disabled':''));?>
                                        <?=form_error('mes','<span class="text-danger">','</span>')?>
                                    </div>
                                    <input type="hidden" value="<?=$anio?>" name="anio" />
                                    <?php if($this->method !='details'): ?>
                                    <hr />
                                    <div class="form-actions">
                                        <a href="<?=base_url('fondo')?>" class="btn btn-color-grey-light"><i class="fa fa-list"></i> Ir a listado</a>
                                        
                                        <a href="#" onclick="$('#tabs li:eq(1) a').tab('show')" class="btn btn-color-grey-light">Siguiente</a>
                                        
                                    </div>
                                    <?php endif;?>
                        </div><!-- /tab-pane -->
                        <!--div class="tab-pane" id="tab-partida">
                                    <div class="form-group">
                                        <label>Actividad POA</label>
                                        <?=form_dropdown('actividad',array(''=>'Selecciona la actividad')+option_actividades(),$fondo->actividad,'class="selectize" data-anio="'.$anio.'" '.($this->method=='details'?'disabled':''))?>
                                        
                                    </div>
                                    <div class="form-group">
                                        <label>Proyecto/Concepto</label>
                                        
                                        
                                       
                                        
                                        
                                        <select class="form-control" name="proyecto" placeholder="Selecciona el concepto"   <?=$this->method=='details'?'disabled':''?>>
                                            
                                        </select>
                                         
                                        
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Partida</label>
                                        
                                        
                                       
                                        
                                        
                                        <select class="form-control" name="partida" placeholder="Selecciona la partida"  <?=$this->method=='details'?'disabled':''?> >
                                            
                                        </select>
                                         
                                        
                                    </div>
                                    <hr />
                                    <div class="form-actions">
                                        <a href="#" onclick="$('#tabs li:eq(0) a').tab('show')" class="btn btn-color-grey-light">Regresar</a>
                                        
                                        <a href="#" onclick="$('#tabs li:eq(2) a').tab('show')" class="btn">Siguiente</a>
                                        
                                    </div>
                        </div--><!-- /.tab-pane -->
                        <div class="tab-pane" id="tab-documento">
                             <div class="form-group">
                                <label>Archivo XML</label>
                                 <?php if($this->method!='details' ){ ?>
                                 <input data-file="xml" type="file" name="file_xml" accept="application/xml"  />
                                 <?php }?>
                                 <?php if($fondo->attach['xml']){ ?>
                                 <p>
                                     <a target="_blank" href="<?=$fondo->attach['xml']->path?>"><?=$fondo->attach['xml']->filename?></a>
                                 </p>
                                 <?php }?>
                                 <input data-type="xml" type="hidden" value="<?=$fondo->xml?>" name="xml" />
                                 
                                 <input  type="hidden" value="<?=$fondo->xml_uuid?>" name="xml_uuid" />
                             </div>
                             <div class="form-group">
                             
                                <label>Archivo PDF</label>
                                  
                                  <?php if($this->method!='details' ){ ?>
                                 <input data-file="pdf" type="file" name="file_pdf" accept="application/pdf" />
                                  <?php }?>
                                 <?php if($fondo->attach['pdf']){ ?>
                                 <p>
                                     <a target="_blank" href="<?=$fondo->attach['pdf']->path?>"><?=$fondo->attach['pdf']->filename?></a>
                                 </p>
                                 <?php }?>
                                 <input data-type="pdf" type="hidden" value="<?=$fondo->pdf?>" name="pdf" />
                             </div>
                            <?php if($this->method !='details'): ?>
                            <hr />
                            <div class="form-actions">
                                        <a href="#" onclick="$('#tabs li:eq(0) a').tab('show')" class="btn btn-color-grey-light">Regresar</a>
                                        
                                        
                                        
                                        <button type="submit" class="btn confirm">Terminar y enviar</button>
                                        
                            </div>
                            <?php endif;?>
                        </div>
                </div>
                <?php if($this->method == 'details'): ?>
                <div class="form-actions">
                   <a class="btn btn-color-grey-light" href="<?=base_url('fondo')?>"><i class="fa fa-list"></i> Ir a listado</a>
                   <?php if(es_activo($fondo->anio,$fondo->mes)  && $fondo->autorizado==='0'){ ?>
                   <a class="btn btn-color-grey-light" href="<?=base_url('fondo/edit/'.$fondo->id)?>">Modificar registro</a>
                    <?php }?>
                </div>
                <?php endif;?>
                                    
                <?=form_close()?>
            </div>
            <div class="col-md-4">
                 <?php if($this->method == 'edit' ){ ?>
                    <div class="alert alert-info">
                        <h4>ATENCIÓN</h4>
                        <?=sprintf(lang('fondo:on_edit'),$fondo->no_factura)?>
                    </div>
                 <?php }?>
                 <?php if($this->method == 'create' ){ ?>
                    <div class="alert alert-info">
                        <h4><i class="fa fa-question-circle"> </i> AYUDA </h4>
                        <?=lang('fondo:help_create')?>
                        
                        <hr />
                        <p>Teléfonos de oficina: <i class="fa fa-phone"></i> 81-608-11   Ext: 118</p>
                    </div>
                 <?php }?>
                 <?php if(is_numeric($fondo->autorizado) && $fondo->autorizado== 0){ ?>
                    <div class="alert alert-danger">
                        <h4>ESTADO DE LA SOLICITUD: <strong>RECHAZADO</strong> </h4>
                       
                        <?=$fondo->motivo?> 
                        
                        
                          
                    </div>
                    
                <?php }?>
                <?php if($this->method != 'create' && is_numeric($fondo->autorizado)==false){ ?>
                    <div class="alert alert-info">
                        <h4>ESTADO DE LA SOLICITUD: <strong>PENDIENTE</strong> </h4>
                        El estado de esta solicitud se encuentra en proceso de validación.
                    </div>
                <?php }?>
                
                <?php if(is_numeric($fondo->autorizado) && $fondo->autorizado== 1){ ?>
                    <div class="alert alert-success">
                        <h4>ESTADO DE LA SOLICITUD: <strong>AUTORIZADO</strong> </h4>
                        El estado de esta solicitud se encuentra autorizada y validada correctamente.
                        
                        <hr/>
                        Si deseas agregar una nueva solicitud haz clic <a href="<?=base_url('fondo/create/')?>">aquí</a>.
                    </div>
                <?php }?>
                <div  class="alert alert-info">
                    <h4>ATENCIÓN</h4>
                    Faltan 
                    
                    <?php echo ceil(($corte-now())/(60 * 60 * 24)); ?> días para el siguiente corte.
                </div>
                <?php if($this->input->get('add')){ ?>
                    <p class="text-center"><?=lang('fondo:add_question');?></p>
                    <p class="text-center"><a href="<?=base_url('fondo/create')?>" class="btn btn-small">SI</a> <a href="<?=base_url('fondo')?>" class="btn btn-color-grey-light btn-small">NO</a></p>
                    
                <?php }?>
            </div>
        </div>
    </div>
</section>