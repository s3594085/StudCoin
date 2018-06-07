<?php session_start();?>
<!doctype html>
<html lang="en">
<?php include('include/head.inc.php');?>
<?php include('include/nav.inc.php');?>
<?php include('getUTXO.php');?>
<?php include('storeData.php');?>


<body>



  <!-- Modal -->
  <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

        </div>

      </div>
    </div>
  </div>








    <div class="lll">
        <h1 style="text-align:center">Welcome <?php echo htmlspecialchars($_SESSION['username']);?></h1>
        <div class="container">
          <div id="itemsList">
          </div>
        </div>


        <hr />

        <p><small>Thanks for visiting</small></p>
    </div>
    <script>
      function myFunc(e){
        $.post('storeData.php', {getItem: 'true', itemID: e})
        .done(function(data){
          $(".modal-body").html(data);
          $("#exampleModalCenter").modal();
        });

      }
      window.onload = function(){
            $.ajax({
            'url': 'storeData.php',
            'type': 'POST',
            'data': {loadItems:'true'},
            'success': function(result){
                $('#itemsList').html(result);
            }
        })
      };

      $(".canClick").on('click', function(e){

        alert(e);
      });

    </script>
</body>
</html>
