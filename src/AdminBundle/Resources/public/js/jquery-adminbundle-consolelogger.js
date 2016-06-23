$.AdminBundle = {
    ConsoleLogger: {
        _options: {
            lastMessageLength: null,
            lastPrefixedMessageLength: null,
            separatorNumber: 2,
            separatorChar: "-"
        },
        resetSeparators: function(){
            this._options.lastMessageLength = null;
            this._options.lastPrefixedMessageLength = null;
        },
        getPrefixedMessage: function(prefix, number, message){
            return '[' + prefix + '][' + number + '] ' + message;
        },
        generateSeparator: function(lastLength, header, separatorChar, separatorNumber){
            header = header || '';
            separatorChar = separatorChar || this._options.separatorChar;
            separatorNumber = separatorNumber || this._options.separatorNumber;

            var line;
            var mod;
            var repeatChar = lastLength !== null ? lastLength : separatorNumber;

            if(header !== ''){
                repeatChar -= header.length;
                repeatChar = repeatChar >= separatorNumber ? repeatChar : separatorNumber;
                mod = repeatChar % 2;
                repeatChar = mod == 0 ? repeatChar : repeatChar + mod;
                repeatChar = repeatChar / 2;

                line = separatorChar.repeat(repeatChar) + header + separatorChar.repeat(repeatChar);
            } else {
                line = separatorChar.repeat(repeatChar);
            }

            return line;
        },
        message: function(message){
            if(message.length > this._options.lastMessageLength || resetSeparators){
                this._options.lastMessageLength = message.length;
            }

            console.log(message);
        },
        prefixedMessage: function(prefix, number, message){
            if(message.length > this._options.lastPrefixedMessageLength){
                this._options.lastPrefixedMessageLength = message.length;
            }

            message = this.getPrefixedMessage(prefix, number, message);
            console.log(message);
        },
        separator: function(header, separatorNumber, separatorChar){
            var line = this.generateSeparator(this._options.lastMessageLength, header, separatorChar, separatorNumber);
            this.message(line, true);
        },
        prefixedSeparator: function(prefix, number, header, separatorNumber, separatorChar){
            var line = this.generateSeparator(this._options.lastPrefixedMessageLength, header, separatorChar, separatorNumber);
            this.prefixedMessage(prefix, number, line, true);
        }
    }
};