<div class="row">
  <div class="col-sm-4"></div>
  <div class="col-sm-8" style="padding: 10% 0;">
    <form class="form-horizontal" id="loginUserForm">
      
      <div class="form-group">
        <label class="control-label col-sm-2" for="user">Username:</label>
        <div class="col-sm-3">
          <input type="text" class="form-control" id="user" name="user">
         </div>
      </div>
      <div class="form-group">
        <label class="control-label col-sm-2" for="pass">Password:</label>
         <div class="col-sm-3">
          <input type="password" class="form-control" id="pass" name="pass">
         </div>
      </div>
      <div class="form-group">
         <div class="col-sm-offset-2 col-sm-10">
          <button type="submit" class="btn btn-custom">LOGIN</button>
         </div>
      </div>
    </form>
  </div>
</div>
</div>
</body>
<script>

$(function() {
  $('#loginUserForm').submit(function(e){
    e.preventDefault();
    $.ajax({ 
            type:'POST',
            url:'<?php echo base_url('Mpdi_log_ctrl/login'); ?>',
            data: $(this).serialize(),
            success: function(data){
              var res = JSON.parse(data);
              //console.log(data);
              //console.log(data+" "+res[0]);
              
              
              if(res[1]=='Success')
                location.reload();
              else
                swal("",res[0],res[1].toLowerCase());
              
            }      
       });      
  });
});

</script>
</html>