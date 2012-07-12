// tabs
(function($){
	$.fn.tabs = function(callback){
		var $tab_nuevo = $(this);
		
		var $tab_actual = $(this).parents('ul').find('a.actual');
		
		if($tab_actual != $tab_nuevo){
			$($tab_actual.attr('href')).hide();
			$tab_actual.removeClass('actual');
			$($tab_nuevo.attr('href')).show();
			$tab_nuevo.addClass('actual');
			
			if(typeof callback != 'undefined'){
				callback.call(this);
			}
		}
	}
})(jQuery);

// alerta de errores ajax
$.ajaxSetup({error:function(x,e){
	alert(x.responseText);
}});

// funcion para insertar un archivo swf
function getSWF(filename, width, height, flashvars, params, attributes)
{
	var arrayFlvars = new Array();
	for(var i in flashvars){ arrayFlvars.push(i+'='+flashvars[i]); }
	flashvars = arrayFlvars.join('&');

	var arrayAttrs = new Array();
	for(var i in attributes){ arrayAttrs.push(i + '="' + attributes[i] + '"'); }
	attributes = arrayAttrs.join(' ');

	var html = '<object data="' + filename + '" type="application/x-shockwave-flash" width="' + width + '" height="' + height + '"' + attributes + '>';
	html += '<param name="movie" value="' + filename + '" />';
	html += '<param name="flashvars" value="' + flashvars + '" />';
	var arrayParams = new Array();
	for(var i in params){
		html += '<param name="'+i+'" value="' + params[i] + '" />';
		arrayParams.push(i+'="'+params[i]+'"');
	}
	params = arrayParams.join(' ');
	html += '</object>';

	if(navigator.userAgent.indexOf('MSIE') != -1)
	{
		return html;
	}
	else
	{
		return '<embed src="' + filename + '" type="application/x-shockwave-flash" width="' + width + '" height="' + height + '" flashvars="'+flashvars+'" '+params+' '+attributes+'></embed>';
	}
}

// plugin para manipular el envio de formularios, requiere jquery
/* formularios */
(function($){
	//
	var disablings = new Array();
	
	// version 2: llamar dentro de submit
	$.fn.enviar = function(args, callback)
	{	
		$.post($(this).attr('action'), args + '&ajax=1', function(data){callback.call(this,data)}, 'json');
		//return false;
	}
	
	$.fn.args = function()
	{
		return $(this).serialize().replace('%5B%5D', '[]');
	}
	
	$.fn.difTarget = function()
	{
		var target = $(this).attr('target');
		if(target != '' && target != '_self' && typeof target != 'undefined'){
			return true;
		}
		//return false;
	}
	
	$.fn.lock = function()
	{
		$(this).disable();
		$(this).bloquear();
	}
	
	$.fn.unlock = function()
	{
		$(this).enable();
		$(this).desbloquear();
	}
	
	$.fn.postForm = function(antes, respuesta)
	{
		$(this).submit(function(){
			var $form = $(this);
			var target = $form.attr('target');
			if(target != '' && target != '_self'/* && typeof target != 'undefined'*/){
				return true;
			}
			
			var args = $form.serialize().replace('%5B%5D', '[]');
			antes();
			$.post($form.attr('action'), args + '&ajax=1', function(data){respuesta(data);}, 'json');
			return false;
		});
	}

	$.fn.bloquear = function(){
		var $capa = $(document.createElement('div'));
		$capa.addClass('capa cargando').css({'position':'absolute','left':'0','top':'0','right':'0','bottom':'0','background':'#fff url(img/loader.png) no-repeat center','opacity':'0.75'});
		$(this).css('position','relative').append($capa);
	}

	$.fn.desbloquear = function(){
		$(this).children('div.capa').remove();
	}
	
	//
	var disablings = new Array();
	
	$.fn.disable = function(){
		var form = document.getElementById($(this).attr('id'));
		for(i = 0; i < form.elements.length; i++){
			disablings[i] = form.elements[i].disabled;
			form.elements[i].disabled = true;
		}
	} // end disable
	
	$.fn.enable = function(){
		var form = document.getElementById($(this).attr('id'));
		for(i = 0; i < form.elements.length; i++){
			form.elements[i].disabled = disablings[i];
		}
	} // end enable
	
})(jQuery);

// upload media
(function($){
	
	//var $input_file = null;
	
	$.fn.startUpload = function(){
		var $este = $(this);
		var $form = $este.parents('form');
		var $loader = $(document.createElement('div'));
		$loader.addClass('loader').css({'position':'absolute','left':'0','top':'0','right':'0','bottom':'0','background':'#fff url(img/loader.gif) no-repeat center','opacity':'0.75'})
		
		$este.hide();
		$('#' + $este.attr('id') + '-container').css('position','relative').append($loader);

		var old_action = $form.attr('action');
		var old_target = $form.attr('target') || '';

		$form.attr('action', $este.attr('rel'));
		$form.attr('target', 'frame_upload');

		$form.submit();

		//console.log(old_target);

		$form.attr('action', old_action);
		$form.attr('target', old_target);	
	}
	
	$.fn.stopUpload = function(out){
		var $este = $(this);
		var id_input_file = $este.attr('id');
		eval('json='+out);
		if (json.exito == 1){
			$('#' + id_input_file + '-container').html(json.html);
			$('#'+id_input_file+'-borrar').show();
			$('#'+id_input_file+'-tmp').val(json.path);
		}
		else{
			alert(json.mensaje);
			$(this).show();
		}
		
		$('#'+id_input_file+'-container').children('div.loader').remove();
		$este.val('');
		return true;   
	}
})(jQuery);

function extStopUpload(id, json)
{
	$('#' + id).stopUpload(json);
}