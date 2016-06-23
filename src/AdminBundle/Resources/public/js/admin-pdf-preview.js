(function($){
    $(function(){
        /* Add plugins on page load */
        $('.admin-pdf-preview').adminPdfPreview({log: true});

        /* Add plugins on elements added */
        $('#sonata-ba-field-container-' + uniqid + '_pdfs').on('sonata.add_element', function(){
            $('.admin-pdf-preview').adminPdfPreview({log: true});
        });
    });

    /* Plugin adminImagePreview */
    $.fn.adminPdfPreview = function($options){
        var _options = {
            uniqueInstance: 'admin-pdf-preview',
            altAttr: 'admin-pdf-alt',
            containerId: 'admin-pdf-preview-container',
            log: false
        };
        _options = $.extend(_options, $options);

        /* Add others options after extend because they are internals */
        _options['counter'] = 0;
        _options['logMessagePrefix'] = 'adminPdfPreview';

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
            var logFinishMessage = function(message, header){
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

            /* Get the pdf's alt */
            var getAlt = function(){
                return _self.attr(_options.altAttr);
            };

            /* Hide the input file */
            var hideInput = function(){
                _self.hide();
            };

            /* Update the input's style */
            var updateInputCss = function(){
                _self.css({
                    'display': 'inline'
                });
            };

            /* Update the parent's style */
            var updateParentCss = function(){
                var parent = getParent();

                parent.css({
                    'text-align': 'center',
                    'padding-top': '3%'
                });
            };

            /* Append alt to parent */
            var appendAlt = function(alt){
                var parent = getParent();

                parent.append(alt);
            };

            /* Plugin initialization*/
            var initFormWidget = function(){
                var alt = getAlt();

                updateParentCss();

                if(alt !== undefined){
                    hideInput();
                    appendAlt(alt);
                } else {
                    updateInputCss();
                }

                logFinishMessage('Plugin initialized');
            };
            initFormWidget();
        });
    };
})(jQuery);