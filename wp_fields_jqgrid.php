<?php
/*
Set functions for generate rows. Constructor function with params will be create in version 0.2.0
*/
	function id(){ ?>
		{name:'id', index:'id', width:30, search:true, searchoptions:{sopt:["eq","ne","lt","le","gt","ge"]}},
	<? }
	
	function post_title(){ ?>
		{name:'post_title', index:'post_title', width:200, search:true, searchoptions:{sopt:["eq","ne","cn","nc"]}},
	<? }
	
	function initiator(){ ?>
		{name:'initiator', index:'initiator', width:50, search:true, searchoptions:{sopt:["eq","ne","cn","nc"]}},
	<? }
	
	function responsible(){ ?>
		{name:'responsible', index:'responsible', width:50, search:true, searchoptions:{sopt:["eq","ne","cn","nc"]}},
	<? }
	
	function date_deadline(){ ?>
		{name:'date_deadline', index:'date_deadline', width:50, align:'center', search:true, searchoptions:{sopt:["eq","ne","cn","nc"]}},
	<? }
	
	function date_end(){ ?>
		{name:'date_end', index:'date_end', width:50, align:'center', search:true, searchoptions:{sopt:["eq","ne","cn","nc"]}},
	<? }
	
	function state(){ ?>
		{name:'state', index:'state', width:50, search: true, searchoptions:{sopt:["eq","ne","cn","nc"]}},
	<? }
	
	function objects(){ ?>
		{name:'objects', index:'objects', width:50, search:true, searchoptions:{sopt:["eq","ne","cn","nc"]}},
	<? }
	
	function functions(){ ?>
		{name:'functions', index:'functions', width:50, search:true, searchoptions:{sopt:["eq","ne","cn","nc"]}},
	<? }
	
	function prioritet(){ ?>
		{name:'prioritet', index:'prioritet', width:30, search:true, searchoptions:{sopt:["eq","ne","lt","le","gt","ge"]}},
	<? }
	
	function post_date(){ ?>
		{name:'post_date', index:'post_date', width:50, align:'center', search:true, searchoptions:{sopt:["eq","ne","cn","nc"]}},
	<? }
	
?>