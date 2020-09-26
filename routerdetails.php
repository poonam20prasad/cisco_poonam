<?php 

include ("server.php");
$objServer = new Server();
$routers = $objServer->getRouterList(isset($_REQUEST['status'])?$_REQUEST['status']:0);
//$type = $objServer->getRouterTypeList();

//var_dump($routers);die;
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">   
   
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">	
 
     <!-- Site Metas -->
    <title>Router</title>  

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Site CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/custom.css">
	
	<link rel="stylesheet" href="css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="css/responsive.dataTables.min.css">
	<script src="js/modernizr.js"></script> <!-- Modernizr -->

</head>
<body id="page-top">
	
	<hr>
	<div class="titlehead" ><h2> Router LIST </h2></div>	
    <div id="order" class="section bc">
        <div class="container-fluid">
            <div class=" content">
				<table id="example" class="display responsive nowrap content" >
					<thead>
						<tr style="background-color:#ff0034">
							<th>Sapid</th>
							<th>Hostname</th>
							<th>Ip Address</th>
							<th>Mac Address</th>
							<th>TYpe</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					if($routers){
					while ($row = $routers->fetch_assoc()) {
					?>
						<tr>
							<td><?php echo $row['Sapid'];?></td>
							<td><?php echo $row['Hostname'];?></td>
							<td><?php echo $row['Loopback'];?></td>
							<td><?php echo $row['MacAddress'];?></td>
							<td><?php echo $row['Type'];?></td>
							<td><span class="btn btn-primary" data-toggle="modal" data-target="#Modal" data-action="updateRouter" data-sapid="<?php echo $row['Sapid'] ?>" data-hostname="<?php echo $row['Hostname'] ?>" data-macaddress="<?php echo $row['MacAddress'] ?>" data-ipaddress="<?php echo $row['Loopback'] ?>" data-type="<?php echo $row['Type'] ?>" data-rid="<?php echo $row['Rid'] ?>" >Update </span> 
							<span class="btn btn-primary delete"  id="delete<?php echo $row['Rid'];?> " > Delete</span></td>
						</tr>
					<?php 
					} }
					?>
					</tbody>
				</table>
                <div class="block">
					<center><h2 data-toggle="modal" data-target="#Modal" data-action="addRouter" class="btn btn-primary" >Add New Router</h2> </center> 
				</div>
            </div><!-- end row -->
        </div><!-- end container -->
    </div><!-- end section -->
	
	
	
	<div id="Modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Fill Router Details</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
		  <div class="modal-body">
			<!-- Form -->
			<form id="routerForm" name="routerForm" novalidate="novalidate" action="" method="post" enctype="multipart/form-data">
				<input type="hidden" id="action" name="action" value="updateRouter" />
				<input type="hidden" id="rid" name="rid"/>
				<div class="col-md-12">
					<div class="form-group">
						<input class="form-control" id="sapid" name="sapid" placeholder="Your SAP ID (Required)" required="required"  maxlength="18" data-validation-required-message="Please enter a SAP ID."/>
						<p class="help-block text-danger"></p>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<input class="form-control" id="hostname" name="hostname" placeholder="Your HostName (Required)" required="required" maxlength="14" data-validation-required-message="Please enter a HostName."/>
						<p class="help-block text-danger"></p>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<input class="form-control" id="ipaddress" name="ipaddress" placeholder="Your Ip Address (Required)" required="required" maxlength="15" data-validation-required-message="Please enter a Ip Address."/>
						<p class="help-block text-danger"></p>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<input class="form-control" id="macaddress" name="macaddress" placeholder="Your Mac Address (Required)" required="required" maxlength="17" data-validation-required-message="Please enter a Mac Address."/>
						<p class="help-block text-danger"></p>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<select class="" id="type" name="type">
							<option value="AGI">AGI </option>
							<option value="CSS">CSS </option>
						</select>
						<p class="help-block text-danger"></p>
					</div>
				</div>
				
				<div class="col-lg-12 text-center">
					<div id="success"></div>
					<input type="submit" class="sim-btn hvr-rectangle-out" id="submit"  name="submit" />

				</div>
			</form>

		  </div>
	 
		</div>

	  </div>
	</div>
	
    <!-- ALL JS FILES -->
    <script src="js/all.js"></script>
	<!-- Camera Slider -->
	<script src="js/jquery.mobile.customized.min.js"></script>
	<script src="js/jquery.easing.1.3.js"></script> 
	<script src="js/parallaxie.js"></script>
	<script src="js/headline.js"></script>
	<script src="js/owl.carousel.js"></script>
	<script src="js/jquery.nicescroll.min.js"></script>
	<!-- Contact form JavaScript -->
    <script src="js/jqBootstrapValidation.js"></script>
   
	
	<script type="text/javascript" language="javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" language="javascript" src="js/dataTables.responsive.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#example').DataTable({
				 responsive: true
			});
			var table = $('#example').DataTable();
 
			$('#example tbody').on( 'click', 'tr', function () {
				if ( $(this).hasClass('selected') ) {
					$(this).removeClass('selected');
				}
				else {
					table.$('tr.selected').removeClass('selected');
					$(this).addClass('selected');
				}
			} );
			$(".delete").click(function(){
				let a = this.id;
				rid= a.replace("delete", "");
				alert(rid);
				$.ajax({
					type: 'POST',
					url: 'server.php',
					data: { 
						'rid': rid, 
						'status':2,
						'action': 'deleteRouter' // <-- the $ sign in the parameter name seems unusual, I would avoid it
					},
					success: function(msg){
						alert('deleted' + msg);
						table.row('.selected').remove().draw( false );
					}
				});
			});
			
			
			$(".routerForm").click(function(){
				$.ajax({
					type: 'POST',
					url: 'server.php',
					data: { 
						'rid': $('.modal-body #rid').val,
						'sapid': $('.modal-body #sapid').val,
						'hostname': $('.modal-body #hostname').val,
						'ipaddress': $('.modal-body #ipaddress').val,
						'macaddress': $('.modal-body #macaddress').val,
						'type': $('.modal-body #type').val,						
						'action': $('.modal-body #action').val // <-- the $ sign in the parameter name seems unusual, I would avoid it
					},
					success: function(msg){
						alert('deleted' + msg);
						$("#Modal").modal('hide');
						$("#Modal").style.display='hide';
						//table.draw();
					}
				});
			});

			$('#Modal').on('show.bs.modal', function (event) {
				var button = $(event.relatedTarget); // Button that triggered the modal
				var modal = $(this);
				if(button.data('action')=="updateRouter"){
					modal.find('.modal-body #sapid').val(button.data('sapid'));
					modal.find('.modal-body #hostname').val(button.data('hostname'));
					modal.find('.modal-body #ipaddress').val(button.data('ipaddress'));
					modal.find('.modal-body #macaddress').val(button.data('macaddress'));
					modal.find('.modal-body #type').val(button.data('type'));
					modal.find('.modal-body #rid').val(button.data('rid'));
				}
				modal.find('.modal-body #action').val(button.data('action'));
			});
			
			$(document).on('hide.bs.modal','#routerForm', function () {
				location.reload();		
			});
			

		} );
	</script>
   
</body>
</html>