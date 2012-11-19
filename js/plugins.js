// tabs
(function($){
	$.fn.tab = function(callback){
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
	
	var methods = {
		enviar:function(options){
			options = options || {};
			//return this.each(function(){
		var target = $(this).attr('target');
		if(target != '' && target != '_self' && typeof target != 'undefined'){
			return true;
		}
				//console.log('no paso target adentro:' + target);
				var args = $(this).serialize().replace('%5B%5D', '[]');
				if(options['antes']) options['antes'].call(this);
				$.post($(this).attr('action'), args + '&ajax=1', function(data){if(options['despues'])options['despues'].call(this,data);}, 'json');
				return false;
			//});
		},
//		bloquear : function(){
//			return this.each(function(){
//				var $capa = $(document.createElement('div'));
//				$capa.addClass('capa cargando').css({
//					'position':'absolute',
//					'left':'0',
//					'top':'0',
//					'right':'0',
//					'bottom':'0',
//					'background':'#fff url(img/loader.png) no-repeat center',
//					'opacity':'0.75'
//				});
//				this.css('position','relative').append($capa);
//			});
//		},
//		desbloquear : function(){
//			return this.each(function(){
//				this.children('div.capa').remove();
//			});
//		},
		disable : function(){
			return this.each(function(){
				var form = $(this)[0];
				for(i = 0; i < form.elements.length; i++){
					disablings[i] = form.elements[i].disabled;
					form.elements[i].disabled = true;
	}
			});
		},
		enable : function(){
			return this.each(function(){
				var form = $(this)[0];
				for(i = 0; i < form.elements.length; i++){
					form.elements[i].disabled = disablings[i];
	}
		});
	}

	}

	$.fn.jsForm = function( method ) {
		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.formulario' );
	}
		}
	
})(jQuery);

// upload media
(function($){
	
	//var $input_file = null;
	
	$.fn.startUpload = function(){
		var $este = $(this);
		var $form = $este.parents('form');
		
		$('#' + $este.attr('id') + '-container').ajaxui('bloquear');		

		var old_action = $form.attr('action');
		var old_target = $form.attr('target') || '';

		$form.attr('action', $este.attr('rel'));
		$form.attr('target', 'frame_upload');

		$form.submit();

		$form.attr('action', old_action);
		$form.attr('target', old_target);	
	}
	
	$.fn.stopUpload = function(out){
		console.log('se inicio stop upload...');
		var $este = $(this);
		var id_input_file = $este.attr('id');
		eval('json='+out);
		if (json.exito == 1){
			$('#' + id_input_file + '-container').html(json.html);
			$('#'+id_input_file+'-borrar').show();
			$('#'+id_input_file+'_tmp').val(json.path);
			$este.parent('div').prev('.input-file-falso').hide();
		}
		else{
			alert(json.mensaje);
			//$este.parent('div').prev('.input-file-falso').show();
		}
		
		//$('#'+id_input_file+'-container').children('div.loader').remove();
		$('#' + $este.attr('id') + '-container').ajaxui('desbloquear');
		$este.val('');
		return true;   
	}
})(jQuery);

function extStopUpload(id, json)
{
	$('#' + id).stopUpload(json);
}

// AJAX UI
(function($){
	var $loader = null;
	
	var methods = {
		crear:function(){
			$loader = $(document.createElement('div'));
			//return this.each(function(){
				$loader.attr('id', 'divLI').text('Ejecutando...').css({
					'position':'fixed',
					'display':'none',
					'z-index':'9999',
					'padding':'5px 15px',
					'background':'#D01F3C',
					'color':'#FFF',
					'font':'bold 12px Verdana, Arial, Helvetica, sans-serif',
					'left':'50%',
					'left': ($(window).width() - 138)/2 + 'px', 
					'top':0
				});
				$(window).resize(function(){
					$loader.css({
						'left': ($(window).width() - 138)/2 + 'px'
					});
				});
				$('body').append($loader);
			//});
		},
		mostrar:function(){
			if($loader == null) methods.crear.call(this);
			//alert('hola');
			//console.log();
			//return this.each(function(){
				$loader.show();
			//});
		},
		ocultar:function(){
			//return this.each(function(){
				$loader.hide();
			//});
		},
		bloquear:function(){
			return this.each(function(){
				var $capa = $(document.createElement('div'));
				$capa.addClass('cargando').css({
					'position':'absolute',
					'left':'0',
					'top':'0',
					'right':'0',
					'bottom':'0',
					'background':'#fff url(img/loader.gif) no-repeat center',
					'opacity':'0.75'
				});
				$(this).css('position','relative').append($capa);
			});
		},
		desbloquear:function(){
			return this.each(function(){
				$(this).children('div.cargando').remove();
			});
		}
	}
	
	$.fn.ajaxui = $.ajaxui = function(method){
		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.ajaxui' );
		}
	}
})(jQuery);


	// input file false
function inputFileImage()
{
	var wrapper = $('<div/>').css({
		height:0,
		width:0,
		'overflow':'hidden'
	});
	var fileInput = $('input.input-file-oculto').wrap(wrapper);

	$('.input-file-falso').click(function(){
		fileInput.click();
	});
	
	// upload file
	fileInput.change(function(){
		//console.log('ID before:' + $(this));
		$(this).startUpload();
	});
	
	$('a.borrar-media').click(function(e){
		e.preventDefault();
		var $this = $(this);
		if(!confirm('¿Desea eliminar el archivo?')){
			return;
		}
		else{
			$this.siblings('.media').html('');
			$this.hide();
			$this.siblings('.input-file-falso').show();
			var id = $this.siblings('#id_input_file').val();
			console.log('el id: ' + id);
			$('#' + id + '_tmp').val('');
			$('#' + id + '_actual').val('');
		}
	});
}