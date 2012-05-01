<?php

function wp_data_jqgrid($params){

	global $wpdb;
	
	$postID = $_POST['postID'];
	$page = $_POST['page'];
	$limit = $_POST['rows'];
	$sidx = $_POST['sidx'];
	$sord = $_POST['sord'];
	$search = $_POST['_search'];
	$posttype = $_GET['posttype'];
	$tax_slug = $_GET['tax_slug'];
	$tax_id = $_GET['tax_id'];
	$status = $_GET['status'];
	$fields = $_GET['fields'];
	
	if(!$sidx) $sidx =1;

/* Get child terms */
	
		$tax_ids = get_categories('child_of='.$tax_id.'&taxonomy='.$tax_slug);
			foreach($tax_ids as $id){
				$t_id .= $id->term_id.',';
			}
		
		$t_id .= $tax_id;

		
/* Count posts and get navigation */

		$count=wp_count_posts($posttype);
		$count= $count->publish;

		if($count > 0 && $limit > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}

		if($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit;

		if($start <0) $start = 0;
		
		
		global $wpdb, $post;
		$qwhere = "";
		$qfield = "";
		$qjoin = "";
		
		if(isset($_POST['_search']) && $_POST['_search'] == 'true'){
			$qval = $_POST['searchString'];
			foreach($_POST as $pkey=>$pvalue){
				switch ($pkey){
					case 'post_title':
						$qwhere .= " AND $wpdb->posts.post_title LIKE '%$pvalue%'";
						break;
					case 'responsible':
					case 'initiator':
					
						$qfield .= " ,".$pkey."fio.ID as f_id ";
						$qjoin  .=  "
							JOIN wp_postmeta ".$pkey."postmeta ON (".$pkey."postmeta.post_id = wp_posts.ID AND ".$pkey."postmeta.meta_key = '$pkey')
							JOIN wp_posts ".$pkey."fio ON (".$pkey."fio.id = ".$pkey."postmeta.meta_value  AND ".$pkey."fio.post_title LIKE '%$pvalue%' ) 
						";
						$qwhere .= " ";
						break;
					case 'date_deadline':
						$qjoin  .=  "
							JOIN wp_postmeta ".$pkey."postmeta ON (".$pkey."postmeta.post_id = wp_posts.ID 
							AND ".$pkey."postmeta.meta_key= 'date_deadline' 
							AND DATE_FORMAT(".$pkey."postmeta.meta_value,'%d\.%m\.%Y') = '$pvalue' )						
						";
						$qwhere .= "";
						break;
					case 'functions2':
						$qjoin  .=  "
							JOIN wp_postmeta ".$pkey."postmeta ON (".$pkey."postmeta.post_id = wp_posts.ID 
							AND ".$pkey."postmeta.meta_key= '$pkey' 
							AND ".$pkey."postmeta.meta_value LIKE '%$pvalue%' )						
						";
						$qwhere .= "";
						break;
				}
			}
		}
		


/* Get data */		

	switch ($sidx)	{
				case 'responsible':
					$query = "SELECT *,
							(SELECT $wpdb->posts.post_title FROM $wpdb->posts WHERE $wpdb->posts.ID IN($wpdb->postmeta.meta_value)) AS 'repe'
							FROM $wpdb->posts
							LEFT JOIN $wpdb->postmeta ON($wpdb->postmeta.meta_key = 'responsible')
							LEFT JOIN $wpdb->term_relationships
								ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
							LEFT JOIN $wpdb->term_taxonomy
								ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
							WHERE $wpdb->posts.post_type = '$posttype'
							AND $wpdb->posts.post_status = 'publish'
							AND term_id IN ($t_id)
							AND taxonomy = '$tax_slug'
							AND $wpdb->posts.ID = $wpdb->postmeta.post_id
							GROUP BY $wpdb->posts.ID
							ORDER BY repe $sord
							LIMIT $start, $limit";
					$SQL = $wpdb->get_results($query);
					break;
				case 'initiator':
					$query = "SELECT *,
							(SELECT $wpdb->posts.post_title FROM $wpdb->posts WHERE $wpdb->posts.ID IN($wpdb->postmeta.meta_value)) AS 'repe'
							FROM $wpdb->posts
							LEFT JOIN $wpdb->postmeta ON($wpdb->postmeta.meta_key = 'initiator')
							LEFT JOIN $wpdb->term_relationships
								ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
							LEFT JOIN $wpdb->term_taxonomy
								ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
							WHERE $wpdb->posts.post_type = '$posttype'
							AND $wpdb->posts.post_status = 'publish'
							AND term_id IN ($t_id)
							AND taxonomy = '$tax_slug'
							AND $wpdb->posts.ID = $wpdb->postmeta.post_id
							GROUP BY $wpdb->posts.ID
							ORDER BY repe $sord
							LIMIT $start, $limit";
					$SQL = $wpdb->get_results($query);
					break;
					
				case 'prioritet':
					$query = "SELECT *
							FROM $wpdb->posts
							LEFT JOIN $wpdb->postmeta ON($wpdb->postmeta.meta_key = 'prioritet')
							LEFT JOIN $wpdb->term_relationships
								ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
							LEFT JOIN $wpdb->term_taxonomy
								ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
							WHERE $wpdb->posts.post_type = '$posttype'
							AND $wpdb->posts.post_status = 'publish'
							AND term_id IN ($t_id)
							AND taxonomy = '$tax_slug'
							AND $wpdb->posts.ID = $wpdb->postmeta.post_id
							GROUP BY $wpdb->posts.ID
							ORDER BY $wpdb->postmeta.meta_value $sord
							LIMIT $start, $limit";
					$SQL = $wpdb->get_results($query);
					break;
				
				case 'date_end':
					$query = "SELECT *
							FROM $wpdb->posts
							LEFT JOIN $wpdb->postmeta ON($wpdb->postmeta.meta_key = 'date_end')
							LEFT JOIN $wpdb->term_relationships
								ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
							LEFT JOIN $wpdb->term_taxonomy
								ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
							WHERE $wpdb->posts.post_type = '$posttype'
							AND $wpdb->posts.post_status = 'publish'
							AND term_id IN ($t_id)
							AND taxonomy = '$tax_slug'
							AND $wpdb->posts.ID = $wpdb->postmeta.post_id
							GROUP BY $wpdb->posts.ID
							ORDER BY $wpdb->postmeta.meta_value $sord
							LIMIT $start, $limit";
					$SQL = $wpdb->get_results($query);
					break;
					
				case 'date_deadline':
					$query = "SELECT *
							FROM $wpdb->posts
							LEFT JOIN $wpdb->postmeta ON($wpdb->postmeta.meta_key = 'date_deadline')
							LEFT JOIN $wpdb->term_relationships
								ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
							LEFT JOIN $wpdb->term_taxonomy
								ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
							WHERE $wpdb->posts.post_type = '$posttype'
							AND $wpdb->posts.post_status = 'publish'
							AND term_id IN ($t_id)
							AND taxonomy = '$tax_slug'
							AND $wpdb->posts.ID = $wpdb->postmeta.post_id
							GROUP BY $wpdb->posts.ID
							ORDER BY $wpdb->postmeta.meta_value $sord
							LIMIT $start, $limit";
					$SQL = $wpdb->get_results($query);
					break;
				
				case 'state':
					$query = "SELECT * 
							FROM $wpdb->posts
							LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id ) 
							LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id ) 
							LEFT JOIN $wpdb->terms ON ( $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id ) 
							WHERE post_status = 'publish'
							AND post_type = '$posttype'
							AND taxonomy = 'state'
							GROUP BY $wpdb->posts.ID
							ORDER BY name $sord 
							LIMIT $start, $limit";
					$SQL = $wpdb->get_results($query);
					break;
					
				case 'functions':
					$query = "SELECT * 
							FROM $wpdb->posts
LEFT JOIN $wpdb->postmeta ON($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key='date_end')
							LEFT JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id ) 
							LEFT JOIN $wpdb->term_taxonomy ON ( $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id ) 
							LEFT JOIN $wpdb->terms ON ( $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id ) 
							WHERE post_status = 'publish'
							AND post_type = '$posttype'";
					if($_GET['tax_id']!=0){
					$query .= "AND term_id IN ($t_id)
							AND taxonomy = '$tax_slug'";
					}

					if($status == 'open'){
						$query .= " AND (meta_value = ''
						OR meta_value IS NULL)";
					}
					$query .= "
							GROUP BY $wpdb->posts.ID
							ORDER BY name $sord 
							LIMIT $start, $limit";
					$SQL = $wpdb->get_results($query);
					break;
					
				default:
					$query = "SELECT $wpdb->posts.* $qfield FROM $wpdb->posts
							$qjoin
							LEFT JOIN $wpdb->postmeta ON($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key='date_end')
							LEFT JOIN $wpdb->term_relationships
								ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
							LEFT JOIN $wpdb->term_taxonomy
								ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
								
							WHERE $wpdb->posts.post_status='publish'
							AND $wpdb->posts.post_type='$posttype' 
							$qwhere
							";
						
					if($_GET['tax_id']!=0){
						$query .= "AND term_id IN ($t_id)
							AND taxonomy = '$tax_slug'";
					}

					if($status == 'open'){
						$query .= " AND ($wpdb->postmeta.meta_value = ''
						OR $wpdb->postmeta.meta_value IS NULL)";
					}

					$query .= "     GROUP BY $wpdb->posts.ID
							ORDER BY $wpdb->posts.$sidx $sord
							LIMIT $start, $limit";
					$SQL = $wpdb->get_results($query);
					
					break;	
	}	
	//die(print_r($query));
	//die(print_r($SQL));
/* Set and get data fields */
	
	$rows = explode(',', $fields);
	/* Start of data */
		
		$s = "<?xml version='1.0' encoding='utf-8'?>";
		$s.=  "<rows>";
		$s.= "<page>".$page."</page>";
		$s.= "<total>".( $_POST['_search'] == "true" ? ceil(count($SQL) / $count) :$total_pages) ."</total>";
		$s.= "<records>" . count($SQL) ."</records>";
		
		foreach($SQL as $item){
		
				//global $wpdb, $post;
				
				$a = 0;
				$search_op = 'cn';
				if(isset($_POST['_search']) && $_POST['_search'] == 'true'){
					$search_op = $_POST['searchOper'];
					$getop_what = $_POST['searchString'];
					switch ($_POST['searchField']){
						case 'id':
							$getop_where = $item->ID;
							break;
						case 'post_title':
							$getop_where = $item->post_title;
							break;
						case 'responsible':
							$re_ps = get_post_meta($item->ID,'responsible', true);
							$rp = explode(',', $re_ps);
							foreach ($rp as $k => $person)
								$rp[$k] = get_the_title($person);
							$responsible_persons = implode(', ', $rp);
							$getop_where = $responsible_persons;
							break;
						
						case 'initiator':
							$init = get_post_meta($item->ID,'initiator', true);
							$in = explode(',', $init);
							foreach ($in as $k => $initiat)
								$in[$k] = get_the_title($initiat);
							$initiator = implode(', ', $in);
							$getop_where = $initiator;
							break;
							
						case 'date_deadline':
							$init = get_post_meta($item->ID,'date_deadline', true);
							$getop_where = $init;
							break;
						
						case 'date_end':
							$init = get_post_meta($item->ID,'date_end', true);
							$getop_where = $init;
							break;
						
						case 'prioritet':
							$prt = get_post_meta($item->ID,'prioritet', true);
							$pr = explode(',', $prt);
							foreach ($pr as $k => $rior)
								$pr[$k] = get_the_title($prior);
							$prioritet = implode(', ', $pr);
							$getop_where = $prioritet;
							break;
						case 'functions':
							$category_terms = get_the_terms($item->ID,'functions');
							
							if(is_array($category_terms)){
								unset($cats);
								foreach ($category_terms as $k => $category)
									$cats[$k] = $category->name;
								$categories = implode(', ', $cats);
							}
							else unset($categories);
							$getop_where = $categories;
							break;
							
						case 'state':
							$category_terms = get_the_terms($item->ID,'state');
							if(is_array($category_terms)){
								unset($cats);
								foreach ($category_terms as $k => $category)
									$cats[$k] = $category->name;
								$categories = implode(', ', $cats);
							}
							else unset($categories);
							$getop_where = $categories;
							break;
						
						case 'post_date':
							$getop_where = $item->post_date;
							break;
					}
				}

				if (isset($_POST['id'])){
					$getop_what = $_POST['id'];
					$getop_where = $item->ID;
				}

				if (isset($_POST['post_title'])){
					$getop_what = $_POST['post_title'];
					$getop_where = $item->post_title;
				}

				if (isset($_POST['responsible'])){
					$re_ps = get_post_meta($item->ID,'responsible', true);
					$rp = explode(',', $re_ps);
					foreach ($rp as $k => $person)
						$rp[$k] = get_the_title($person);
					$responsible_persons = implode(', ', $rp);
					$getop_what = $_POST['responsible'];
					$getop_where = $responsible_persons;
				}
				
				if (isset($_POST['initiator'])){
					$init = get_post_meta($item->ID,'initiator', true);
					$in = explode(',', $init);
					foreach ($in as $k => $initiat)
						$in[$k] = get_the_title($initiat);
					$initiator = implode(', ', $in);
					$getop_what = $_POST['initiator'];
					$getop_where = $initiator;
				}
				
				if (isset($_POST['date_deadline'])){
					$init = get_post_meta($item->ID,'date_deadline', true);
					$getop_what = $_POST['date_deadline'];
					$getop_where = $init;
				}
				
				if (isset($_POST['date_end'])){
					$init = get_post_meta($item->ID,'date_end', true);
					$getop_what = $_POST['date_end'];
					$getop_where = $init;
				}

				if (isset($_POST['prioritet'])){
					$prt = get_post_meta($item->ID,'prioritet', true);
					$pr = explode(',', $prt);
					foreach ($pr as $k => $rior)
						$pr[$k] = get_the_title($prior);
					$prioritet = implode(', ', $pr);
					$getop_what = $_POST['prioritet'];
					$getop_where = $prioritet;
				}

				if (isset($_POST['functions'])){
					$category_terms = get_the_terms($item->ID,'functions');
					if(is_array($category_terms)){
						unset($cats);
						foreach ($category_terms as $k => $category)
							$cats[$k] = $category->name;
						$categories = implode(', ', $cats);
					}
					else unset($categories);
					$getop_what = $_POST['functions'];
					$getop_where = $categories;
				}
				
				if (isset($_POST['state'])){
					$category_terms = get_the_terms($item->ID,'state');
					if(is_array($category_terms)){
						unset($cats);
						foreach ($category_terms as $k => $category)
							$cats[$k] = $category->name;
						$categories = implode(', ', $cats);
					}
					else unset($categories);
					$getop_what = $_POST['state'];
					$getop_where = $categories;
				}

				if (isset($_POST['post_date'])){
					$getop_what = $_POST['post_date'];
					$getop_where = $item->post_date;
				}

				if($getop_where || $getop_what){
					$search_op = (!$search_op) ? 'cn' : $search_op;
					$g = get_op($search_op, $getop_what, $getop_where);
					if(!$g) $a = 1;
				}

				if ($a == 1)
					continue;
				
		
				$id = '<cell><![CDATA[<a href="'.$item->guid.'">' . $item->ID . '</a>]]></cell>';
				
				$post_title = '<cell><![CDATA[<a href="' .$item->guid. '">' . $item->post_title . '</a>]]></cell>';
				
				
				$init = get_post_meta($item->ID,'initiator', true);
				$in = explode(',', $init);
				foreach ($in as $k => $initiat)
					$in[$k] = get_the_title($initiat);
				$initiators = implode(', ', $in);
				$initiator = '<cell>' . $initiators . '</cell>';
				
				$re_ps = get_post_meta($item->ID,'responsible', true);
				$rp = explode(',', $re_ps);
				foreach ($rp as $k => $person)
					$rp[$k] = get_the_title($person);
				$responsible_persons = implode(', ', $rp);
				$responsible = '<cell>' . $responsible_persons . '</cell>';
				
				$date_deadline = '<cell>' . substr(get_field('date_deadline',$item->ID),0,11) . '</cell>';
				
				$date_end = '<cell>' . substr(get_field('date_end',$item->ID),0,11) . '</cell>';
				
				$state_terms = get_the_terms($item->ID,'state');
				if(is_array($state_terms)){
					unset($status);
					foreach ($state_terms as $k => $state)
						$status[$k] = $state->name;
					$states = implode(', ', $status);
				}
				else unset($states);
				$state = '<cell>' . $states . '</cell>';
				
				$obj = implode(',',get_post_meta($item->ID,'object'));
				$massiv = explode(',', $obj);
					for( $i=0; $i<count($massiv); $i++){
						if($i>0){$s.= ', ';}
						$objs= get_the_title($massiv[$i]);
					}
				$objects ='<cell>'. $objs .'</cell>';
				
				$category_terms = get_the_terms($item->ID,$tax_slug);
				if(is_array($category_terms)){
					unset($cats);
					foreach ($category_terms as $k => $category)
						$cats[$k] = $category->name;
					$categories = implode(', ', $cats);
				}
				else unset($categories);
				$functions = '<cell>' . $categories . '</cell>';
				
				$pri = get_post_meta($item->ID,'prioritet');
				if (is_array($pri))
					$pri = implode(',', $pri);
				$prioritet = '<cell>' . $pri . '</cell>';
				
				$post_date = '<cell>' . $item->post_date . '</cell>';
				
				$s.= '<row id="' . $item->ID . '">';
					foreach($rows as $row){
						$s .= $$row;
					}
				$s.= '</row>';
	} 
		$s .= '</rows>';

	header("Content-type: text/xml;charset=utf-8");

	echo $s;

	exit;
}

function get_op($op, $what, $where)
{
	//$text = mysql_escape_string($text);
	$what = mysql_real_escape_string(mb_strtolower($what));
	$where = mysql_real_escape_string(mb_strtolower($where));
	switch ($op)
	{
		case 'eq':
			$result = ($what == $where);
			break;
		case 'ne':
			$result = ($what != $where);
			break;
		case 'lt':
			$result = ($what > $where);
			break;
		case 'le':
			$result = ($what >= $where);
			break;
		case 'gt':
			$result = ($what < $where);
			break;
		case 'ge':
			$result = ($what <= $where);
			break;
		case 'cn':
			$result = stristr($where, $what);
			break;
		case 'nc':
			$result = !stristr($where, $what);
			break;
		default:
			$result = '';
	}
	return $result;
}

?>