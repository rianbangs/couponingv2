<script>
    // Define the function to be executed at each interval
    function check_session() 
    {
      
       $.ajax({
                    type:'POST',
                    url: '<?php echo base_url() ?>Cuponing_ctrl/session_check_js',
                    dataType:'JSON',
                    success: function(data)
                    {
                        console.log(data.response); 
                        if(data.response == 'expired')
                        {
                             location.reload();
                        }
                    }
              });
    }

    // setInterval(function() 
    // {
    //     console.log("");
    //        check_session();
    // }, 2000);
</script>