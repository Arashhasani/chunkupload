<!DOCTYPE html>
<html lang="en">

<head>
    <title>Default example - fileuploader - Innostudio.de</title>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Default example - fileuploader - Innostudio.de">
    <meta name="robots" content="noindex">

    <link rel="shortcut icon" href="https://innostudio.de/fileuploader/images/favicon.ico">


    <!-- js -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" crossorigin="anonymous"></script>

    <script src="/js/custom.js" type="text/javascript"></script>




    <!-- 1. Link the css and js files -->
    <link href="dist/font/font-fileuploader.css" rel="stylesheet">
    <link href="dist/jquery.fileuploader.min.css" rel="stylesheet">

    <script src="dist/jquery.fileuploader.min.js" type="text/javascript"></script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            line-height: normal;
            background-color: #fff;

            margin: 0;
        }

        form {
            margin: 15px;
        }

        .fileuploader {
            max-width: 560px;
        }
    </style>
</head>

<body>
<form action="{{ route('example.upload') }}" method="post" id="myform"  enctype="multipart/form-data">
    {{ csrf_field() }}
    @csrf

    <input type="file" name="files" id="files">

    <input type="submit">

    <div class="form-status"></div>

</form>
{{--<form action="{{ route('upload') }}" method="post" enctype="multipart/form-data">--}}
{{--    @csrf--}}
{{--    <!-- file input -->--}}
{{--    <input type="file" name="files">--}}
{{--    <input type="file" name="files" data-upload-url="{{ route('upload') }}" data-upload-token="{{ csrf_token() }}">--}}

{{--    <input type="submit">--}}
{{--</form>--}}

<script>


    // define the form and the file input
    var $form = $('#myform');

    // enable fileuploader plugin
    $form.find('input:file').fileuploader({
        addMore: true,
        changeInput: '<div class="fileuploader-input">' +
            '<div class="fileuploader-input-inner">' +
            '<div>${captions.feedback} ${captions.or} <span>${captions.button}</span></div>' +
            '</div>' +
            '</div>',
        theme: 'dropin',




        dragDrop:true,
        thumbnails: {
            onImageLoaded: function(item) {
                if (!item.html.find('.fileuploader-action-edit').length)
                    item.html.find('.fileuploader-action-remove').before('<button type="button" class="fileuploader-action fileuploader-action-popup fileuploader-action-edit" title="Edit"><i class="fileuploader-icon-edit"></i></button>');

                // hide current thumbnail (this is only animation)
                if (item.imageIsUploading) {
                    item.image.addClass('fileuploader-loading').html('');
                }
            }
        },
        editor:true,



        upload: true,
        chunk: 10,
        enableApi: true,
        onSelect: function(item) {
            item.upload = null;
        },
        onRemove: function(item) {
            if (item.data.uploaded)
                $.post('./php/ajax_remove_file.php', {
                    file: item.name
                });
        },
        captions: $.extend(true, {}, $.fn.fileuploader.languages['en'], {
            feedback: 'Drag and drop files here',
            or: 'or',
            button: 'Browse Files'
        })
    });

    // form submit
    $form.on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(),
            _fileuploaderFields = [];

        // append inputs to FormData
        $.each($form.serializeArray(), function(key, field) {
            formData.append(field.name, field.value);
        });
        // append file inputs to FormData
        $.each($form.find("input:file"), function(index, input) {
            var $input = $(input),
                name = $input.attr('name'),
                files = $input.prop('files'),
                api = $.fileuploader.getInstance($input);


            // add fileuploader files to the formdata
            if (api) {
                if ($.inArray(name, _fileuploaderFields) > -1)
                    return;
                files = api.getChoosedFiles();
                _fileuploaderFields.push($input);
            }

            for(var i = 0; i<files.length; i++) {
                formData.append(name, (files[i].file ? files[i].file : files[i]), (files[i].name ? files[i].name : false));
            }
        });

        $.ajax({
            url: $form.attr('action') || '#',
            data: formData,
            type: $form.attr('method') || 'POST',
            enctype: $form.attr('enctype') || 'multipart/form-data',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $form.find('.form-status').html('<div class="progressbar-holder"><div class="progressbar"></div></div>');
                $form.find('input[type="submit"]').attr('disabled', 'disabled');
            },
            xhr: function() {
                var xhr = $.ajaxSettings.xhr();

                if (xhr.upload) {
                    xhr.upload.addEventListener("progress", this.progress, false);
                }

                return xhr;
            },
            success: function(result, textStatus, jqXHR) {
                console.log(result);
                // update input values
                try {
                    var data = JSON.parse(result);

                    for(var key in data) {
                        var field = data[key],
                            api;

                        // if fileuploader input
                        if (field.files) {
                            var input = _fileuploaderFields.filter(function(element) {
                                    return key == element.attr('name').replace('[]', '');
                                }).shift(),
                                api = input ? $.fileuploader.getInstance(input) : null;

                            if (field.hasWarnings) {
                                for (var warning in field.warnings) {
                                    alert(field.warnings[warning]);
                                }

                                return this.error ? this.error(jqXHR, textStatus, field.warnings) : null;
                            }

                            if (api) {
                                // update the fileuploader's file names
                                for (var i = 0; i<field.files.length; i++) {
                                    $.each(api.getChoosedFiles(), function(index, item) {
                                        if (field.files[i].old_name == item.name) {
                                            item.name = field.files[i].name;
                                            item.html.find('.column-title > div:first-child').text(field.files[i].name).attr('title', field.files[0].name);
                                        }
                                        item.data.uploaded = true;
                                    });
                                }

                                api.updateFileList();
                            }
                        } else {
                            $form.find('[name="'+ key +'"]:input').val(field);
                        }
                    }
                } catch (e) {}

                $form.find('.form-status').html('<p class="text-success">Success!</p>');
                $form.find('input[type="submit"]').removeAttr('disabled');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $form.find('.form-status').html('<p class="text-error">Error!</p>');
                $form.find('input[type="submit"]').removeAttr('disabled');
            },
            progress: function(e) {
                if (e.lengthComputable) {
                    var t = Math.round(e.loaded * 100 / e.total).toString();

                    $form.find('.form-status .progressbar').css('width', t + '%');
                }
            }
        });
    });







</script>
</body>


</html>
