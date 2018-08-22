$(function () {

  'use strict';

  var console = window.console || { log: function () {} };

  var avatarView = $('.avatar-view');
  var avatar = $('#crop-avatar img');
  var avatarModal = $('#avatar-modal');
  var loading = $('.loading');

  var avatarForm = $('.avatar-form');
  var avatarUpload = $('.avatar-upload');
  var avatarSrc = $('.avatar-src');
  var avatarData = $('.avatar-data');
  var avatarInput = $('.avatar-input');
  var avatarSave = $('.avatar-save');

  var avatarWrapper = $('.avatar-wrapper');
  var avatarPreview = $('.avatar-preview');

  var support = {
        fileList: !!$('<input type="file">').prop('files'),
        blobURLs: !!window.URL && URL.createObjectURL,
        formData: !!window.FormData
  };
  support.datauri = support.fileList && support.blobURLs;

  console.log(support);

  var url,active,img,uploaded;

  if (!support.formData) {
    initIframe();
  }

  avatarView.click(function () {
    avatarModal.modal('show');
  });

  avatarInput.change(function () {
    var files,
        file;

    if (support.datauri) {
      files = $(this).prop('files');

      if (files.length > 0) {
        file = files[0];

        if (isImageFile(file)) {
          if (url) {
            URL.revokeObjectURL(url); // Revoke the old one
          }

          url = URL.createObjectURL(file);

          startCropper();
        }
      }
    } else {
      file = $(this).val();

      if (isImageFile(file)) {
        syncUpload();
      }
    }
  });

  avatarSave.click(function () {
    if (!avatarSrc.val() && !avatarInput.val()) {
      return false;
    }

    if (support.formData) {
      ajaxUpload();
      return false;
    }
  });



  function isImageFile(file) {
    if (file.type) {
      return /^image\/\w+$/.test(file.type);
    } else {
      return /\.(jpg|jpeg|png|gif)$/.test(file);
    }
  }

  function startCropper() {

    if (active) {
      img.cropper('replace', url);
    } else {
      img = $('<img src="' + url + '">');
      avatarWrapper.empty().html(img);
      img.cropper({
        aspectRatio: 16/9,
        preview: avatarPreview.selector,
        strict: false,
        crop: function (data) {
          console.log(data);
          var json = [
            '{"x":' + data.x,
            '"y":' + data.y,
            '"height":' + data.height,
            '"width":' + data.width,
            '"rotate":' + data.rotate + '}'
          ].join();

          avatarData.val(json);
        }
      });

      active = true;
    }
  }

  function syncUpload() {
    avatarSave.click();
  }

  function ajaxUpload() {
    var geturl = add_photo_url,
        data = new FormData(avatarForm[0]);

    $.ajax(geturl, {
      type: 'post',
      data: data,
      dataType: 'json',
      processData: false,
      contentType: false,

      beforeSend: function () {
        submitStart();
      },

      success: function (data) {
        submitDone(data);
      },

      error: function (XMLHttpRequest, textStatus, errorThrown) {
        submitFail(textStatus || errorThrown);
      },

      complete: function () {
        submitEnd();
      }
    });
  }

  function submitStart() {
    loading.fadeIn();
  }

  function submitDone(data) {
    console.log(data);

    if ($.isPlainObject(data) && data.state === 200) {
      if (data.result) {
        url = data.result;

        if (support.datauri || uploaded) {
          uploaded = false;
          cropDone();
        } else {
          uploaded = true;
          avatarSrc.val(url);
          startCropper();
        }

        avatarInput.val('');
      } else if (data.message) {
        alert(data.message);
      }
    } else {
      alert('Failed to response');
    }
  }

  function submitFail(msg) {
    alert(msg);
  }

  function submitEnd() {
    loading.fadeOut();
  }

  function cropDone() {
    avatarForm.get(0).reset();
    avatar.attr('src', this.url);
    stopCropper();
    avatarModal.modal('hide');
  }

  function alert(msg) {
    var $alert = [
      '<div class="alert alert-danger avater-alert">',
      '<button type="button" class="close" data-dismiss="alert">&times;</button>',
      msg,
      '</div>'
    ].join('');

    avatarUpload.after($alert);
  }

  function stopCropper() {
    if (active) {
      img.cropper('destroy');
      img.remove();
      active = false;
    }
  }

  function initIframe() {
    var target = 'upload-iframe-' + (new Date()).getTime(),
        $iframe = $('<iframe>').attr({
          name: target,
          src: ''
        });

    // Ready ifrmae
    $iframe.one('load', function () {

      // respond response
      $iframe.on('load', function () {
        var data;

        try {
          data = $(this).contents().find('body').text();
        } catch (e) {
          console.log(e.message);
        }

        if (data) {
          try {
            data = $.parseJSON(data);
          } catch (e) {
            console.log(e.message);
          }

          submitDone(data);
        } else {
          submitFail('Image upload failed!');
        }

        submitEnd();

      });
    });

    avatarForm.attr('target', target).after($iframe.hide());
  }
  /*
  initTooltip();
  initModal();
  addListener();

  function addListener() {
    $('.avatar-view').on('click', click());
    $('.avatar-input').on('change', change());
    $('.avatar-form').on('submit', submit());
  }

  function initTooltip() {
    $('.avatar-view').tooltip({
      placement: 'bottom'
    });
  }

  function initModal() {
    $('#avatar-modal').modal({
      show: false
    });
  }

  function initPreview() {
    var url = $('#crop-avatar img').attr('src');

    $('.avatar-preview').empty().html('<img src="' + url + '">');
  }


  function initIframe() {
    var target = 'upload-iframe-' + (new Date()).getTime(),
        $iframe = $('<iframe>').attr({
          name: target,
          src: ''
        });

    // Ready ifrmae
    $iframe.one('load', function () {

      // respond response
      $iframe.on('load', function () {
        var data;

        try {
          data = $(this).contents().find('body').text();
        } catch (e) {
          console.log(e.message);
        }

        if (data) {
          try {
            data = $.parseJSON(data);
          } catch (e) {
            console.log(e.message);
          }

          submitDone(data);
        } else {
          submitFail('Image upload failed!');
        }

        submitEnd();

      });
    });

    $('.avatar-form').attr('target', target).after($iframe.hide());
  }

  function click() {
    $('#avatar-modal').modal('show');
    initPreview();
  }

  function change() {
    var files,
        file;

    if (support.datauri) {
      files = $(this).prop('files');

      if (true) {
        file = files[0];

        if (isImageFile(file)) {
          if (url) {
            URL.revokeObjectURL(url); // Revoke the old one
          }

          url = URL.createObjectURL(file);
          startCropper();
        }
      }
    } else {
      file = $('.avatar-input').val();

      if (isImageFile(file)) {
        syncUpload();
      }
    }
  }

  function submit() {
    if (!$('.avatar-src').val() && !$('.avatar-input').val()) {
      return false;
    }

    if (support.formData) {
      ajaxUpload();
      return false;
    }
  }

  function isImageFile(file) {
    if (file.type) {
      return /^image\/\w+$/.test(file.type);
    } else {
      return /\.(jpg|jpeg|png|gif)$/.test(file);
    }
  }

  function startCropper() {
    var _this = this;
    if (this.active) {
      this.$img.cropper('replace', this.url);
    } else {
      this.$img = $('<img src="' + this.url + '">');
      $('.avatar-wrapper').empty().html(this.$img);
      this.$img.cropper({
        aspectRatio: 1,
        preview: $('.avatar-preview').selector,
        strict: false,
        crop: function (data) {
          var json = [
            '{"x":' + data.x,
            '"y":' + data.y,
            '"height":' + data.height,
            '"width":' + data.width,
            '"rotate":' + data.rotate + '}'
          ].join();

          _this.$avatarData.val(json);
        }
      });

      this.active = true;
    }
  }

  function stopCropper() {
    if (this.active) {
      this.$img.cropper('destroy');
      this.$img.remove();
      this.active = false;
    }
  }

  function ajaxUpload() {
    var url = $('.avatar-form').attr('action'),
        data = new FormData($('.avatar-form')[0]),
        _this = this;

    $.ajax(url, {
      type: 'post',
      data: data,
      dataType: 'json',
      processData: false,
      contentType: false,

      beforeSend: function () {
        submitStart();
      },

      success: function (data) {
        submitDone(data);
      },

      error: function (XMLHttpRequest, textStatus, errorThrown) {
         submitFail(textStatus || errorThrown);
      },

      complete: function () {
        submitEnd();
      }
    });
  }

  function syncUpload() {
    this.$avatarSave.click();
  }

  function submitStart() {
    this.$loading.fadeIn();
  }

  function submitDone(data) {
    console.log(data);

    if ($.isPlainObject(data) && data.state === 200) {
      if (data.result) {
        this.url = data.result;

        if (support.datauri || this.uploaded) {
          this.uploaded = false;
          this.cropDone();
        } else {
          this.uploaded = true;
          this.$avatarSrc.val(this.url);
          startCropper();
        }

        $('.avatar-input').val('');
      } else if (data.message) {
        this.alert(data.message);
      }
    } else {
      this.alert('Failed to response');
    }
  }

  function submitFail(msg) {
    alert(msg);
  }

  function submitEnd() {
    $('.loading').fadeOut();
  }*/











});