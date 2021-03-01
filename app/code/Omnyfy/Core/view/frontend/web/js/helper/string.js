define(
    [],
    function() {
        return {
            STRPAD_DEFAULT_PAD: ' ',
            STRPAD_LEFT: 'L',
            STRPAD_RIGHT: 'R',

            strpad: function(str, max, pad, lr) {
                str = str.toString();
                pad = pad ? pad : this.STRPAD_DEFAULT_PAD;
                lr  = lr ? lr : this.STRPAD_LEFT;

                if (pad.length > 0) {
                    while (str.length < max) {
                        if (lr == this.STRPAD_RIGHT) {
                            str = str + pad;
                        } else {
                            str = pad + str;
                        }
                    }
                }

                return str.substr(0, max);
            }
        }
    }
);
