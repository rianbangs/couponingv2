<div class="row">
  <div class="col-sm-5" style="margin-left:70px;">
    <form class="form-horizontal" id="addUserForm">
      <div class="form-group">
        <label class="control-label col-sm-2" for="u_fn">First Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="u_fn" name="u_fn">
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-sm-2" for="u_ln">Last Name:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="u_ln" name="u_ln">
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-sm-2" for="user">Username:</label>
        <div class="col-sm-4">
          <input type="text" class="form-control" id="user" name="user">
         </div>
      </div>
      <div class="form-group">
        <label class="control-label col-sm-2" for="pass">Password:</label>
         <div class="col-sm-4">
          <input type="password" class="form-control" id="pass" name="pass">
         </div>
      </div>
      <div class="form-group">
        <label class="control-label col-sm-2" for="c_pass">Confirm Password:</label>
         <div class="col-sm-4">
          <input type="password" class="form-control" id="c_pass" name="c_pass">
         </div>
      </div>
      <div class="form-group">
         <div class="col-sm-offset-2 col-sm-10">
          <button type="submit" class="btn btn-custom">REGISTER</button>
         </div>
      </div>
    </form>
  </div>

  <div class="col-sm-7" style="margin-left:-220px;">
    <table id="usersTable">
      <thead>
        <th>FIRST NAME</th>
        <th>LAST NAME</th>
        <th>TYPE</th>
      </thead>
      <tbody id="usersTableBody">
        
      </tbody>
    </table>
  </div>

</div>
</div>
</body>
<script>

$(function() {
  $('#addUserForm').submit(function(e){
    e.preventDefault();
    $.ajax({ 
            type:'POST',
            url:'<?php echo base_url('Mpdi_ctrl/registerClientAccount'); ?>',
            data: $(this).serialize(),
            success: function(data){
              var res = JSON.parse(data);
              //alert(res[0]);
              swal("",res[0],res[1].toLowerCase());

              if(res[1]=='Success'){
                $('#addUserForm').trigger("reset");
                loadUserTable();
              }
              
            }      
       });      
  });
});

var t = $("#usersTable").DataTable({"searching": true});

function loadUserTable(){
  $.ajax({ 
            type:'POST',
            url:'<?php echo base_url('Mpdi_ctrl/getListOfUsers'); ?>',
            //data: $(this).serialize(),
            success: function(data){
              var res = JSON.parse(data);
              
              t.clear().draw();
              for(var c=0; c<res.length; c++){
                var col = JSON.parse(res[c]);
                t.row.add([col.fn,col.ln,col.ut]).draw();
              }

            }      
       });
}

loadUserTable();

function search(value){
  t.search(value).draw();
}

</script>
</html>