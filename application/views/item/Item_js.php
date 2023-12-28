<script>
/*	function search_item()
	{

		//console.log($(".search").val());
		var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
         $('.table_div').html(loader);
		$.ajax({
				   type:'POST', 		
                   url:'<?php echo base_url(); ?>item/search_item',
                   data:{
                   	      'search':$(".search").val()
                   	    },
 				   dataType:'JSON',
 				   success: function(data)
 				   {
 				   	 console.log(data.html);
 				   	 $('.table_div').html(data.html);
 				   }
		       });
	} */

	function item_details(item_no,uom)
	{
		console.log(item_no);
		$.ajax({
					type:'POST',
					url:'<?php echo base_url() ?>item/item_details',
					data:{
						   'item_no':item_no,
						   'uom':uom
						 },
					dataType:'JSON',
					success: function(data)
					{
						$(".item_details_body").html(data.html);
					}	 
		       });
		 $("#item_details").modal("show");   
		 //$(".item_details_body").html(item_no);  
	}


	display_item_table();
	function display_item_table()
	{
		var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
        $('.table_div').html(loader);
		$.ajax({
					type:'POST',
					 url:'<?php echo base_url(); ?>item/display_item',
					 dataType:'JSON',
					 success: function(data)
					 {
					 	$('.table_div').html(data.html);
					 }
		       });
	}





	//user is "finished typing," do something
	function doneTyping ()
	{	
	  //console.log($(".search_item").val());

	  if($(".search_item").val() == '')
	  {
	  		display_item_table();
	  }
	  else 
	  {

	  var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
         $('.table_div').html(loader);
		 $.ajax({
				   type:'POST', 		
                   url:'<?php echo base_url(); ?>item/search_item',
                   data:{
                   	      'search':$(".search_item").val()
                   	    },
 				   dataType:'JSON',
 				   success: function(data)
 				   {
 				   	 console.log(data.html);
 				   	 $('.table_div').html(data.html);
 				   }
		        });
	  }
	  	 
	}


/*	function search(search)
	{


		 var loader  = ' <center><img src="<?php echo base_url(); ?>assets/img/preloader.gif" style="padding-top:120px; padding-bottom:120px;"></center>';
         $('.table_div').html(loader);
		 $.ajax({
				   type:'POST', 		
                   url:'<?php echo base_url(); ?>item/search_item',
                   data:{
                   	      'search':search
                   	    },
 				   dataType:'JSON',
 				   success: function(data)
 				   {
 				   	 console.log(data.html);
 				   	 $('.table_div').html(data.html);
 				   }
		        });
	}*/

</script>