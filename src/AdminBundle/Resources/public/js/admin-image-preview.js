(function($){
    $(function(){
        /* Add plugins on page load */
        $('.admin-image-preview').adminImagePreview();
        $('.default-preview-image').defaultImagePreview();

        /* Add plugins on elements added */
        $('#sonata-ba-field-container-' + uniqid + '_images').on('sonata.add_element', function(){
            $('.admin-image-preview').adminImagePreview();
            $('.default-preview-image').defaultImagePreview();
        });
    });

    /* Plugin adminImagePreview */
    $.fn.adminImagePreview = function($options){
        var _options = {
            width: '200px',
            height: null,
            noImageWidth: '200px',
            noImageHeight: '200px',
            uniqueInstance: 'admin-image-preview',
            pathAttribute: 'path',
            imgClass: 'img-thumbnail',
            containerId: 'admin-image-preview-container',
            log: false
        };
        _options = $.extend(_options, $options);

        /* Add others options after extend because they are internals */
        _options['counter'] = 0;
        _options['logMessagePrefix'] = 'adminImagePreview';

        return this.each(function(){
            var _self = $(this);
            var number;

            /* Check if logs are enabled */
            var isLog = function(){
                return _options.log;
            };

            /* Add a log in the console */
            var logMessage = function(message, resetSeparators){
                if(isLog()){
                    resetSeparators = resetSeparators || false;

                    if(resetSeparators){
                        $.AdminBundle.ConsoleLogger.resetSeparators();
                    }

                    $.AdminBundle.ConsoleLogger.prefixedMessage(_options.logMessagePrefix, number, message);
                }
            };

            /* Add a line separator in the console for show a task starting */
            var logStartMessage = function(message, header){
                if(isLog()){
                    header = header || '';

                    $.AdminBundle.ConsoleLogger.resetSeparators();

                    if(header != ''){
                        $.AdminBundle.ConsoleLogger.prefixedSeparator(_options.logMessagePrefix, number, header);
                    } else {
                        $.AdminBundle.ConsoleLogger.prefixedSeparator(_options.logMessagePrefix, number, '', message.length);
                    }

                    $.AdminBundle.ConsoleLogger.prefixedMessage(_options.logMessagePrefix, number, message);
                }
            };

            /* Add a line separator in the console for show a task finishing */
            var logFinishMessage =  function(message, header){
                if(isLog()){
                    message = message || '';

                    if(message != ''){
                        $.AdminBundle.ConsoleLogger.prefixedMessage(_options.logMessagePrefix, number, message);
                    }
                    $.AdminBundle.ConsoleLogger.prefixedSeparator(_options.logMessagePrefix, number, header);
                }
            };

            /* One plugin instance per input */
            if(_self.attr(_options.uniqueInstance) !== undefined){
                return null;
            } else {
                _self.attr(_options.uniqueInstance, 'enabled');
                number = _options.counter;
                _options.counter++;

                logStartMessage('Plugin enabled');
            }

            /* Get the input's parent */
            var getParent = function(){
                return _self.parent();
            };

            /* Get the image's path */
            var getPath = function(){
                return _self.attr(_options.pathAttribute);
            };

            /* Generate the container's id and handle elements counter */
            var generateContainerId = function(){
                return _options.containerId + '-' + number;
            };

            /* Get the container's id */
            var getContainerId = function(){
                return '#' + _options.containerId + '-' + number;
            };

            /* Hide the input file */
            var renderInput = function(){
                var parent = getParent();
                var path = getPath();

                parent.css('position', 'relative');

                _self.css({
                    'opacity': 0,
                    'position': 'absolute'
                });

                if(path === undefined){
                    _self.css({
                        'width': '95%',
                        'height': '95%',
                        'cursor': 'pointer'
                    });
                } else {
                    _self.css({
                       'top': '-9999px'
                    });
                }

                logMessage('Input rendered');
            };

            /* Render the image's preview container */
            var renderContainer = function(){
                var parent = getParent();

                parent.append($('<div></div>')
                    .attr('id', generateContainerId())
                    .css({
                    'width': '100%',
                    'height': '100%',
                    'text-align': 'center'
                }));

                logMessage('Container rendered');
            };

            /* Render the image's preview */
            var renderImagePreview = function(){
                var container = $(getContainerId());
                var path = getPath();
                var imagePreview;

                if(path !== undefined){
                    imagePreview = $('<img src="' + path + '" />');

                    imagePreview.css({
                        'max-height': _options.height,
                        'max-width': _options.width
                    });
                } else {
                    imagePreview = $('<div><i class="fa fa-5x fa-plus-circle"></i></div>');

                    imagePreview.css({
                        'height': _options.noImageHeight,
                        'width': _options.noImageWidth,
                        'padding-top': '20%'
                    });
                }

                imagePreview.addClass(_options.imgClass);
                container.html(imagePreview);
                logMessage('Image preview rendered');
            };

            /* Render the structure */
            var renderFormWidget = function(){
                renderInput();
                renderContainer();
                renderImagePreview();

                logMessage('Structure rendered');
            };

            /* Plugin initialization*/
            var initFormWidget = function(){
                renderFormWidget();
                logFinishMessage('Plugin initialized');
            };
            initFormWidget();

            /* Input changes handler */
            _self.change(function(){
                logStartMessage('Input changed', 'File\'s changes handler');

                var file = this.files[0];
                var reader = new FileReader();

                reader.onloadend = function(){
                    logMessage('File loaded');
                    _self.attr(_options.pathAttribute, reader.result);
                    renderImagePreview();
                    logFinishMessage()
                };

                if(file){
                    reader.readAsDataURL(file);
                }
            });
        });
    };

    /* Plugin defaultImagePreview */
    $.fn.defaultImagePreview = function($options){
        var _options = {
            uniqueInstance: 'default-image-preview',
            checkboxClass: 'checkbox-default-preview-image',
            lastClickField: 'lastClickField',
            log: false
        };
        _options = $.extend(_options, $options);

        /* Add others options after extend because they are internals */
        _options['counter'] = 0;
        _options['logMessagePrefix'] = 'defaultImagePreview';
        _options['checkedFromServerAttr'] = 'checked-from-server';

        return this.each(function(){
            var _self = $(this);
            var number;

            /* Check if logs are enabled */
            var isLog = function(){
                return _options.log;
            };

            /* Add a log in the console */
            var logMessage = function(message, resetSeparators){
                if(isLog()){
                    resetSeparators = resetSeparators || false;

                    if(resetSeparators){
                        $.AdminBundle.ConsoleLogger.resetSeparators();
                    }

                    $.AdminBundle.ConsoleLogger.prefixedMessage(_options.logMessagePrefix, number, message);
                }
            };

            /* Add a line separator in the console for show a task starting */
            var logStartMessage = function(message, header){
                if(isLog()){
                    header = header || '';

                    $.AdminBundle.ConsoleLogger.resetSeparators();

                    if(header != ''){
                        $.AdminBundle.ConsoleLogger.prefixedSeparator(_options.logMessagePrefix, number, header);
                    } else {
                        $.AdminBundle.ConsoleLogger.prefixedSeparator(_options.logMessagePrefix, number, '', message.length);
                    }

                    $.AdminBundle.ConsoleLogger.prefixedMessage(_options.logMessagePrefix, number, message);
                }
            };

            /* Add a line separator in the console for show a task finishing */
            var logFinishMessage =  function(message, header){
                if(isLog()){
                    message = message || '';

                    if(message != ''){
                        $.AdminBundle.ConsoleLogger.prefixedMessage(_options.logMessagePrefix, number, message);
                    }
                    $.AdminBundle.ConsoleLogger.prefixedSeparator(_options.logMessagePrefix, number, header);
                }
            };

            /* One plugin instance per input */
            if(_self.attr(_options.uniqueInstance) !== undefined){
                return null;
            } else {
                _self.attr(_options.uniqueInstance, 'enabled');
                number = _options.counter;
                _options.counter++;

                logStartMessage('Plugin enabled');
            }

            /* Share the last clicked checkbox in window to handle the html code replacement */
            var setLastClickedCheckbox = function(number){
                window[_options.lastClickField] = number;
            };

            /* Get the shared last clicked checkbox number */
            var getLastClickedCheckbox = function(){
                return window[_options.lastClickField];
            };

            /* Handle the last clicked checkbox's number update */
            var handleLastClickedCheckboxNumber = function(checkbox, parent){
                var lastClickedCheckboxNumber = getLastClickedCheckbox();

                if(_self.attr(_options.checkedFromServerAttr) !== undefined){
                    setLastClickedCheckbox(number);
                    logMessage('[From Server] Last Clicked');
                } else if(lastClickedCheckboxNumber !== undefined && lastClickedCheckboxNumber == number){
                    updateCheckbox(checkbox, parent, true);
                    logMessage('Last clicked');
                }
            };

            /* Get the checkbox representation */
            var getCheckbox = function(){
                return _self.next();
            };

            /* Get the parent */
            var getParent = function(){
                return _self.parent();
            };

            /* Handle the checkbox update */
            var updateCheckbox = function(checkbox, parent, isChecked){
                if(isChecked){
                    checkbox.prop('checked', true);
                    parent.addClass('checked');
                } else {
                    checkbox.prop('checked', false);
                    parent.removeClass('checked');
                }
            };

            /* Plugin initialization */
            var initPlugin = function(){
                var checkbox                  = getCheckbox();
                var checkboxClass             = _options.checkboxClass;
                var counterAttr               = _options.checkboxClass + '-number';
                var parent                    = getParent();

                checkbox.addClass(checkboxClass);
                checkbox.attr(counterAttr, number);
                handleLastClickedCheckboxNumber(checkbox, parent);

                checkbox.click(function(){
                    logMessage('Clicked');

                    setLastClickedCheckbox(number);
                    logMessage('Shared in window');

                    logStartMessage('', '[Each Checkbox Loop]');
                    $('.' + checkboxClass).each(function(){
                        var input         = $(this).prev();
                        var parent        = $(this).parent();
                        var currentNumber = $(this).attr(counterAttr);

                        if(number == currentNumber){
                            updateCheckbox(input, parent, true);
                            logMessage('Checkbox_' + currentNumber + ':checked = true');
                        } else {
                            updateCheckbox(input, parent, false);
                            logMessage('Checkbox_' + currentNumber + ':checked = false');
                        }
                    });
                    logFinishMessage();
                });
                logFinishMessage('Plugin initialized');
            };
            initPlugin();
        });
    };
})(jQuery);