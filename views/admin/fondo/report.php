<div class="panel panel-body" ng-controller="InputCtrl">
  
    <div class="col-lg-8 col-lg-offset-2">
        <?php if(!$output_array && $_GET):?>
            <div class="alert alert-info">Al parecer la consulta no ha traido algún resultado, te recomendamos reiniciar la búsqueda con otras fechas.<div class="divider"></div> 
                <a class="btn btn-default btn-mini" href="<?=base_url('admin/fondo/report?')?>">Reiniciar</a>
            </div>
        <?php endif;?>
        <?php if(!$_GET):?>
            <div class="alert alert-info">
                <?=lang('fondo:welcome_report');?>
            </div>
            <?php echo form_open(uri_string(),'method="get"');?>
                
                <div class="row">
                                        <div class="col-md-6">
                                            <p>Año</p>
                                            <div class="divider"></div>
                                            
                                            
                                            <?=form_input('anio',date('Y'),'class="form-control"')?>
                                           
                                            
                                            
                                        </div>
                                        <div class="col-md-6">
                                            <p>Mes</p>
                                            <div class="divider"></div>
                                            <?=form_dropdown('mes',$months,false,'class="form-control"')?>
                                        </div>
                </div>
        
                <div class="divider divider-lg divider-dashed"></div>
                <div class="row">
                    <div class="col-md-6 col-lg-offset-3">
                        <a href="<?=base_url('admin/fondo')?>" class="btn btn-default btn-w-md ui-wave">Cancelar</a>
                        <button class="btn btn-primary btn-w-md ui-wave"><span>Buscar</span></button>
                    </div>
                </div>
            <?php echo form_close();?>
        <?php endif;?>
        <?php if($output_array):?>
               <div class="alert alert-success">
                 La consulta ha traido el siguiente resultado del mes de <?=month_long($_GET['mes'])?> del año <?=$_GET['anio']?>, si  deseas realizar otra consulta haga clic en <a href="<?=base_url('admin/fondo/report?')?>">Reiniciar</a> en la parte de abajo de esta sección.
               </div>
               <?php if($error){ ?>
               <div class="alert alert-warning">
                    <?=sprintf(lang('fondo:error_report'),$_POST['anio']);?>
               </div>
               <?php }?>
               
               <p class="text-right">Total registros: <?=$total_rows?></p>
               <hr />
                <?php $total = array('autorizado'=>0,'rebote'=>0);?>
                <table class="table">
                    <thead>
                        <tr>
                        
                            <th>#Factura</th>
                            <th>Concepto</th>
                            <th width="18%" class="text-right">Autorizado</th>
                            <th width="18%" class="text-right">Rebote</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($output_array['listado'] as $plantel => $data_partida){?>
                        <?php $subtotal = array('autorizado'=>0,'rebote'=>0);?>
                        <?php foreach($data_partida as $row_partida){?>
                            <?php $row_partida->autorizado AND $total['autorizado']+=$row_partida->importe; ?>
                            <?php !$row_partida->autorizado AND $total['rebote']+=$row_partida->importe; ?>
                            <tr class="">
                                <td><a href="<?=base_url('admin/fondo/details/'.$row_partida->id)?>"><?=$row_partida->no_factura;?></a> </td>
                                <td title="[<?=$x1.pref_partida($row_partida->no_partida).$x3?>]"><?=$row_partida->concepto;?> <?=is_numeric($row_partida->autorizado)?'':'<i tooltip-placement="left" uib-tooltip="Falta validar la información de esta solicitud" class="fa fa-warning text-danger"></i>'?></td>
                                <td class="text-right"><?=$row_partida->autorizado?$row_partida->importe:'';?></td>
                                <td class="text-right"><?=$row_partida->autorizado?'':'<span class="text-red" tooltip-placement="left" uib-tooltip="'.$row_partida->motivo.'">'.number_format($row_partida->importe,2).'</span>';?></td>
                            </tr>
                            <?php $row_partida->autorizado AND $subtotal['autorizado']+=$row_partida->importe; ?>
                            <?php !$row_partida->autorizado AND $subtotal['rebote']+=$row_partida->importe?>
                        
                        <?php }?>
                            <tr class="text-success">
                                <td></td>
                                <td><strong><?=strtoupper($plantel);?></strong></td>
                                <td class="text-right"><strong><?=number_format($subtotal['autorizado'],2)?></strong></td>
                                <td class="text-right"><strong><?=number_format($subtotal['rebote'],2)?></strong></td>
                            </tr>
                    <?php }?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right"><strong>TOTAL:</strong></td>
                            <td class="text-right"><strong><?=number_format($total['autorizado'],2)?></strong></td>
                            <td class="text-right"><strong><?=number_format($total['rebote'],2)?></strong></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="divider"></div>
                <div class="form-actions" align="center">
                    <a href="<?=base_url('admin/fondo')?>" class="btn btn-default md-raised btn-w-md">Salir</a>
                    <a href="<?=base_url('admin/fondo/report?')?>" class="btn btn-default  md-raised btn-w-md">Reiniciar</a>
                    <!--a href="<?=base_url('admin/fondo/export')?>" class="btn btn-primary   md-raised btn-w-md">Exportar Excel</a-->
                    
                    <div class="btn-group" uib-dropdown is-open="status.isopen1">
                                <button ui-wave type="button" class="btn btn-w-lg btn-primary dropdown-toggle" uib-dropdown-toggle ng-disabled="disabled"> Exportar <span class="caret"></span> </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a target="_blank" href="<?=base_url('admin/fondo/export_importes?'.http_build_query($_GET))?>">Importes Erogados</a></li>
                                    <li><a target="_blank" href="<?=base_url('admin/fondo/export_partidas?'.http_build_query($_GET))?>">Partidas</a></li>
                                    
                                </ul>
                    </div>
                </div>
        <?php endif;?>
       
    </div>
    
</div>