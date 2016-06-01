/**
 * Created by nathanpage on 19/08/2015.
 */
$('.bcp-social-share-link').click(function(){
    window.open($(this).attr('href'), $(this).attr('title'), "height=500,width=1000,modal=yes,alwaysRaised=yes,toolbar=0,location=0,menubar=0,scrollbars=yes, resizable=yes");
    return false;
});

/**
 * Gestion du formulaire des posts
 */
$('.bcp-form-btn-validate').click(function(){
    $(this).button('validate');
});

$('.bcp-form-post-toggle-col').click(function(){
    var imagesContainer = $('.bcp-form-post-images-container');
    var imagesInputs = $('.bcp-form-post-image-input');

    $(this).before(imagesContainer.data('prototype').replace(/__name__/g, imagesInputs.length));

    return false;
});

function imageRemove(toggle){
    $(toggle).parent().parent().remove();
}

function imagePreview(input){
    var parent = $(input).parent();
    var imgPreview = $('<img class="bcp-form-post-image-preview" style="height: 100%;" src="">');
    var file = input.files[0];
    var reader = new FileReader();

    reader.onloadend = function(){
        imgPreview.attr('src', reader.result);
    };

    if(file){
        reader.readAsDataURL(file);
        parent.children('.bcp-form-post-image-preview').remove();
        parent.children('.bcp-form-post-image-empty').remove();
        parent.removeClass('bcp-form-post-image-input-container').addClass('bcp-form-post-image-input-container-remove');
        $(input).css('height', 0);
        parent.append(imgPreview);
    }
}

$('.bcp-form-post-pdf-add').click(function(){
    var pdfsContainer = $('.bcp-form-post-pdfs-container');
    var pdfsInputs = $('.bcp-form-post-pdf-input');

    $(this).before(pdfsContainer.data('prototype').replace(/__name__/g, pdfsInputs.length));

    return false;
});

function pdfRemove(toggle) {
    $(toggle).parent().remove();
}