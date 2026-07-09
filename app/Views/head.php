<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $page_title;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon.png');?>">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <?= link_tag('assets/css/qompac-ui.minf700.css?v=1.0.1') ?>
    <?= link_tag('assets/css/custom.minf700.css?v=1.0.1') ?>
    <?= link_tag('assets/css/customizer.minf700.css?v=1.0.1') ?>
  
     <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?=base_url('assets/css/print.css')?>" media="print">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"  integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <?= link_tag('assets/css/lib/chosen/chosen.min.css') ?>
    
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="<?=base_url('assets/js/jquery-3.3.1.min.js')?>"></script>
    
      <script language="javascript">
        function checkInput(ob) {
            var invalidChars = /[^0-9.]/gi;
            if(invalidChars.test(ob.value)) {
              ob.value = ob.value.replace(invalidChars,"");
            }
        }
        function removeSpace(ob) {
            var invalidChars = /\s/g;
            if(invalidChars.test(ob.value)) {
              ob.value = ob.value.replace(invalidChars,"");
            }
        }
         function checkNumber(ob) {
            var invalidChars = /[^0-9.,]/gi;
            if(invalidChars.test(ob.value)) {
              ob.value = ob.value.replace(invalidChars,"");
            }
        }
        var csrfName = '<?= csrf_token() ?>';
        var csrfHash = '<?= csrf_hash() ?>';
        document.addEventListener("contextmenu", function(e){
            e.preventDefault();
        }, false);   
        document.onkeydown = function(e) {
            if (e.ctrlKey && e.keyCode === 85) {  return false;  }
            if (e.shiftKey && (e.which == 188 || e.which == 190)) {
                e.preventDefault();
             }
        };         
        $(function(){
            $('input').on("keydown", function(e){
              if (e.shiftKey && (e.which == 188 || e.which == 190)) {
                e.preventDefault();
             }
            });
        }); 
        $(document).on('input', 'input, textarea', function(e){
           if (e.originalEvent.inputType == 'insertFromPaste') {               
               var regex = new RegExp("^[a-zA-Z0-9@. ]+$");
               if (regex.test($(this).val())) { }
               else { alert('Only english language characters accepted.');  $(this).val($(this).val().replace(/[^A-Za-z0-9@. ]/g,'').trim()); }
           }
       });
    </script>
     <script>
$(document).ready(function(){ 
    var invalidChars = /[&\%#;]/;
    $('input').keyup(function(){
         if(invalidChars.test($(this).val())) {
             var str = $(this).val();
             var res = str.replace(invalidChars, "");
           $(this).val(res);
        alert("These 5 special characters(& '/' % # ;) are not allowed. ");
    }
    });
});
</script>
<style>
    #ui-datepicker-div{z-index: 9999 !important;}
</style>
</head>
<body class="container-width">
     <!-- loader Start -->
    <div id="loading">
        <div class="loader1 simple-loader">
            <div class="loader-body ">
                <img src="<?=base_url('assets/images/loader.gif');?>" class="logo-title" style="width:5% "alt="loader" class="image-loader img-fluid ">
            </div>
        </div>
    </div>    
