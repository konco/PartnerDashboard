(function($) {
  //'use strict';
  getStatus = function(val) {
    'use strict';
    
    $('#form-edit #status').html('');
    var status = ['Active', 'Disable'];
    var btn = '';
    var selected = '';

    status.forEach(function (item, index) {
      if(item === val) {
        selected = 'selected="selected"';
      }else{
      selected = '';
      }
      btn += '<option value="'+item+'" '+selected+'>'+item+'</option>';
    });
    $('#form-edit #status').append(btn);

  }

  showSuccessMessage = function(message) {
    'use strict';
    resetPositionMessage();
    $.toast({
      heading: 'Success',
      text: message,
      showHideTransition: 'slide',
      icon: 'success',
      loaderBg: '#f96868',
      position: 'top-right'
    })
  }

  showDangerMessage = function(message) {
    'use strict';
    resetPositionMessage();
    $.toast({
      heading: 'Danger',
      text: message,
      showHideTransition: 'slide',
      icon: 'error',
      loaderBg: '#f2a654',
      position: 'top-right'
    })
  }

  resetPositionMessage = function() {
    $('.jq-toast-wrap').removeClass('bottom-left bottom-right top-left top-right mid-center'); // to remove previous position class
    $(".jq-toast-wrap").css({
      "top": "",
      "left": "",
      "bottom": "",
      "right": ""
    }); //to remove previous position style
  }

  getStatusBtn = function(status) {
    let badge = status == 'ACTIVE' ? 'success' : 'danger';
    btn_status = '<label class="badge badge-'+badge+'">'+status+'</label>';

    return btn_status;
  }

  getTrxStatusBtn = function(status) {
    let badge = ''
    if(status == 'SUCCESS'){
      badge = 'success';
    }else if(status == 'PENDING'){
      badge = 'warning';
    }else if(status == 'FAIL'){
      badge = 'danger';
    }

    btn_status = '<label class="badge badge-'+badge+'">'+status+'</label>';

    return btn_status;
  }

  formatRupiah = function(angka) {
    'use strict';

    var number_string = angka.toString(),
    number_string = number_string.replace(/\./g,','),
    split       = number_string.split(','),
    sisa        = split[0].length % 3,
    rupiah        = split[0].substr(0, sisa),
    ribuan        = split[0].substr(sisa).match(/\d{3}/gi);
   
    // tambahkan titik jika yang di input sudah menjadi angka ribuan
    if(ribuan){
      var separator = sisa ? '.' : '';
      rupiah += separator + ribuan.join('.');
    }
   
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;


  }

  isKeyNumber = function (evt)
  {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

    return true;
  }
  
  /*$.validator.setDefaults({
    submitHandler: function() {
      alert("submitted!!!!");
    }
  });*/

   /*Quill editor*/
  if ($("#quillExample1").length) {
    var quill = new Quill('#quillExample1', {
      modules: {
        toolbar: [
          [{
            header: [1, 2, false]
          }],
          ['bold', 'italic', 'underline'],
          ['image', 'code-block']
        ]
      },
      placeholder: 'Compose an epic...',
      theme: 'snow' // or 'bubble'
    });
  }

  /*simplemde editor*/
  if ($("#simpleMde").length) {
    var simplemde = new SimpleMDE({
      element: $("#simpleMde")[0],
      spellChecker: false,
      styleSelectedText: false,
      toolbar: ["bold", "italic", "heading", "|", "unordered-list", "ordered-list", "quote", "|", "link", "preview", "side-by-side", "fullscreen"],
      blockStyles: {
        bold: "<bold>",
        italic: "<italic>",
      },
    });
  }

  /*Tinymce editor*/
  if ($("#tinyMceExample").length) {
    tinymce.init({
      selector: '#tinyMceExample',
      height: 500,
      theme: 'silver',
      plugins: [
        'advlist autolink lists link image charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
      ],
      toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
      toolbar2: 'print preview media | forecolor backcolor emoticons | codesample help',
      image_advtab: true,
      templates: [{
          title: 'Test template 1',
          content: 'Test 1'
        },
        {
          title: 'Test template 2',
          content: 'Test 2'
        }
      ],
      content_css: []
    });
  }
  
})(jQuery);