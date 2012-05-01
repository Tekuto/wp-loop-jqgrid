<?php
/*
Set view function. Generate javascript based on fields from params. See field functions in wp_fields_jqgrid.php
*/

include 'wp_fields_jqgrid.php';

function wp_view_jqgrid(){
	global $posttype, $tax_slug, $tax_id, $title, $status, $fields, $fields_name;
	
	if($tax_id == 0){
		if(is_tax($tax_slug)){
			$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
			$tax_id = $term->term_id;
		}
	}
	
?>
<form metod="GET" action="">
<select name="status_form" onchange="this.form.submit();" >
<?php if(isset($_GET['status_form']) AND $_GET['status_form']=='all'){ ?>
<option value="open">Открытые</option>
<option value="all" selected="selected">Все</option> <?php
}else{ ?>
<option value="open" selected="selected">Открытые</option>
<option value="all">Все</option> <?php
} ?>
</select>
</form>
<?php 
if(isset($_GET['status_form'])){
$status = $_GET['status_form'];
}
$ajaxurl = "/wp-admin/admin-ajax.php?action=wp_data_jqgrid&posttype=".$posttype."&tax_slug=".$tax_slug."&tax_id=".$tax_id."&fields=".$fields."&status=".$status; ?>

	<table id="list"><tr><td/></tr></table> 
	<div id="pager"></div>
		
	<script type="text/javascript">
		jQuery().ready(function(){ 
			jQuery("#list").jqGrid({
				url:'<?php echo $ajaxurl; ?>',
				datatype: 'xml',
				colNames:[
				<?php
					$names = explode(',', $fields_name);
					foreach($names as $name){
					$fnames .= "'".$name."',";
					}
					echo $fnames;
				?>
				],
				colModel:[
				<?php	
					$frows = explode(',', $fields);
					foreach($frows as $frow){
						$frow();
					}						
				?>
				],
				pager:jQuery('#pager'),
				mtype:'POST',
				rowNum:30,
				autowidth:true,
				rowList:[15,30,50],
				height: 'auto',
				sortname:'post_date',
				viewrecords:true,
				sortorder: "desc",
				<?php if(isset($title)){ ?>
				caption: '<?php echo $title; ?>',
				<?php } ?>
     	    });
			
			jQuery("#list").jqGrid('filterToolbar');
			
						
		}); 
	</script>
<?php }
?>