<?php
	
	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}

	if( isset($_REQUEST['mcpt']) and isset($_REQUEST['mcpt_status']) ){
		$mcpt = $_REQUEST['mcpt'];
		$mcpt_status = $_REQUEST['mcpt_status'];
	?>
		<div class="updated notice">
    		<p><?php esc_html_e($mcpt_status." : ".$mcpt, 'manage-custom-post-types'); ?></p>
		</div>
	<?php
	}

	if( isset($_REQUEST['activate']) || isset($_REQUEST['deactivate']) ){
		if ( ! isset( $_REQUEST['_wpnonce'] ) 
		    || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'mcpt_edit' )
		) {
		?>
		   <div class="error notice">
				<p><?php esc_html_e('Error : Direct access may be harm your content...', 'manage-custom-post-types'); ?></p>
			</div>
		<?php
		} else {
			if(isset($_REQUEST['deactivate'])){
				$mcpt_post = $_REQUEST['deactivate'];
				$mcpt_post_status = 0;
				$mcpt_status = esc_html('Deactivated', 'manage-custom-post-types');
			}elseif(isset($_REQUEST['activate'])){
				$mcpt_post = $_REQUEST['activate'];
				$mcpt_post_status = 1;
				$mcpt_status = 'Activated';
			}
			$mcpt_post_name = 'mcpt_'.$mcpt_post;

			$mcpt_get_posts = maybe_unserialize( get_option( $mcpt_post_name ) );

			$mcpt_current = date("Y/m/d g:i:s a");
			$mcpt_data = array( 
				'mcpt_name' => $mcpt_get_posts['mcpt_name'],
				'mcpt_slug' => $mcpt_get_posts['mcpt_slug'],
				'mcpt_icon' => $mcpt_get_posts['mcpt_icon'],
				'mcpt_status' => $mcpt_post_status,
				'mcpt_modified' => $mcpt_current,
				'mcpt_description' => $mcpt_get_posts['mcpt_description'],
			);

			$mcpt_serialized_data = maybe_serialize( $mcpt_data );

			$temp = update_option( $mcpt_post_name, $mcpt_serialized_data );
			if($temp){
				?>
					<script>
						window.location.href = "<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&mcpt=<?php echo $mcpt_post; ?>&mcpt_status=<?php echo $mcpt_status; ?>";
					</script>
				<?php
			}
			else{
				?>
					<div class="error notice">
		    			<p><?php esc_html_e('Error : In Updating records'); ?></p>
					</div>	
				<?php
			}
		}
	}

	if(isset($_REQUEST['delete'])){
		if ( ! isset( $_REQUEST['_wpnonce'] ) 
		    || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'mcpt_edit' )
		) {
		?>
		   <div class="error notice">
				<p><?php esc_html_e('Error : Direct access may be harm your content...'); ?></p>
			</div>
		<?php
		} else {
			global $wpdb;

			$table = $wpdb->prefix."options";
			$where=array(
				'option_name' => esc_html("mcpt_".$_REQUEST['delete'], 'manage-custom-post-types'),
			);

			if($wpdb->delete($table, $where))
			{
				unregister_post_type( "mcpt_".$_REQUEST['delete'] );
				?>
					<script>
						window.location.href = "<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&mcpt=<?php echo $_REQUEST['delete']; ?>&mcpt_status=Deleted";
					</script>
				<?php
			}	
			else{
				?>
					<div class="error notice">
		    			<p><?php esc_html_e('Error : In Deleting records'); ?></p>
					</div>	
				<?php
			}
		}
	}
/*
	if ( ! isset( $_REQUEST['_wpnonce'] ) 
	    || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'mcpt_manage-post' )
	) {
	?>
	   <div class="error notice">
			<p>Security Check...</p>
		</div>
	<?php
	}
*/
	global $wpdb, $mcpt_temp;

	$result=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mcpt%' ORDER BY option_name ASC");

	$total_rec = $wpdb->num_rows;

//	print_r($result[0]->option_name);

	$enable=0;
	$disable=0;

	for($i=0; $i<$total_rec; $i++){
		$mcpt_temp_id[$i] = $result[$i]->option_id;
		$mcpt_temp[$i] = maybe_unserialize( get_option($result[$i]->option_name) );
		if($mcpt_temp[$i]['mcpt_status']==1){
			$mcpt_temp_enable[$enable] = $mcpt_temp[$i];
			$mcpt_temp_enable_id[$enable] = $result[$i]->option_id;
			$enable++;
		}else{
			$mcpt_temp_disable[$disable] = $mcpt_temp[$i];
			$mcpt_temp_disable_id[$disable] = $result[$i]->option_id;
			$disable++;
		}
	}

	$mcpt_nonce_field = wp_create_nonce( 'mcpt_edit' );
?>

	<div class="wrap">
		<h1 class="wp-heading-inline">Custom Post Types</h1>
		<a href="<?php echo admin_url(); ?>admin.php?page=mcpt_add_new_post_types&_wpnonce=<?php echo $mcpt_nonce_field; ?>" class="page-title-action">Add New</a>
		<hr class="wp-header-end">

		<h2 class="screen-reader-text">Filter posts list</h2>
		
		<form id="posts-filter" method="get">
			<div class="tablenav top">
				<ul class="subsubsub">
					<?php 
						if($total_rec>0){
					?>			
						<li class="all">
							<?php
								if( isset($_REQUEST['mcpt_stat']) and $_REQUEST['mcpt_stat']=='all' or !isset($_REQUEST['mcpt_stat'])){ 
							?>
								<a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&mcpt_stat=all" class="current">
							<?php } else { ?>
								<a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&mcpt_stat=all">
							<?php } ?>
								All <?php echo "(".$total_rec.")"; ?> |
							</a>
						</li>
					<?php
							if($enable>0){
					?>
						<li class="publish">
							<?php
								if( isset($_REQUEST['mcpt_stat']) and $_REQUEST['mcpt_stat']=='enable' ){ 
									$mcpt_temp = $mcpt_temp_enable;
									$mcpt_temp_id = $mcpt_temp_enable_id;
							?>
								<a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&mcpt_stat=enable" class="current">
							<?php } else { ?>
								<a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&mcpt_stat=enable">
							<?php } ?>
								Enable <?php echo "(".$enable.")"; ?> |
							</a>
						</li>
					<?php
							}
							if($disable>0){
					?>
						<li class="publish">
							<?php
								if( isset($_REQUEST['mcpt_stat']) and $_REQUEST['mcpt_stat']=='disable'){ 
									$mcpt_temp = $mcpt_temp_disable;
									$mcpt_temp_id = $mcpt_temp_disable_id;
							?>
								<a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&mcpt_stat=disable" class="current">
							<?php } else { ?>
								<a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&mcpt_stat=disable">
							<?php } ?>
								Disable <?php echo "(".$disable.")"; ?>
							</a>
						</li>
					<?php
							}
						}else{
					?>
						<li class="all">All <?php echo "(0)"; ?></li>
					<?php
						}
					?>
				</ul>
				<!--
				<div class="alignleft actions bulkactions">
					<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
					<select name="action" id="bulk-action-selector-top">
						<option value="-1">Bulk Actions</option>
						<option value="0">Activate</option>
						<option value="1">Deactivate</option>
						<option value="2">Delete</option>
					</select>
					<input id="doaction" class="button action" value="Apply" type="submit" name="mcpt_do-action">
				</div>
				-->
				<div class="tablenav-pages one-page">
					<span class="displaying-num"><?php esc_html_e($total_rec." item", 'manage-custom-post-types'); ?> </span>
				</div>
				<br class="clear">
			</div>

			<h2 class="screen-reader-text">Posts list</h2>
				<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<tr>
						<td id="cb" class="manage-column column-cb check-column">
							<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
							<!--<input id="cb-select-all-1" type="checkbox">-->
						</td>
						<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
							<input type="hidden" name="mcpt_hidden_id" value="<?php echo $mcpt_temp_id[$i]; ?>">
							<?php
								$mcpt_name_order = 'asc';
								if( isset($_REQUEST['order']) and $_REQUEST['order']=='asc')	{	$mcpt_name_order = "desc";	}
								if(  isset($_REQUEST['order']) and $_REQUEST['order']=='desc')	{	$mcpt_name_order = 'asc';	}
							?>
							<a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&orderby=name&amp;order=<?php echo $mcpt_name_order; ?>">
								<span>Name</span>
								<?php
									$mcpt_name_order = 'asc';
									if(  isset($_REQUEST['order']) and $_REQUEST['order']=='asc')	{	echo '<span class="dashicons dashicons-arrow-up">';	}
									if(  isset($_REQUEST['order']) and $_REQUEST['order']=='desc')	{	echo '<span class="dashicons dashicons-arrow-down">';	}
								?></span>
							</a>
						</th>
						<th scope="col" id="author" class="manage-column column-author"><?php esc_html_e('Slug', 'manage-custom-post-types'); ?></th>
						<th scope="col" id="tags" class="manage-column column-tags"><?php esc_html_e('Count', 'manage-custom-post-types'); ?></th>
						<th scope="col" id="date" class="manage-column column-date sortable asc"><span><?php esc_html_e('Last Modified', 'manage-custom-post-types'); ?></span></th>
					</tr>
				</thead>
				<tbody id="the-list">
				<?php
				if(count($mcpt_temp)>0){
					if(  isset($_REQUEST['order']) and $_REQUEST['order']=='asc'){	sort($mcpt_temp);	}
					if(  isset($_REQUEST['order']) and $_REQUEST['order']=='desc'){	rsort($mcpt_temp);	}
					for($i=0; $i<count($mcpt_temp); $i++){

					$start  = date_create($mcpt_temp[$i]['mcpt_modified']);
					$temp 	= date("Y/m/d g:i:s a");
					$end 	= date_create($temp); // Current time and date
					$diff  	= date_diff( $start, $end );
	
				?>
					<tr id="post-<?php echo ($i+1);?>" class="iedit author-self level-0 post-<?php echo ($i+1);?> type-post status-publish format-standard hentry category-uncategorized">
						<th scope="row" class="check-column">
							<label class="screen-reader-text" for="cb-select-<?php echo ($i+1);?>">Select <?php esc_html_e($mcpt_temp[$i]['mcpt_name'], 'manage-custom-post-types'); ?></label>
							<!--<input id="cb-select-<?php echo ($i+1);?>" name="post[]" value="1" type="checkbox">-->
							<div class="locked-indicator">
								<span class="locked-indicator-icon" aria-hidden="true"></span>
								<span class="screen-reader-text">“<?php echo $mcpt_temp[$i]['mcpt_name']; ?>” is locked</span>
							</div>
						</th>
						<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
							<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
							<strong><a class="row-title" href="<?php echo admin_url(); ?>admin.php?page=mcpt_add_new_post_types&edit=<?php echo $mcpt_temp_id[$i]; ?>&_wpnonce=<?php echo $mcpt_nonce_field; ?>" aria-label="“<?php echo $mcpt_temp[$i]['mcpt_name'];?>” (Edit)"><span class="dashicons <?php echo $mcpt_temp[$i]['mcpt_icon']; ?>"></span>&nbsp;&nbsp;<?php esc_html_e($mcpt_temp[$i]['mcpt_name'],'manage-custom-post-types'); ?></a></strong>

<div class="hidden" id="inline_<?php echo ($i+1);?>">
	<div class="tags_input" id="post_tag_<?php echo ($i+1);?>"></div><div class="sticky"></div><div class="post_format"></div>
</div>
<div class="row-actions">
	<?php
		if($mcpt_temp[$i]['mcpt_status'] == 1){
	?>
	<span class="status"><a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&deactivate=<?php echo $mcpt_temp[$i]['mcpt_slug']; ?>&_wpnonce=<?php echo $mcpt_nonce_field; ?>" aria-label="Edit “<?php $mcpt_temp[$i]['mcpt_slug']; ?>”"><?php esc_html_e('Deactivate','manage-custom-post-types'); ?></a> | </span>
	<?php
		}else{
	?>
	<span class="status"><a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&activate=<?php echo $mcpt_temp[$i]['mcpt_slug']; ?>&_wpnonce=<?php echo $mcpt_nonce_field; ?>" aria-label="Edit “<?php $mcpt_temp[$i]['mcpt_slug']; ?>”"><?php esc_html_e('Activate','manage-custom-post-types'); ?></a> | </span>
	<?php
		}
	?>
	<span class="edit"><a href="<?php echo admin_url(); ?>admin.php?page=mcpt_add_new_post_types&edit=<?php echo $mcpt_temp_id[$i]; ?>&_wpnonce=<?php echo $mcpt_nonce_field; ?>" aria-label="Edit “<?php echo $mcpt_temp[$i]['mcpt_name']; ?>”"><?php esc_html_e('Edit', 'manage-custom-post-types'); ?></a> | </span><span class="trash"><a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&delete=<?php echo $mcpt_temp[$i]['mcpt_slug']; ?>&_wpnonce=<?php echo $mcpt_nonce_field; ?>" class="submitdelete" aria-label="Move “Hello world!” to the Trash"><?php esc_html_e('Delete', 'manage-custom-post-types'); ?></a> | </span><span class="view"><a href="<?php echo admin_url(); ?>edit.php?post_type=<?php echo $mcpt_temp[$i]['mcpt_slug']; ?>"><?php esc_html_e('View', 'manage-custom-post-types'); ?></a></span>
</div>
<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>

						</td>
						<td class="slug column-author" data-colname="Slug">
							<a href="<?php echo admin_url(); ?>edit.php?post_type=<?php echo $mcpt_temp[$i]['mcpt_slug']; ?>"><?php echo $mcpt_temp[$i]['mcpt_slug']; ?></a>
						</td>
						<td class="count column-tags" data-colname="Count">
							<?php
								if($mcpt_temp[$i]['mcpt_status'] == 1){
									$mcpt_count_temp = wp_count_posts( $mcpt_temp[$i]['mcpt_slug'] );
									if($mcpt_count_temp->publish > 0){
										$mcpt_post_count = esc_html($mcpt_count_temp->publish, 'manage-custom-post-types');
									} else {
										$mcpt_post_count = esc_html('—', 'manage-custom-post-types');
									}
								} else {
									$mcpt_post_count = esc_html('—', 'manage-custom-post-types');
								}
							?>
							<span aria-hidden="true">&nbsp;&nbsp;&nbsp;<b><?php echo $mcpt_post_count; ?></b></span>
						</td>
						<td class="date column-date" data-colname="Date"><abbr title="<?php echo $mcpt_temp[$i]['mcpt_modified']; ?>">
							<?php 
								if($diff->y > 0){	esc_html_e($diff->y." Years ago", 'manage-custom-post-types');	}
								elseif($diff->m > 0){	esc_html_e($diff->m." Months ago", 'manage-custom-post-types');	}
								elseif($diff->d > 0){	esc_html_e($diff->d." Days ago", 'manage-custom-post-types');	}
								elseif($diff->h > 0){	esc_html_e($diff->h." Hours ago", 'manage-custom-post-types');	}
								elseif($diff->i > 0){	esc_html_e($diff->i." Minutes ago", 'manage-custom-post-types');	}
								elseif($diff->s > 0){	esc_html_e($diff->s." Seconds ago", 'manage-custom-post-types');	}
								else{	echo "<span aria-hidden='true'>—</span>";	}
							?>
						</abbr></td>
					</tr>
				<?php
					}
				}
				else{
					echo '<tr class="no-items"><td class="colspanchange" colspan="4">Not found</td></tr>';
				}
				?>
				</tbody>
				<tfoot>
					<tr>
						<td class="manage-column column-cb check-column">
							<label class="screen-reader-text" for="cb-select-all-2">Select All</label>
							<!--<input id="cb-select-all-2" type="checkbox">-->
						</td>
						<th scope="col" class="manage-column column-title column-primary sortable desc">
							<?php
								$mcpt_name_order = 'asc';
								if( isset($_REQUEST['order']) and $_REQUEST['order']=='asc')	{	$mcpt_name_order = "desc";	}
								if( isset($_REQUEST['order']) and $_REQUEST['order']=='desc')	{	$mcpt_name_order = 'asc';	}
							?>
							<a href="<?php echo admin_url(); ?>admin.php?page=mcpt_manage_post_types&orderby=name&amp;order=<?php echo $mcpt_name_order; ?>">
								<span>Name</span>
								<?php
									$mcpt_name_order = 'asc';
									if( isset($_REQUEST['order']) and $_REQUEST['order']=='asc')	{	echo '<span class="dashicons dashicons-arrow-up">';	}
									if( isset($_REQUEST['order']) and $_REQUEST['order']=='desc')	{	echo '<span class="dashicons dashicons-arrow-down">';	}
								?></span>
							</a>
						</th>
						<th scope="col" class="manage-column column-author"><?php esc_html_e('Slug', 'manage-custom-post-types'); ?></th>
						<th scope="col" class="manage-column column-tags"><?php esc_html_e('Count', 'manage-custom-post-types'); ?></th>
						<th scope="col" class="manage-column column-date sortable asc"><span><?php esc_html_e('Last Modified', 'manage-custom-post-types'); ?> </span></th>
					</tr>
				</tfoot>
			</table>
			<div class="tablenav bottom">
				<div class="alignleft actions"></div>
				<div class="tablenav-pages one-page"><span class="displaying-num"><?php esc_html_e($total_rec." item", 'manage-custom-post-types'); ?> </span></div>
				<br class="clear">
			</div>
		</form>

		<div id="ajax-response"></div>
		<br class="clear">
	</div>
	<div class="clear"></div>