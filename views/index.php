<section>
    <div class="container">
         <header><h2>Fondo revolvente</h2></header>
        {{ theme:partial name="notices" }}
        
        <?php if(!$director){ ?>
            <div class="alert alert-danger">
                <?=lang('fondo:error_access')?>
            </div>
        <?php }else{?>
        <?=form_open('','class="form-inline" method="get"');?>
            <div class="form-group">
                
                <?=form_dropdown('anio',array(''=>'[ Año ]')+$anios,null,'class="selectize" style="width:100px;"')?>
                
            </div>
            
            
            <div class="form-group">
                
                <?=form_dropdown('mes',array(''=>'[ Mes ]')+$months,null,'class="selectize" style="width:150px;"')?>
                
            </div>
            <div class="form-group">
                
                <?=form_input('keywords','','class="" placeholder="No. de factura o concepto" style="width:300px;"')?>
                
            </div>
            <button type="submit" class="btn btn-color-grey-light"><i class="fa fa-search"></i> Buscar</button>
            <?php if(count($base_where) > 1) {?>
                <a href="<?=base_url('fondo')?>" class="btn"><i class="fa fa-refresh"></i> Mostrar todos</a>
            <?php }?>
            <a href="<?=base_url('fondo/create')?>" class="btn  pull-right">Nueva solicitud</a>
        <?=form_close();?>
        <hr />
         <?php if(count($base_where) > 1 && count($items) == 0) {?>
         <div class="alert alert-info text-center"><?=lang('fondo:not_found_query');?></div>
         <?php }else{?>
        <p class="text-right">Total registros: <?php echo $pagination['total_rows'];?></p>
        <table class="table table-hover course-list-table tablesorter">
            <thead>
                <tr>
                    
                    <th width="10%"># Factura</th>
                    <th width="12%">Mes</th>
                    <th>Concepto</th>
                    <th width="12%">Estado</th>
                    <th width="16%"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item){?>
                <tr class="<?=is_numeric($item->autorizado)==false?'':($item->autorizado==1?'success':'danger')?>">
                    <td><a href="<?=base_url('fondo/details/'.$item->id)?>"><?=$item->no_factura?></a></td>
                    <td><?=$item->anio?> - <?=strtoupper(month_long($item->mes))?></td>
                    <td><?=$item->concepto?></td>
                    <td><?=is_numeric($item->autorizado)?($item->autorizado==1?'<i class="fa fa-check text-success"></i> Autorizado':'<i class="fa fa-close text-danger"></i> Rechazado'):'<i class="fa fa-clock-o text-primary"></i> Proceso'?></td>
                    <td>
                        <a href="<?=base_url('fondo/details/'.$item->id)?>">Detalles</a>
                        |
                        <?php if($item->es_activo && $item->autorizado === '0'){ ?>
                           
                            <a href="<?=base_url('fondo/edit/'.$item->id)?>">Modificar</a>
                            
                        <?php }else{?>
                        <span class="text-muted">Modificar</span>
                        <?php }?>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
        <p><i class="fa fa-square text-danger"></i>  No autorizados <i class="fa fa-square text-success"></i>  Autorizados</p>
        <p><?=$pagination['links']?></p>
        <?php }?>
        <?php }?>
    </div>
</section>

