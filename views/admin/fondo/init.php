<div class="row">
    <?php foreach($periodos as $periodo){?>
        <div class="col-lg-4">
            <div class="card bg-primary">
                <div class="card-content">
                                <span class="card-title"><?=$periodo->anio?></span>
                                <p></p>
                                </div>
                                <div class="card-action">
                                    <a href="<?=base_url('admin/fondo/'.$periodo->anio)?>" class="btn btn-default color-primary"><span>Administrar</span></a>
                                    
                                </div>
            </div> 
        </div>


    <?php }?>
</div>