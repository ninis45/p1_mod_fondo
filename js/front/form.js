$(document).ready(function(){
    
    $('body').delegate('.remove','click',function(){
        
        /*var element = $(this),
              label = $(this).closest('label'),
               type = $(this).data('type');
               
        alert(label);
        
        label.find('span').html('Buscar archivo '+type);
        label.find('input[type="hidden"]').val('');
        label.prepend('<input data-file="xml" type="file" name="file_xml"  />');*/
        
        
        
    });
    /*$('body').delegate('input[name="file_xml"]','change',function(){
        var file_data = $(this)[0].files[0];
        
        
        fondo.upload_file($(this),file_data);
    });
    
     $('body').delegate('input[name="file_pdf"]','change',function(){
        var file_data = $(this)[0].files[0];
        
       
        fondo.upload_file($(this),file_data);
    });*/
    
    ///$('#form').one('submit',fondo.upload_file)
    var $select_concepto = $('select[name="proyecto"]').selectize({ 
        
        valueField: 'id',
        labelField: 'nombre',
        options:option_conceptos,
       
       
    });
    
    $select_concepto[0].selectize.setValue(fondo.id_concepto);
    var $select_partida = $('select[name="partida"]').selectize({ 
        
        valueField: 'id',
        labelField: 'nombre',
        options:option_partidas,
       
    });
    
    $select_partida[0].selectize.setValue(fondo.id_partida);
    $('select[name="proyecto"]').on('change',function(){
       
            
            
        $.get('/fondo/list_partida',{concepto:$(this).val()},function(response){
            
            var selectize = $select_partida[0].selectize;
            selectize.clearOptions();
            $.each(response,function(index,item){
                
                
               
               selectize.addOption({id:item.id,nombre:item.nombre});
            });
        });
    });
    $('select[name="actividad"]').on('change',function(){
        
        var element = $(this),
            anio    = element.data('anio');
        $.get('/fondo/list_concepto',{actividad:$(this).val(),anio:anio},function(response){
            
            var html = '<option value=""> Elegir concepto</option>';
            var opetions = [];
            var selectize = $select_concepto[0].selectize;
            selectize.clearOptions();
            $.each(response,function(index,item){
                
               
               
               selectize.addOption({id:item.id,nombre:item.nombre});
            });
            
        });
        
    });
});

var fondo2 = {
    element  :false,
    progress : function(element,loaded)
    {
        var dom_progress = $(element).parent('.input-factura').find('.progress');
        if(typeof loaded == 'undefined')
        {
            loaded = 0;
        }
        
        if(loaded>0)
        {
            dom_progress.show();
        }
        if(loaded == 100)
        {
            dom_progress.hide();
        }
        var percent = Math.ceil(loaded)+'%';
        dom_progress.find('.progress-bar').css('width',percent).html(percent);
    },
    upload_file:function(element,file)
    {
        //event.stopPropagation(); // Stop stuff happening
        //event.preventDefault(); // Totally stop stuff happening
        
        var data   = new FormData();
        var parent = $(element).parent('label')
            type   = $(element).data('file'),
            html   = ' | <a href="javascript:;" data-type="'+type+'" class="remove">Eliminar</a>';
      
        data.append('file',file);
        data.append('type',type);
        $.ajax({
            url: '/fondo/upload',
            type: 'POST',
            data: data,
           
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            enctype: 'multipart/form-data',
            success: function(data, textStatus, jqXHR)
            {
                console.log(data);
                parent.find('div.jumbotron').append(data.message);
                if(data.status == false)
                {
                   // $('#notices-modal').html(data.message);
                   // $('#btn-save').attr('disabled',false);
                }
                else
                {
                    //location.href = action_redirect;
                    parent.find('input[type="hidden"]').val(data.data.id);
                    parent.find('span').html(data.data.name+' '+html);
                    $(element).remove();
                }
                if(typeof data.error === 'undefined')
                {
                    // Success so call function to process the form
                    //submitForm(event, data);
                }
                else
                {
                    // Handle errors here
                    console.log('ERRORS: ' + data.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
                // STOP LOADING SPINNER
            },
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    // For handling the progress of the upload
                    myXhr.upload.addEventListener('progress', function(e) {
                       
                        if (e.lengthComputable) {
                            /*$('#counter').val(e.loaded);
                            $('progress').attr({
                                value: e.loaded,
                                max: e.total,
                            });*/
                            var loaded = e.loaded*100/e.total;
                            fondo.progress(element,loaded);
                        }
                    } , false);
                }
                
               
                return myXhr;
            },
        });
    }
    
}