/*----------------------------------------------------------------------*/
/* wl_Gallery v 1.3 by revaxarts.com
/* description: makes a sortable gallery
/* dependency: jQuery UI sortable
/*----------------------------------------------------------------------*/


$.fn.wl_Gallery = function (method) {

	var args = arguments;
	return this.each(function () {

		var $this = $(this);


		if ($.fn.wl_Gallery.methods[method]) {
			return $.fn.wl_Gallery.methods[method].apply(this, Array.prototype.slice.call(args, 1));
		} else if (typeof method === 'object' || !method) {
			if ($this.data('wl_Gallery')) {
				var opts = $.extend({}, $this.data('wl_Gallery'), method);
			} else {
				var opts = $.extend({}, $.fn.wl_Gallery.defaults, method, $this.data());
			}
		} else {
			$.error('Method "' + method + '" does not exist');
		}

		var $items = $this.find('a');
		if (!$this.data('wl_Gallery')) {

			$this.data('wl_Gallery', {});

			//make it sortable
			$this.sortable({
				containment: $this,
				opacity: 0.8,
				distance: 5,
				handle: 'a',
				forceHelperSize: true,
				placeholder: 'sortable_placeholder',
				forcePlaceholderSize: true,
				start: function (event, ui) {
					$this.dragging = true;
					ui.item.trigger('mouseleave');
				},
				stop: function (event, ui) {
					$this.dragging = false;
				},
				update: function (event, ui) {
					var _a = ui.item.find('a').eq(0);
					//callback action
					opts.onMove.call($this[0], ui.item, _a.attr('href'), _a.attr('title'), $this.find('li'));
				}
			});

			opts.images = [];
			
			$items.each(function () {
				var _this = $(this),
					_image = _this.find('img,.menuedicion,.fileupload-nofile'),
					_append = $('<span>');
				
				_image.each(function() {	
					//add edit and delete buttons
					if (! _this.hasClass('no-edit')) {
						if(opts.editBtn) _append.append('<a class="edit">Editar</a>');
					}
					if (! _this.hasClass('no-delete')) {
						if(opts.deleteBtn) _append.append('<a class="delete">Borrar</a>');
					}
					if(opts.deleteBtn || opts.editBtn) _this.append(_append);
					
					//store images within the DOM
					opts.images.push({
						image: _image.attr('rel') || _image.attr('src'),
						thumb: _image.attr('src'),
						title: _image.attr('title'),
						description: _image.attr('alt')
					});
				});
			});
			
			
			
			if(opts.editBtn){
				//bind the edit event to the button
				$this.find('a.edit').bind('click.wl_Gallery touchstart.wl_Gallery', function (event) {
					event.stopPropagation();
					event.preventDefault();
					var opts = $this.data('wl_Gallery') || opts,
						_this = $(this),
						_element = _this.parent().parent().parent(),
						_href = _element.find('a')[0].href,
						_title = _element.find('a')[0].title;
						_id = _element.find('a')[0].id;
					//callback action
					opts.onEdit.call($this[0], _element, _href, _title, _id);
					return false;
	
				});
			}
			
			if(opts.deleteBtn){
				//bind the delete event to the button
				$this.find('a.delete').bind('click.wl_Gallery touchstart.wl_Gallery', function (event) {
					event.stopPropagation();
					event.preventDefault();
					var opts = $this.data('wl_Gallery') || opts,
						_this = $(this),
						_element = _this.parent().parent().parent(),
						_href = _element.find('a')[0].href,
						_title = _element.find('a')[0].title;
						_id = _element.find('a')[0].id;
					
					//callback action
					opts.onDelete.call($this[0], _element, _href, _title, _id);
					return false;
				});
			}

		} else {

		}
		
		if (opts) $.extend($this.data('wl_Gallery'), opts);
	});

};

$.fn.wl_Gallery.defaults = {
	group: 'wl_gallery',
	editBtn: true,
	deleteBtn: true,
	fancybox: {},
	onEdit: function (element, href, title, id) {},
	onDelete: function (element, href, title, id) {},
	onMove: function (element, href, title, newOrder) {}
};
$.fn.wl_Gallery.version = '1.3';


$.fn.wl_Gallery.methods = {
	set: function () {
		var $this = $(this),
			options = {};
		if (typeof arguments[0] === 'object') {
			options = arguments[0];
		} else if (arguments[0] && arguments[1] !== undefined) {
			options[arguments[0]] = arguments[1];
		}
		$.each(options, function (key, value) {
			if ($.fn.wl_Gallery.defaults[key] !== undefined || $.fn.wl_Gallery.defaults[key] == null) {
				$this.data('wl_Gallery')[key] = value;
			} else {
				$.error('Key "' + key + '" is not defined');
			}
		});

	}
};

$(document).ready(function() {
	function DeleteImageGallery(obj,el,id) {
		var myUrl='';
		var prefix='';
		if (obj.attr('Prefix').length) { prefix=obj.attr('Prefix'); }
		myUrl = obj.attr('module')+'/'+prefix+obj.attr('extra')+'_delete/id/'+id;
		if (myUrl!="") {
			$.ajax({
				type: 'GET',
				url: myUrl,
				data: 'idelement='+id,
				success:function(msj){	
					if ( msj == 1 ){
						el.fadeOut();					
					}
					else{
						bootbox.alert('No se pudo borrar el elemento.');
					}
				},
				error:function(){
					bootbox.alert('Se produjo un error interno al borrar el elemento');
				}
			});
		}
	}

	function EditImageGallery(obj,id) {
		var myUrl='';
		var prefix='';
		if (obj.attr('Prefix').length) { prefix=obj.attr('Prefix'); }
		myUrl=obj.attr('module')+'/'+prefix+obj.attr('extra')+'_edit/prior/'+obj.attr('prior')+'/id/'+id;
		window.location = myUrl;
	}

	function SaveOrder(myUrl,orderdata,notify) {
		$.ajax({
			type: 'GET',
			url: myUrl,
			data: 'elementsorder='+ orderdata,
			success:function(msj){ 
				return true; 
			},
			error:function(){ 
				if (notify) { bootbox.alert('Se produjo un error interno al guardar el nuevo orden'); }
				return false; 
			}
		});
		return false;
	}

	$(document).find('ul.wl_gallery').wl_Gallery({				
		onEdit: function (element, href, title, id) {
			var obj=$(this);
			//Comprobamos is hay que preguntar antes de ejecutar
			if (obj.is('.edit-confirm')) {
				//$.confirm('Se perderán todos los cambios realizados.Desea continuar?',function(){ EditExtra(obj,id); });						
				EditImageGallery(obj,id);
			} else {
				EditImageGallery(obj,id);
			}
		},
		onDelete: function (element, href, title, id) {
			if(id){
				var obj=$(this);
				bootbox.confirm("¿Borrar el elemento seleccionado?", function(result) {
					if (result) { DeleteImageGallery(obj,element,id); }
				});
			}		
		},
		onMove:	function(el, item, href, title, li){
			if(href){
				var obj=$(this);
				_script=obj.attr('script');
				_prior=obj.attr('prior');
				_extra=obj.attr('extra');
				_prefix='';
				if (obj.attr('Prefix').length) { _prefix=obj.attr('Prefix'); }
				var orderdata=obj.sortable('toArray');
				var action="saveorder";
				myUrl=_script+'/'+_prefix+_extra+'_'+action+'/prior/'+_prior;
				//myUrl=_script + "?action=" + action + "&id=" + _prior;
				SaveOrder(myUrl,orderdata,true);
			}
		}
	});

	
});