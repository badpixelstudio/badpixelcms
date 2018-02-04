/*Functions*/
$(document).ready(function () {
	$.parseData = function (data, returnArray) {
		if (/^\[(.*)\]$/.test(data)) { //array
			data = data.substr(1, data.length - 2).split(',');
		}
		if (returnArray && !$.isArray(data) && data != null) {
			data = Array(data);
		}
		return data;
	};
	$.leadingZero = function (value) {
		value = parseInt(value, 10);
		if(!isNaN(value)) {
			(value < 10) ? value = '0' + value : value;
		}
		return value;
	};
});
/*
 * jQuery File Upload Plugin 5.0.2
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://creativecommons.org/licenses/MIT/
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global document, XMLHttpRequestUpload, Blob, File, FormData, location, jQuery */

(function ($) {
    'use strict';

    // The fileupload widget listens for change events on file input fields
    // defined via fileInput setting and drop events of the given dropZone.
    // In addition to the default jQuery Widget methods, the fileupload widget
    // exposes the "add" and "send" methods, to add or directly send files
    // using the fileupload API.
    // By default, files added via file input selection, drag & drop or
    // "add" method are uploaded immediately, but it is possible to override
    // the "add" callback option to queue file uploads.
    $.widget('blueimp.fileupload', {
        
        options: {
            // The namespace used for event handler binding on the dropZone and
            // fileInput collections.
            // If not set, the name of the widget ("fileupload") is used.
            namespace: undefined,
            // The drop target collection, by the default the complete document.
            // Set to null or an empty collection to disable drag & drop support:
            dropZone: $(document),
            // The file input field collection, that is listened for change events.
            // If undefined, it is set to the file input fields inside
            // of the widget element on plugin initialization.
            // Set to null or an empty collection to disable the change listener.
            fileInput: undefined,
            // By default, the file input field is replaced with a clone after
            // each input field change event. This is required for iframe transport
            // queues and allows change events to be fired for the same file
            // selection, but can be disabled by setting the following option to false:
            replaceFileInput: true,
            // The parameter name for the file form data (the request argument name).
            // If undefined or empty, the name property of the file input field is
            // used, or "files[]" if the file input name property is also empty:
            paramName: undefined,
            // By default, each file of a selection is uploaded using an individual
            // request for XHR type uploads. Set to false to upload file
            // selections in one request each:
            singleFileUploads: true,
            // Set the following option to true to issue all file upload requests
            // in a sequential order:
            sequentialUploads: false,
            // Set the following option to true to force iframe transport uploads:
            forceIframeTransport: false,
            // By default, XHR file uploads are sent as multipart/form-data.
            // The iframe transport is always using multipart/form-data.
            // Set to false to enable non-multipart XHR uploads:
            multipart: true,
            // To upload large files in smaller chunks, set the following option
            // to a preferred maximum chunk size. If set to 0, null or undefined,
            // or the browser does not support the required Blob API, files will
            // be uploaded as a whole.
            maxChunkSize: undefined,
            // When a non-multipart upload or a chunked multipart upload has been
            // aborted, this option can be used to resume the upload by setting
            // it to the size of the already uploaded bytes. This option is most
            // useful when modifying the options object inside of the "add" or
            // "send" callbacks, as the options are cloned for each file upload.
            uploadedBytes: undefined,
            // By default, failed (abort or error) file uploads are removed from the
            // global progress calculation. Set the following option to false to
            // prevent recalculating the global progress data:
            recalculateProgress: true,
            
            // Additional form data to be sent along with the file uploads can be set
            // using this option, which accepts an array of objects with name and
            // value properties, a function returning such an array, a FormData
            // object (for XHR file uploads), or a simple object.
            // The form of the first fileInput is given as parameter to the function:
            formData: function (form) {
                return form.serializeArray();
            },
            
            // The add callback is invoked as soon as files are added to the fileupload
            // widget (via file input selection, drag & drop or add API call).
            // If the singleFileUploads option is enabled, this callback will be
            // called once for each file in the selection for XHR file uplaods, else
            // once for each file selection.
            // The upload starts when the submit method is invoked on the data parameter.
            // The data object contains a files property holding the added files
            // and allows to override plugin options as well as define ajax settings.
            // Listeners for this callback can also be bound the following way:
            // .bind('fileuploadadd', func);
            // data.submit() returns a Promise object and allows to attach additional
            // handlers using jQuery's Deferred callbacks:
            // data.submit().done(func).fail(func).always(func);
            add: function (e, data) {
                data.submit();
            },
            
            // Other callbacks:
            // Callback for the start of each file upload request:
            // send: function (e, data) {}, // .bind('fileuploadsend', func);
            // Callback for successful uploads:
            // done: function (e, data) {}, // .bind('fileuploaddone', func);
            // Callback for failed (abort or error) uploads:
            // fail: function (e, data) {}, // .bind('fileuploadfail', func);
            // Callback for completed (success, abort or error) requests:
            // always: function (e, data) {}, // .bind('fileuploadalways', func);
            // Callback for upload progress events:
            // progress: function (e, data) {}, // .bind('fileuploadprogress', func);
            // Callback for global upload progress events:
            // progressall: function (e, data) {}, // .bind('fileuploadprogressall', func);
            // Callback for uploads start, equivalent to the global ajaxStart event:
            // start: function (e) {}, // .bind('fileuploadstart', func);
            // Callback for uploads stop, equivalent to the global ajaxStop event:
            // stop: function (e) {}, // .bind('fileuploadstop', func);
            // Callback for change events of the fileInput collection:
            // change: function (e, data) {}, // .bind('fileuploadchange', func);
            // Callback for drop events of the dropZone collection:
            // drop: function (e, data) {}, // .bind('fileuploaddrop', func);
            // Callback for dragover events of the dropZone collection:
            // dragover: function (e) {}, // .bind('fileuploaddragover', func);
            
            // The plugin options are used as settings object for the ajax calls.
            // The following are jQuery ajax settings required for the file uploads:
            processData: false,
            contentType: false,
            cache: false
        },
        
        // A list of options that require a refresh after assigning a new value:
        _refreshOptionsList: ['namespace', 'dropZone', 'fileInput'],

        _isXHRUpload: function (options) {
            var undef = 'undefined';
            return !options.forceIframeTransport &&
                typeof XMLHttpRequestUpload !== undef && typeof File !== undef &&
                (!options.multipart || typeof FormData !== undef);
        },

        _getFormData: function (options) {
            var formData;
            if (typeof options.formData === 'function') {
                return options.formData(options.form);
            } else if ($.isArray(options.formData)) {
                return options.formData;
            } else if (options.formData) {
                formData = [];
                $.each(options.formData, function (name, value) {
                    formData.push({name: name, value: value});
                });
                return formData;
            }
            return [];
        },

        _getTotal: function (files) {
            var total = 0;
            $.each(files, function (index, file) {
                total += file.size || 1;
            });
            return total;
        },

        _onProgress: function (e, data) {
            if (e.lengthComputable) {
                var total = data.total || this._getTotal(data.files),
                    loaded = parseInt(
                        e.loaded / e.total * (data.chunkSize || total),
                        10
                    ) + (data.uploadedBytes || 0);
                this._loaded += loaded - (data.loaded || data.uploadedBytes || 0);
                data.lengthComputable = true;
                data.loaded = loaded;
                data.total = total;
                // Trigger a custom progress event with a total data property set
                // to the file size(s) of the current upload and a loaded data
                // property calculated accordingly:
                this._trigger('progress', e, data);
                // Trigger a global progress event for all current file uploads,
                // including ajax calls queued for sequential file uploads:
                this._trigger('progressall', e, {
                    lengthComputable: true,
                    loaded: this._loaded,
                    total: this._total
                });
            }
        },

        _initProgressListener: function (options) {
            var that = this,
                xhr = options.xhr ? options.xhr() : $.ajaxSettings.xhr();
            // Accesss to the native XHR object is required to add event listeners
            // for the upload progress event:
            if (xhr.upload && xhr.upload.addEventListener) {
                xhr.upload.addEventListener('progress', function (e) {
                    that._onProgress(e, options);
                }, false);
                options.xhr = function () {
                    return xhr;
                };
            }
        },

        _initXHRData: function (options) {
            var formData,
                file = options.files[0];
            if (!options.multipart || options.blob) {
                // For non-multipart uploads and chunked uploads,
                // file meta data is not part of the request body,
                // so we transmit this data as part of the HTTP headers.
                // For cross domain requests, these headers must be allowed
                // via Access-Control-Allow-Headers or removed using
                // the beforeSend callback:
                options.headers = $.extend(options.headers, {
                    'X-File-Name': file.name,
                    'X-File-Type': file.type,
                    'X-File-Size': file.size
                });
                if (!options.blob) {
                    // Non-chunked non-multipart upload:
                    options.contentType = file.type;
                    options.data = file;
                } else if (!options.multipart) {
                    // Chunked non-multipart upload:
                    options.contentType = 'application/octet-stream';
                    options.data = options.blob;
                }
            }
            if (options.multipart && typeof FormData !== 'undefined') {
                if (options.formData instanceof FormData) {
                    formData = options.formData;
                } else {
                    formData = new FormData();
                    $.each(this._getFormData(options), function (index, field) {
                        formData.append(field.name, field.value);
                    });
                }
                if (options.blob) {
                    formData.append(options.paramName, options.blob);
                } else {
                    $.each(options.files, function (index, file) {
                        // File objects are also Blob instances.
                        // This check allows the tests to run with
                        // dummy objects:
                        if (file instanceof Blob) {
                            formData.append(options.paramName, file);
                        }
                    });
                }
                options.data = formData;
            }
            // Blob reference is not needed anymore, free memory:
            options.blob = null;
        },
        
        _initIframeSettings: function (options) {
            // Setting the dataType to iframe enables the iframe transport:
            options.dataType = 'iframe ' + (options.dataType || '');
            // The iframe transport accepts a serialized array as form data:
            options.formData = this._getFormData(options);
        },
        
        _initDataSettings: function (options) {
            if (this._isXHRUpload(options)) {
                if (!this._chunkedUpload(options, true)) {
                    if (!options.data) {
                        this._initXHRData(options);
                    }
                    this._initProgressListener(options);
                }
            } else {
                this._initIframeSettings(options);
            }
        },
        
        _initFormSettings: function (options) {
            // Retrieve missing options from the input field and the
            // associated form, if available:
            if (!options.form || !options.form.length) {
                options.form = $(options.fileInput.prop('form'));
            }
            if (!options.paramName) {
                options.paramName = options.fileInput.prop('name') ||
                    'files[]';
            }
            if (!options.url) {
                options.url = options.form.prop('action') || location.href;
            }
            // The HTTP request method must be "POST" or "PUT":
            options.type = (options.type || options.form.prop('method') || '')
                .toUpperCase();
            if (options.type !== 'POST' && options.type !== 'PUT') {
                options.type = 'POST';
            }
        },
        
        _getAJAXSettings: function (data) {
            var options = $.extend({}, this.options, data);
            this._initFormSettings(options);
            this._initDataSettings(options);
            return options;
        },

        // Maps jqXHR callbacks to the equivalent
        // methods of the given Promise object:
        _enhancePromise: function (promise) {
            promise.success = promise.done;
            promise.error = promise.fail;
            promise.complete = promise.always;
            return promise;
        },

        // Creates and returns a Promise object enhanced with
        // the jqXHR methods abort, success, error and complete:
        _getXHRPromise: function (resolveOrReject, context, args) {
            var dfd = $.Deferred(),
                promise = dfd.promise();
            context = context || this.options.context || promise;
            if (resolveOrReject === true) {
                dfd.resolveWith(context, args);
            } else if (resolveOrReject === false) {
                dfd.rejectWith(context, args);
            }
            promise.abort = dfd.promise;
            return this._enhancePromise(promise);
        },

        // Uploads a file in multiple, sequential requests
        // by splitting the file up in multiple blob chunks.
        // If the second parameter is true, only tests if the file
        // should be uploaded in chunks, but does not invoke any
        // upload requests:
        _chunkedUpload: function (options, testOnly) {
            var that = this,
                file = options.files[0],
                fs = file.size,
                ub = options.uploadedBytes = options.uploadedBytes || 0,
                mcs = options.maxChunkSize || fs,
                // Use the Blob methods with the slice implementation
                // according to the W3C Blob API specification:
                slice = file.webkitSlice || file.mozSlice || file.slice,
                upload,
                n,
                jqXHR,
                pipe;
            if (!(this._isXHRUpload(options) && slice && (ub || mcs < fs)) ||
                    options.data) {
                return false;
            }
            if (testOnly) {
                return true;
            }
            if (ub >= fs) {
                file.error = 'uploadedBytes';
                return this._getXHRPromise(false);
            }
            // n is the number of blobs to upload,
            // calculated via filesize, uploaded bytes and max chunk size:
            n = Math.ceil((fs - ub) / mcs);
            // The chunk upload method accepting the chunk number as parameter:
            upload = function (i) {
                if (!i) {
                    return that._getXHRPromise(true);
                }
                // Upload the blobs in sequential order:
                return upload(i -= 1).pipe(function () {
                    // Clone the options object for each chunk upload:
                    var o = $.extend({}, options);
                    o.blob = slice.call(
                        file,
                        ub + i * mcs,
                        ub + (i + 1) * mcs
                    );
                    // Store the current chunk size, as the blob itself
                    // will be dereferenced after data processing:
                    o.chunkSize = o.blob.size;
                    // Process the upload data (the blob and potential form data):
                    that._initXHRData(o);
                    // Add progress listeners for this chunk upload:
                    that._initProgressListener(o);
                    jqXHR = ($.ajax(o) || that._getXHRPromise(false, o.context))
                        .done(function () {
                            // Create a progress event if upload is done and
                            // no progress event has been invoked for this chunk:
                            if (!o.loaded) {
                                that._onProgress($.Event('progress', {
                                    lengthComputable: true,
                                    loaded: o.chunkSize,
                                    total: o.chunkSize
                                }), o);
                            }
                            options.uploadedBytes = o.uploadedBytes
                                += o.chunkSize;
                        });
                    return jqXHR;
                });
            };
            // Return the piped Promise object, enhanced with an abort method,
            // which is delegated to the jqXHR object of the current upload,
            // and jqXHR callbacks mapped to the equivalent Promise methods:
            pipe = upload(n);
            pipe.abort = function () {
                return jqXHR.abort();
            };
            return this._enhancePromise(pipe);
        },

        _beforeSend: function (e, data) {
            if (this._active === 0) {
                // the start callback is triggered when an upload starts
                // and no other uploads are currently running,
                // equivalent to the global ajaxStart event:
                this._trigger('start');
            }
            this._active += 1;
            // Initialize the global progress values:
            this._loaded += data.uploadedBytes || 0;
            this._total += this._getTotal(data.files);
        },

        _onDone: function (result, textStatus, jqXHR, options) {
            if (!this._isXHRUpload(options)) {
                // Create a progress event for each iframe load:
                this._onProgress($.Event('progress', {
                    lengthComputable: true,
                    loaded: 1,
                    total: 1
                }), options);
            }
            options.result = result;
            options.textStatus = textStatus;
            options.jqXHR = jqXHR;
            this._trigger('done', null, options);
        },

        _onFail: function (jqXHR, textStatus, errorThrown, options) {
            options.jqXHR = jqXHR;
            options.textStatus = textStatus;
            options.errorThrown = errorThrown;
            this._trigger('fail', null, options);
            if (options.recalculateProgress) {
                // Remove the failed (error or abort) file upload from
                // the global progress calculation:
                this._loaded -= options.loaded || options.uploadedBytes || 0;
                this._total -= options.total || this._getTotal(options.files);
            }
        },

        _onAlways: function (result, textStatus, jqXHR, errorThrown, options) {
            this._active -= 1;
            options.result = result;
            options.textStatus = textStatus;
            options.jqXHR = jqXHR;
            options.errorThrown = errorThrown;
            this._trigger('always', null, options);
            if (this._active === 0) {
                // The stop callback is triggered when all uploads have
                // been completed, equivalent to the global ajaxStop event:
                this._trigger('stop');
                // Reset the global progress values:
                this._loaded = this._total = 0;
            }
        },

        _onSend: function (e, data) {
            var that = this,
                jqXHR,
                pipe,
                options = that._getAJAXSettings(data),
                send = function (resolve, args) {
                    jqXHR = jqXHR || (
                        (resolve !== false &&
                        that._trigger('send', e, options) !== false &&
                        (that._chunkedUpload(options) || $.ajax(options))) ||
                        that._getXHRPromise(false, options.context, args)
                    ).done(function (result, textStatus, jqXHR) {
                        that._onDone(result, textStatus, jqXHR, options);
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        that._onFail(jqXHR, textStatus, errorThrown, options);
                    }).always(function (a1, a2, a3) {
                        if (!a3 || typeof a3 === 'string') {
                            that._onAlways(undefined, a2, a1, a3, options);
                        } else {
                            that._onAlways(a1, a2, a3, undefined, options);
                        }
                    });
                    return jqXHR;
                };
            this._beforeSend(e, options);
            if (this.options.sequentialUploads) {
                // Return the piped Promise object, enhanced with an abort method,
                // which is delegated to the jqXHR object of the current upload,
                // and jqXHR callbacks mapped to the equivalent Promise methods:
                pipe = (this._sequence = this._sequence.pipe(send, send));
                pipe.abort = function () {
                    if (!jqXHR) {
                        return send(false, [undefined, 'abort', 'abort']);
                    }
                    return jqXHR.abort();
                };
                return this._enhancePromise(pipe);
            }
            return send();
        },
        
        _onAdd: function (e, data) {
            var that = this,
                result = true,
                options = $.extend({}, this.options, data);
            if (options.singleFileUploads && this._isXHRUpload(options)) {
                $.each(data.files, function (index, file) {
                    var newData = $.extend({}, data, {files: [file]});
                    newData.submit = function () {
                        return that._onSend(e, newData);
                    };
                    return (result = that._trigger('add', e, newData));
                });
                return result;
            } else if (data.files.length) {
                data = $.extend({}, data);
                data.submit = function () {
                    return that._onSend(e, data);
                };
                return this._trigger('add', e, data);
            }
        },
        
        // File Normalization for Gecko 1.9.1 (Firefox 3.5) support:
        _normalizeFile: function (index, file) {
            if (file.name === undefined && file.size === undefined) {
                file.name = file.fileName;
                file.size = file.fileSize;
            }
        },

        _replaceFileInput: function (input) {
            var inputClone = input.clone(true);
            $('<form></form>').append(inputClone)[0].reset();
            // Detaching allows to insert the fileInput on another form
            // without loosing the file input value:
            input.after(inputClone).detach();
            // Replace the original file input element in the fileInput
            // collection with the clone, which has been copied including
            // event handlers:
            this.options.fileInput = this.options.fileInput.map(function (i, el) {
                if (el === input[0]) {
                    return inputClone[0];
                }
                return el;
            });
        },
        
        _onChange: function (e) {
           var that = e.data.fileupload,
                data = {
                    files: $.each($.makeArray(e.target.files), that._normalizeFile),
                    fileInput: $(e.target),
                    form: $(e.target.form)
                };
            if (!data.files.length) {
                // If the files property is not available, the browser does not
                // support the File API and we add a pseudo File object with
                // the input value as name with path information removed:
                data.files = [{name: e.target.value.replace(/^.*\\/, '')}];
            }
            // Store the form reference as jQuery data for other event handlers,
            // as the form property is not available after replacing the file input: 
            if (data.form.length) {
                data.fileInput.data('blueimp.fileupload.form', data.form);
            } else {
                data.form = data.fileInput.data('blueimp.fileupload.form');
            }
            if (that.options.replaceFileInput) {
                that._replaceFileInput(data.fileInput);
            }
            if (that._trigger('change', e, data) === false ||
                    that._onAdd(e, data) === false) {
                return false;
            }
        },
        
        _onDrop: function (e) {
            var that = e.data.fileupload,
                dataTransfer = e.dataTransfer = e.originalEvent.dataTransfer,
                data = {
                    files: $.each(
                        $.makeArray(dataTransfer && dataTransfer.files),
                        that._normalizeFile
                    )
                };
            if (that._trigger('drop', e, data) === false ||
                    that._onAdd(e, data) === false) {
                return false;
            }
            e.preventDefault();
        },
        
        _onDragOver: function (e) {
            var that = e.data.fileupload,
                dataTransfer = e.dataTransfer = e.originalEvent.dataTransfer;
            if (that._trigger('dragover', e) === false) {
                return false;
            }
            if (dataTransfer) {
                dataTransfer.dropEffect = dataTransfer.effectAllowed = 'copy';
            }
            e.preventDefault();
        },
        
        _initEventHandlers: function () {
            var ns = this.options.namespace || this.name;
            this.options.dropZone
                .bind('dragover.' + ns, {fileupload: this}, this._onDragOver)
                .bind('drop.' + ns, {fileupload: this}, this._onDrop);
            this.options.fileInput
                .bind('change.' + ns, {fileupload: this}, this._onChange);
        },

        _destroyEventHandlers: function () {
         var ns = this.options.namespace || this.name;
            this.options.dropZone
                .unbind('dragover.' + ns, this._onDragOver)
                .unbind('drop.' + ns, this._onDrop);
            this.options.fileInput
                .unbind('change.' + ns, this._onChange);
        },
        
        _beforeSetOption: function (key, value) {
            //this._destroyEventHandlers();
        },
        
        _afterSetOption: function (key, value) {
            var options = this.options;
            if (!options.fileInput) {
                options.fileInput = $();
            }
            if (!options.dropZone) {
                options.dropZone = $();
            }
            this._initEventHandlers();
        },
        
        _setOption: function (key, value) {
            var refresh = $.inArray(key, this._refreshOptionsList) !== -1;
            if (refresh) {
                this._beforeSetOption(key, value);
            }
            $.Widget.prototype._setOption.call(this, key, value);
            if (refresh) {
                this._afterSetOption(key, value);
            }
        },

        _create: function () {
            var options = this.options;
            if (options.fileInput === undefined) {
                options.fileInput = this.element.is('input:file') ?
                    this.element : this.element.find('input:file');
            } else if (!options.fileInput) {
                options.fileInput = $();
            }
            if (!options.dropZone) {
                options.dropZone = $();
            }
            this._sequence = this._getXHRPromise(true);
            this._active = this._loaded = this._total = 0;
            this._initEventHandlers();
        },
        
        destroy: function () {
           // this._destroyEventHandlers();
            //$.Widget.prototype.destroy.call(this);
        },

        enable: function () {
            $.Widget.prototype.enable.call(this);
            this._initEventHandlers();
        },
        
        disable: function () {
           this._destroyEventHandlers();
            $.Widget.prototype.disable.call(this);
        },

        // This method is exposed to the widget API and allows adding files
        // using the fileupload API. The data parameter accepts an object which
        // must have a files property and can contain additional options:
        // .fileupload('add', {files: filesList});
        add: function (data) {
            if (!data || this.options.disabled) {
                return;
            }
            data.files = $.each($.makeArray(data.files), this._normalizeFile);
            this._onAdd(null, data);
        },
        
        // This method is exposed to the widget API and allows sending files
        // using the fileupload API. The data parameter accepts an object which
        // must have a files property and can contain additional options:
        // .fileupload('send', {files: filesList});
        // The method returns a Promise object for the file upload call.
        send: function (data) {
            if (data && !this.options.disabled) {
                data.files = $.each($.makeArray(data.files), this._normalizeFile);
                if (data.files.length) {
                    return this._onSend(null, data);
                }
            }
            return this._getXHRPromise(false, data && data.context);
        }
        
    });
    
}(jQuery));


/*wl_file*/
$.fn.wl_File = function (method) {
	var args = arguments;
	return this.each(function () {
		var $this = $(this);

		if ($.fn.wl_File.methods[method]) {
			return $.fn.wl_File.methods[method].apply(this, Array.prototype.slice.call(args, 1));
		} else if (typeof method === 'object' || !method) {
			if ($this.data('wl_File')) {
				var opts = $.extend({}, $this.data('wl_File'), method);
			} else {
				var opts = $.extend({}, $.fn.wl_File.defaults, method, $this.data());
			}
		} else {
			try {
				return $this.fileupload(method, args[1], args[2], args[3], args[4]);
			} catch (e) {
				$.error('Method "' + method + '" does not exist');
			}
		}

		if (!$this.data('wl_File')) {

			$this.data('wl_File', {});
			
			//The queue, the upload files and drag&drop support of the current browser
			var queue = {},
				files = [],
				queuelength = 0,
				tempdata, maxNumberOfFiles, dragdropsupport = isEventSupported('dragstart') && isEventSupported('drop') && !! window.FileReader;

			//get native multiple attribute or use defined one 
			opts.multiple = ($this.is('[multiple]') || typeof $this.prop('multiple') === 'string') || opts.multiple;

			//used for the form
			opts.queue = {};
			opts.files = [];

			if (typeof opts.allowedExtensions === 'string') opts.allowedExtensions = $.parseData(opts.allowedExtensions);

			//the container for the buttons
			opts.ui = $('<div>', {
				'class': 'fileuploadui'
			}).insertAfter($this);

			//start button only if autoUpload is false
			if (!opts.autoUpload) {
				opts.uiStart = $('<a>', {
					'class': 'btn small fileupload_start',
					'title': opts.text.start
				}).html(opts.text.start).bind('click', function () {
					$.each(queue, function (file) {
						upload(queue[file].data);
					});
				}).appendTo(opts.ui);

			}

            //select file
            opts.uiSelect = $('<a>', {
                'class': 'btn default fileupload_select',
                'title': opts.text.select_file
            }).html(opts.text.select_file).appendTo(opts.ui).bind('click', function () {
                ($this).click();
            });


			//cancel/remove all button
			opts.uiCancel = $('<a>', {
				'class': 'btn red hide fileupload_cancel',
				'title': opts.text.cancel_all
			}).html(opts.text.cancel_all).appendTo(opts.ui).bind('click', function () {
				var _this = $(this),
					el = opts.filepool.find('li');
				el.addClass('error');

                _this.addClass('hide');
				//IE and Opera delete the data on submit so we store it temporarily
				if (!$this.data('wl_File')) $this.data('wl_File', tempdata);
				
				files = $this.data('wl_File').files = [];

				queuelength = 0;

				$.each(queue, function (name) {
					if (queue[name]) {
						queue[name].fileupload.abort();
						delete queue[name];
					}
				});
				el.delay(700).fadeOut(function () {

					//trigger a change for required inputs
					opts.filepool.trigger('change');
					_this.text(opts.text.cancel_all).attr('title', opts.text.cancel_all);
					$(this).remove();
				});
				$.map(el,function(k,v){
					tmpname=$(k).data('fileName');
					var tmp=el.find('.name');
					if (tmp.length) { tmpname=tmp.text(); }
					return tmpname;
				})
				
				
				DeleteFiles($.map(el,function(k,v){return $(k).data('fileName');}));
				//trigger delete event
				opts.onDelete.call($this[0], $.map(el,function(k,v){return $(k).data('fileName');}));
			});


			//filepool and dropzone
			opts.filepool = $('<ul>', {
				'class': 'fileuploadpool'
			}).insertAfter($this)

			//cancel one files
			.delegate('a.cancel', 'click', function () {
				var el = $(this).parent(),
					name = el.data('fileName');

				//IE and Opera delete the data on submit so we store it temporarily
				if (!$this.data('wl_File')) $this.data('wl_File', tempdata);

				//remove clicked file from the list
				$this.data('wl_File').files = files = $.map(files, function (filename) {
					if (filename != name) return filename;
				});

				//abort upload
				queue[name].fileupload.abort();

				//remove from queue
				delete queue[name];
				queuelength--;

				el.addClass('error').delay(700).fadeOut();
				
				//trigger cancel event
				opts.onCancel.call($this[0], name);
				//trigger a change for required inputs
				opts.filepool.trigger('change');
			})

			//remove file from list
			.delegate('a.remove', 'click', function () {
				var el = $(this).parent(),
					name = el.data('fileName');
				var tmp=el.find('.name');
				if (tmp.length) { name=tmp.text(); }
				if (!$this.data('wl_File')) $this.data('wl_File', tempdata);

				//remove clicked file from the list
				$this.data('wl_File').files = files = $.map(files, function (filename) {
					if (filename != name) return filename;
				});
				el.fadeOut();
				//delete file from temp folder
				DeleteFiles([name]);
				//trigger cancel event
				opts.onDelete.call($this[0], [name]);
				//trigger a change for required inputs
				opts.filepool.trigger('change');
			})

			//add some classes to the filepool
			.addClass((!opts.multiple) ? 'single' : 'multiple').addClass((dragdropsupport) ? 'drop' : 'nodrop');


			//call the fileupload plugin
			$this.fileupload({
				url: opts.url,
				dropZone: (opts.dragAndDrop) ? opts.filepool : null,
				fileInput: $this,
				//required
				singleFileUploads: true,
				sequentialUploads: opts.sequentialUploads,
				//must be an array
				paramName: opts.paramName + '[]',
				formData: opts.formData,
				add: function (e, data) {

					//cancel current upload and remove item on single upload field
					if (!opts.multiple) {
						opts.uiCancel.click();
						opts.filepool.find('li').remove();
					}

					//add files to the queue
					$.each(data.files, function (i, file) {
						file.ext = file.name.substring(file.name.lastIndexOf('.') + 1).toLowerCase();

						queuelength++;
						var error = getError(file);

						if (!error) {

							//add file to queue and to filepool
							addFile(file, data);
						} else {

							//reduces queuelength
							queuelength--;
							//throw error
							opts.onFileError.call($this[0], error, file);
						}
					});

					//IE and Opera delete the data on submit so we store it temporarily
					if ($this.data('wl_File')) {
						$this.data('wl_File').queue = queue;
						tempdata = $this.data('wl_File');
					} else if (tempdata) {
						tempdata.queue = queue;
					}

					//trigger a change for required inputs
					opts.filepool.trigger('change');

					opts.onAdd.call($this[0], e, data);

					//start upload if autoUpload is true
					if (opts.autoUpload) upload(data);
				},
				send: function (e, data) {
					$.each(data.files, function (i, file) {
						queue[file.name].element.addClass(data.textStatus);
						queue[file.name].progress.width('100%');
						queue[file.name].status.text(opts.text.uploading);
					});

					//rename cancel button
					opts.uiCancel.text(opts.text.cancel_all).attr('title', opts.text.cancel_all);
					return opts.onSend.call($this[0], e, data);
				},
				done: function (e, data) {
					if($.browser.msie){
						var data_to_parse = $( 'pre', data.result ).text();
					} else {
						var data_to_parse = data.result;
					}
					var result = jQuery.parseJSON(data_to_parse);
					var nombre_subido = result[0].name;
					
					$this.data('wl_File', tempdata);
					//set states for each file and push them in the list
					$.each(data.files, function (i, file) {
						if (queue[file.name]) {
							queue[file.name].element.addClass(data.textStatus);
							queue[file.name].progress.width('100%');
							queue[file.name].status.text(opts.text.done);
							queue[file.name].name.text(nombre_subido);
							queue[file.name].data.files[0].finalname=nombre_subido;
							queue[file.name].cancel.removeAttr('class').addClass('remove').attr('title', opts.text.remove);
							var extension = nombre_subido.substr( (nombre_subido.lastIndexOf('.') +1) );
							if ($.inArray(extension,['jpg','jpeg','png','gif'])>=0) {
								var w=queue[file.name].element.css('width');
								var h=queue[file.name].element.css('height');
								var tmb_url=$this.data('wl_File').url;
								tmb_url=tmb_url.replace('uploader.php','thumbnail.php')+'?w='+w+'&h='+h+'&f='+nombre_subido;
								queue[file.name].element.css('background-image','url("'+tmb_url+'")');
								queue[file.name].progress.hide();
								queue[file.name].status.hide();
								queue[file.name].name.hide();
							}
							if ($.inArray(file.name, files) == -1) {
								files.push(nombre_subido);
								$this.data('wl_File').files = files;
							}

							//delete from the queue
							queuelength--;
							delete queue[file.name];
						}
					});


					opts.onDone.call($this[0], e, data);

					//empty queue => all files uploaded
					if ($.isEmptyObject(queue)) {

						//trigger a change for required inputs
						opts.filepool.trigger('change');
						opts.uiCancel.text(opts.text.remove_all).attr('title', opts.text.remove_all);
                        opts.uiCancel.removeClass('hide');
						opts.onFinish.call($this[0], e, data);
					}

				},
				fail: function (e, data) {
					opts.onFail.call($this[0], e, data);
				},
				always: function (e, data) {
					opts.onAlways.call($this[0], e, data);
				},
				progress: function (e, data) {
					//calculate progress for each file
					$.each(data.files, function (i, file) {
						if (queue[file.name]) {
							var percentage = Math.round(parseInt(data.loaded / data.total * 100, 10));
							queue[file.name].progress.width(percentage + '%');
							queue[file.name].status.text(opts.text.uploading + percentage + '%');
						}
					});
					opts.onProgress.call($this[0], e, data);
				},
				progressall: function (e, data) {
					opts.onProgressAll.call($this[0], e, data);
				},
				start: function (e) {
					opts.onStart.call($this[0], e);
				},
				stop: function (e) {
					opts.onStop.call($this[0], e);
				},
				change: function (e, data) {
					opts.onChange.call($this[0], e, data);
				},
				drop: function (e, data) {
					opts.onDrop.call($this[0], e, data);
				},
				dragover: function (e) {
					opts.onDragOver.call($this[0], e);
				}



			});

		} else {

		}

		//upload method

		function upload(data) {
			$.each(data.files, function (i, file) {
				if (queue[file.name]) queue[file.name].fileupload = data.submit();
			});
		}

		//add files to the queue and to the filepool

		function addFile(file, data) {
			var name = file.name;
			var html = $('<li><span class="name">' + name + '</span><span class="progress"></span><span class="status">' + opts.text.ready + '</span><a class="cancel" title="' + opts.text.cancel + '">' + opts.text.cancel + '</a></li>').data('fileName', name).appendTo(opts.filepool);
			queue[name] = {
				element: html,
				data: data,
				name: html.find('.name'),
				progress: html.find('.progress'),
				status: html.find('.status'),
				cancel: html.find('.cancel')
			};
		}

		//check for errors

		function getError(file) {
			if (opts.maxNumberOfFiles && (files.length >= opts.maxNumberOfFiles || queuelength > opts.maxNumberOfFiles)) {
				return {
					msg: 'maxNumberOfFiles',
					code: 1
				};
			}
			if (opts.allowedExtensions && $.inArray(file.ext, opts.allowedExtensions) == -1) {
				return {
					msg: 'allowedExtensions',
					code: 2
				};
			}
			if (typeof file.size === 'number' && opts.maxFileSize && file.size > opts.maxFileSize) {
				return {
					msg: 'maxFileSize',
					code: 3
				};
			}
			if (typeof file.size === 'number' && opts.minFileSize && file.size < opts.minFileSize) {
				return {
					msg: 'minFileSize',
					code: 4
				};
			}
			return null;
		}

		//took from the modernizr script (thanks paul)

		function isEventSupported(eventName) {

			var element = document.createElement('div');
			eventName = 'on' + eventName;

			// When using `setAttribute`, IE skips "unload", WebKit skips "unload" and "resize", whereas `in` "catches" those
			var isSupported = eventName in element;

			if (!isSupported) {
				// If it has no `setAttribute` (i.e. doesn't implement Node interface), try generic element
				if (!element.setAttribute) {
					element = document.createElement('div');
				}
				if (element.setAttribute && element.removeAttribute) {
					element.setAttribute(eventName, '');
					isSupported = typeof element[eventName] == 'function';

					// If property was created, "remove it" (by setting value to `undefined`)
					if (typeof element[eventName] != undefined) {
						element[eventName] = undefined;
					}
					element.removeAttribute(eventName);
				}
			}

			element = null;
			return isSupported;
		}

		if (opts) $.extend($this.data('wl_File'), opts);
		
		function DeleteFiles(files) {
			//delete file
			var del_url=$this.data('wl_File').url;
			del_url=del_url.replace('uploader.php','deletefile.php');
			$.ajax({
				type: 'POST',
				url: del_url,
				data: "files="+files,
				success:function(msj){
				},
				error:function(){
					console.log('Error in delete function');
				}
			});				
		}

	});

};

$.fn.wl_File.defaults = {
	url: '../lib/upload/uploader.php',
	autoUpload: true,
	paramName: 'files',
	multiple: false,
	allowedExtensions: false,
	maxNumberOfFiles: 0,
	maxFileSize: 0,
	minFileSize: 0,
	sequentialUploads: false,
	dragAndDrop: true,
	formData: {},
	text: {
			ready: 'listo',
			cancel: 'Cancelar',
			remove: 'Eliminar',
			uploading: 'Subiendo...',
			done: 'completada',
			start: 'comenzar',
			add_files: 'Añadir',
			cancel_all: 'Cancelar',
			remove_all: 'Eliminar todo',
            select_file: 'Seleccionar archivo'
		},
	onAdd: function (e, data) {},
	onDelete:function(files){},
	onCancel:function(file){},
	onSend: function (e, data) {},
	onDone: function (e, data) {},
	onFinish: function (e, data) {},
	onFail: function (e, data) {},
	onAlways: function (e, data) {},
	onProgress: function (e, data) {},
	onProgressAll: function (e, data) {},
	onStart: function (e) {},
	onStop: function (e) {},
	onChange: function (e, data) {},
	onDrop: function (e, data) {},
	onDragOver: function (e) {},
	onFileError: function (error, fileobj) {}
};

$.fn.wl_File.version = '1.2';


$.fn.wl_File.methods = {
	reset: function () {
		var $this = $(this),
			opts = $this.data('wl_File');
		
		//default value if uniform is used
		if($.uniform)$this.next().html($.uniform.options.fileDefaultText);
		//empty file pool
		opts.filepool.empty();
		//reset button
		opts.uiCancel.attr('title',opts.text.cancel_all).text(opts.text.cancel_all);
		//clear array
		$this.data('wl_File').files = [];
	},
	set: function () {
		var $this = $(this),
			options = {};
		if (typeof arguments[0] === 'object') {
			options = arguments[0];
		} else if (arguments[0] && arguments[1] !== undefined) {
			options[arguments[0]] = arguments[1];
		}
		$.each(options, function (key, value) {
			if ($.fn.wl_File.defaults[key] !== undefined || $.fn.wl_File.defaults[key] == null) {
				$this.data('wl_File')[key] = value;
			} else {
				$.error('Key "' + key + '" is not defined');
			}
		});

	}
};

/*CONFIG*/
$(document).ready(function() {
	if($.fn.wl_File){
	};
	
	
	$('.remove-this-item').click(function() {
		var obj=$(this).parent().parent().parent();
        $('#' + obj.attr('data-element')).attr('value','');
        $('#' + obj.attr('upl-block')).css("display","block");
        obj.hide();
        return false;
	});

    $('.edit-this-item').click(function() {
        var obj=$(this).parent().parent().parent();
        window.location = obj.attr('external');
    });
	
});

function _addHiddenField(elemento,nombre,valor) {
    $(elemento).after('<input type="hidden" rel="wlfile" name="'+nombre+'" value="'+valor+'" />');
}


function ProcessUploadedFiles() {
    var _hiddens=$('input[rel="wlfile"]');
    _hiddens.remove();
    var _inputs=$(document).find('input[type=file]');
    _inputs.each(function (i, e) {
        var _this=$(this);
        if (_this.data('wl_File').files.length>0) {
            var _name=_this.attr('name');
            for(i=0;i<_this.data('wl_File').files.length;i++) {
                _addHiddenField(_this,_name+'[]',_this.data('wl_File').files[i]);
            }
        }
    });
}
