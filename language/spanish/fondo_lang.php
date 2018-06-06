<?php defined('BASEPATH') OR exit('No direct script access allowed');
$lang['fondo:title']			=	'Fondo revolvente';
$lang['fondo:unautorized']		=	'Esta solicitud aun no se encuentra validada, se recomienda realizarla para generar el informe completo. <a href="'.base_url('admin/fondo/edit/%s').'">Modificar</a> ';

$lang['fondo:report']			=	'Reporte';
$lang['fondo:create']			=	'Agregar';
$lang['fondo:edit']		     	=	'Modificar la solicitud';
$lang['fondo:on_edit']		     	=	'Modificando la solicitud con el número de factura: <strong>%s</strong>';
$lang['fondo:details']			=	'Detalles de  la solicitud';
$lang['fondo:save_success']			=	'Los datos del concepto %s han sido guardados satisfactoriamente';
$lang['fondo:not_found_query']			=	'La consulta no trajo resultados. Realiza nuevamente la búsqueda con otros parámetros.';


$lang['fondo:unvalide']			=	'El archivo XML aún no esta validado, para realizar dicha validación haga clic <a href="'.base_url('admin/fondo/valid_xml/%s/%s').'">aqui</a>';
$lang['fondo:xml_not'] = 'El archivo XML es requerido';

$lang['fondo:help_create']		     	=	'<p>- Por favor llena los campos requeridos en Datos generales y pulsa el botón <em>Siguiente</em>.</p><p>- Selecciona y carga los documentos que comprueba tu solicitud y a continuación pulsa el boton <em>Terminar y enviar</em></p>';

$lang['fondo:add_question']			=	'¿DESEAS AGREGAR OTRA SOLICITUD?';

$lang['fondo:delete_success']			=	'Los conceptos %s han sido eliminados satisfactoriamente';

$lang['fondo:error_mes']			=	'La solicitud ya no se encuentra vigente para este mes.';

$lang['fondo:error_folder']			=	'La carpeta facturación aun no ha sido creada';

$lang['fondo:error_cadena']			=	'Error al tratar de formar la cadena';
$lang['fondo:success_cadena']			=	'Cadena formada correctamente';

$lang['fondo:error_cert']			=	'Certificado interno incorrecto, descargarlo del FTP del sat';
$lang['fondo:success_cert']			=	'Certificado interno correcto';

$lang['fondo:error_report']			=	'Aun quedan solicitudes por validar, te sugerimos atenderlos y posteriormente generar el reporte. <a href="'.base_url('admin/fondo').'/%s">Ir al panel</a>';
$lang['fondo:error_month']			=	'Este mes se encuentra bloqueado, intentalo nuevamente con otro o el siguiente.';

$lang['fondo:error_xml']			=	'Estructura del XML no válida';
$lang['fondo:error_uuid']			=	'El folio fiscal de esta factura ya se encuentra registrado por el centro/plantel %s. ID de la factura: %s ';
$lang['fondo:error_timbrado']			=	'La factura no tiene timbrado';


$lang['fondo:success_sello']			=	'Sello interno correcto';
$lang['fondo:error_sello']			=	'Sello interno incorrecto';

$lang['fondo:error_access']			= 'La cuenta de usuario no esta completamente vinculada a una cuenta de director, te recomendamos que nos envies un mensaje al correo <a href="mailto:informacion@cobacam.edu.mx">informacion@cobacam.edu.mx</a> reportando este problema.';



$lang['partidas:title']			=	'Partidas';
$lang['partidas:create']			=	'Agregar partida';
$lang['partidas:save_success']			=	'Los datos de la partida %s han sido guardados satisfactoriamente';
$lang['partidas:delete_success']			=	'Las partidas %s han sido eliminados satisfactoriamente';

$lang['fondo:welcome_report']			=	'Bienvenido al panel de reportes, por favor introduce el año y el mes a buscar en los campos de abajo y a continuación haz clic en <em>Buscar</em>';

?>