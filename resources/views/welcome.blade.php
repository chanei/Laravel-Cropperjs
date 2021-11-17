<!DOCTYPE html>
<html>
    <head>
        <title>Laravel Crop Image Before Upload using Cropper JS</title>
        <meta name="_token" content="{{ csrf_token() }}">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha256-WqU1JavFxSAMcLP2WIOI+GB2zWmShMI82mTpLDcqFUg=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" integrity="sha256-jKV9n9bkk/CTP8zbtEtnKaKf+ehRovOYeKoyfthwbC8=" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js" integrity="sha256-CgvH7sz3tHhkiVKh05kSUgG97YtzYNnWt6OXcmYzqHY=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <style type="text/css">
        img {
            display: block;
            max-width: 100%;
        }
        .preview {
            overflow: hidden;
            width: 200px;
            height: 200px;
            border: 1px solid red;
        }
        #side {
            padding: 10px;
        }
        .modal-lg{
            max-width: 1000px !important;
        }
    </style>
    <body>
        <div class="container">
            <label>Laravel Crop Image Before Upload using Cropper JS</label>
            <br>
            <input type="file" name="image" class="image">
        </div>

        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Laravel Crop Image Before Upload using Cropper JS</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="img-container">
                            <div class="row">
                                <div class="col-md-7">
                                    <img id="image">
                                </div>
                                <div class="col-md-5">
                                    <div class="row" id="side">
                                        <div class="col-md-12">
                                            <div class="preview"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Width</label>
                                            <input type="text" class="form-control" id="image-width">
                                        </div>
                                        <div class="col-md-6">
                                            <label>Height</label>
                                            <input type="text" class="form-control" id="image-height">
                                        </div>
                                        <div class="col-md-12">
                                            <button class="btn btn-primary" id="rotate-left"><i class="fa fa-undo"></i></button>
                                            <button class="btn btn-primary" id="rotate-right"><i class="fa fa-redo"></i></button>
                                            <button class="btn btn-primary" id="greyscale"><i class="fa fa-paint-brush"></i></button>
                                            <button class="btn btn-primary" id="reset-greyscale"><i class="fa fa-eraser"></i></button>
                                        </div>
                                    </div>
                                    <div class="row" id="side">
                                        <div class="col-md-12">
                                            <canvas id="textcanvas" width=200 height=200 style="border:1px solid red;"></canvas>
                                        </div>
                                        <div class="col-md-12">
                                            <label>Text</label>
                                            <input type="text" class="form-control" id="image-text">
                                        </div>
                                    </div>
                                    <div class="row" id="side">
                                        <div class="col-md-12">
                                            <label>Text Padding</label>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Top</label>
                                            <input type="text" class="form-control" id="image-text-top-padding" value="20">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Left</label>
                                            <input type="text" class="form-control" id="image-text-left-padding" value="20">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Color</label>
                                            <input type="text" class="form-control" id="image-text-color" value="black">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Size</label>
                                            <input type="text" class="form-control" id="image-text-size" value="15">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="crop">Crop</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            var $modal = $('#modal');
            var image = document.getElementById('image');
            var cropper;
            var save_black_white = false;

            $("body").on("change", ".image", function(e){
                var files = e.target.files;
                var done = function (url) {
                    image.src = url;
                    $modal.modal('show');
                };
                var reader;
                var file;
                var url;

                if (files && files.length > 0) {
                    file = files[0];
                    if (URL) {
                        done(URL.createObjectURL(file));
                    } else if (FileReader) {
                        reader = new FileReader();
                        reader.onload = function (e) {
                            done(reader.result);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });

            $modal.on('shown.bs.modal', function () {
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 3,
                    preview: '.preview',
                    rotatable: true,
                    crop(event) {
                        document.getElementById("image-width").value = Math.round(event.detail.width);
                        document.getElementById("image-height").value = Math.round(event.detail.height);
                    },
                });

                var textcanvas = document.getElementById("textcanvas");
                var context = textcanvas.getContext("2d");
                document.getElementById('image-text').addEventListener("keyup", function (evt) {
                    var imagetext = $('#image-text').val();
                    var toppadding = $('#image-text-top-padding').val();
                    var leftpadding = $('#image-text-left-padding').val();
                    var imagetextcolor = $('#image-text-color').val();
                    var imagetextsize = $('#image-text-size').val();
                    var maxWidth = 200 - leftpadding;
                    var lineHeight = 25;

                    context.font = context.font.replace(/\d+px/, imagetextsize + "px");
                    context.fillStyle = imagetextcolor;
                    // context.fillText(imagetext, leftpadding, toppadding);
                    wrapText(context,imagetext, leftpadding, toppadding, maxWidth, lineHeight);

                 }, false);

                $('#rotate-right').click(function() {
                    cropper.rotate(45);
                });
                $('#rotate-left').click(function() {
                    cropper.rotate(-45);
                });
                $('#greyscale').click(function() {
                    $('.preview').css({
                        'mix-blend-mode': 'luminosity',
                    });
                    save_black_white = true;
                });
                $('#reset-greyscale').click(function() {
                    $('.preview').css({
                        'mix-blend-mode': '',
                    });
                    save_black_white = false;
                });
            }).on('hidden.bs.modal', function () {
                cropper.destroy();
                cropper = null;
            });

            $("#crop").click(function(){
                canvas = cropper.getCroppedCanvas({
                    width: $('#image-width').val(),
                    height: $('#image-width').val(),
                });

                const ctx = canvas.getContext("2d");

                // check if true and save blackwhite image
                if(save_black_white) {
                    let imgData = ctx.getImageData(0, 0, ctx.canvas.width, ctx.canvas.height);
                    let pixels = imgData.data;
                    for (var i = 0; i < pixels.length; i += 4) {

                    let lightness = parseInt((pixels[i] + pixels[i + 1] + pixels[i + 2])/3);

                    pixels[i] = lightness;
                    pixels[i + 1] = lightness;
                    pixels[i + 2] = lightness;
                    }
                    ctx.putImageData(imgData, 0, 0);
                }

                // text varibales
                const imagetext = $('#image-text').val();
                const toppadding = $('#image-text-top-padding').val();
                const leftpadding = $('#image-text-left-padding').val();
                const imagetextcolor = $('#image-text-color').val();
                const imagetextsize = $('#image-text-size').val();
                const maxWidth = $('#image-width').val() - leftpadding;
                const lineHeight = 25;

                // add text to image
                if(imagetext.length > 0) {
                    ctx.font = ctx.font.replace(/\d+px/, imagetextsize + "px");
                    ctx.fillStyle = imagetextcolor;
                    // ctx.fillText(imagetext, leftpadding, toppadding);
                    wrapText(ctx,imagetext, leftpadding, toppadding, maxWidth, lineHeight);
                }

                canvas.toBlob(function(blob) {
                    url = URL.createObjectURL(blob);
                    var reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function() {
                        var base64data = reader.result;

                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "image-cropper",
                            data: {
                                '_token': $('meta[name="_token"]').attr('content'),
                                'image': base64data
                            },
                            success: function(data){
                                $modal.modal('hide');
                                alert("success upload image");
                            }
                        });
                    }
                });
            });

            function wrapText(context, text, x, y, maxWidth, lineHeight) {
                var words = text.split(' ');
                var line = '';

                for(var n = 0; n < words.length; n++) {
                    var testLine = line + words[n] + ' ';
                    var metrics = context.measureText(testLine);
                    var testWidth = metrics.width;
                    if (testWidth > maxWidth && n > 0) {
                        context.fillText(line, x, y);
                        line = words[n] + ' ';
                        y += lineHeight;
                    }
                    else {
                        line = testLine;
                    }
                }
                context.fillText(line, x, y);
            }

        </script>
    </body>
</html>
