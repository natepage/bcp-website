$.fn.extend({
    animateCss: function (options) {
        var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        var defaultOptions = {
            animationName: null,
            removedClass: '',
            addedClass: ''
        };

        options = $.extend(defaultOptions, options);
        console.log("extend");

        if(options.animationName != null){
            console.log("animationName not null");

            if(options.removedClass != ''){
                $(this).removeClass(options.removedClass);
            }

            $(this).addClass('animated ' + options.animationName).one(animationEnd, function() {
                $(this).removeClass('animated ' + options.animationName);

                if(options.addedClass != ''){
                    $(this).addClass(options.addedClass);
                }
            });
        }
    },
    panelHideable: function () {
        return this.each(function(){
            var heading = $(this).children(".panel-heading");
            var body = $(this).children(".panel-body");

            heading.css("cursor", "pointer");
            body.addClass("hidden");

            heading.click(function(){
                if(body.is(":visible")){
                    body.animateCss({
                        animationName: "fadeOut",
                        addedClass: "hidden"
                    });
                } else {
                    body.animateCss({
                        animationName: "fadeIn",
                        removedClass: "hidden"
                    });
                }
            });
        });
    }
});

$(".panel-hideable").panelHideable();