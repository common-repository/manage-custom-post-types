<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//echo "<center>";
//echo date("Y/m/d g:i:s a");
//exit;

	if(isset($_POST['mcpt-post-update'])){
		if ( ! isset( $_REQUEST['_wpnonce'] ) 
		    || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'mcpt_add-new' )
		) {
		?>
		   <div class="error notice">
				<p>Error : Direct access may be harm your content...</p>
			</div>
		<?php
		} else {
			$mcpt_post_name = 'mcpt_'.$_POST['mcpt_edit_slug'];

			$mcpt_exists_check = get_option( $mcpt_post_name );

			if($mcpt_exists_check){
				$mcpt_current = date("Y/m/d g:i:s a");
				$mcpt_data = array( 
					'mcpt_name' => sanitize_text_field($_POST['mcpt-post-name']),
					'mcpt_slug' => sanitize_text_field($_POST['mcpt-post-slug']),
					'mcpt_icon' => sanitize_text_field($_POST['mcpt-post-icon']),
					'mcpt_status' => sanitize_text_field('1'),
					'mcpt_modified' => sanitize_text_field($mcpt_current),
					'mcpt_description' => sanitize_text_field($_POST['mcpt-post-description']),
				);

				$mcpt_serialized_data = maybe_serialize( $mcpt_data );

				$temp = update_option( $mcpt_post_name, $mcpt_serialized_data );
				if($temp){
					global $wpdb;
					$mcpt_nonce_field = wp_create_nonce( 'mcpt_manage-post' );

					$table = $wpdb->prefix."options";
					$data=array(
					'option_name' => 'mcpt_'.$_POST['mcpt-post-slug']
					);
					$where=array(
					'option_id' => $_POST['mcpt_edit_id']
					);
					$format = null;
					$where_format = null;

					$wpdb->update($table, $data, $where, $format, $where_format);
					$header_url = admin_url()."admin.php?page=mcpt_manage_post_types&mcpt=".$_POST['mcpt-post-name']."&mcpt_status=Updated";
			?>
					<script>
						window.location.href = "<?php echo $header_url; ?>";
					</script>
			<?php
				}else{
			?>
					<div class="error notice">
		    			<p>Error : In Updating records</p>
					</div>
			<?php
				}
			}else{
			?>
				<div class="error notice">
	    			<p>Error : In Updating records</p>
				</div>
			<?php
			}
		}
	}

	if(isset($_REQUEST['edit'])){
		if ( ! isset( $_REQUEST['_wpnonce'] ) 
		    || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'mcpt_edit' )
		) {
		?>
		   	<div class="error notice">
				<p>Error : Direct access may be harm your content...</p>
			</div>
		<?php
			unset($_REQUEST['edit']);
		} else {
			global $wpdb;

			$mcpt_temp_id = $_REQUEST['edit'];
			$result=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options WHERE option_id = '".$mcpt_temp_id."' ORDER BY option_name ASC");

			$mcpt_temp = maybe_unserialize( get_option($result[0]->option_name) );
		}
	}


	if(isset($_POST['mcpt-post-publish']))
	{
		if ( ! isset( $_REQUEST['_wpnonce'] ) 
		    || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'mcpt_add-new' )
		) {
		?>
		   <div class="error notice">
				<p><?php esc_html_e('Error : Direct access may be harm your content...', 'manage-custom-post-types'); ?></p>
			</div>
		<?php
		} else {
			$mcpt_post_name = esc_html('mcpt_'.$_POST['mcpt-post-slug'], 'manage-custom-post-types');

			$mcpt_exists_check = get_option( $mcpt_post_name );

			if(!$mcpt_exists_check and !post_type_exists($_POST['mcpt-post-slug'])){
				$mcpt_current = date("Y/m/d g:i:s a");
				$mcpt_data = array( 
					'mcpt_name' => sanitize_text_field($_POST['mcpt-post-name']),
					'mcpt_slug' => sanitize_text_field($_POST['mcpt-post-slug']),
					'mcpt_icon' => sanitize_text_field($_POST['mcpt-post-icon']),
					'mcpt_status' => sanitize_text_field('1'),
					'mcpt_modified' => sanitize_text_field($mcpt_current),
					'mcpt_description' => sanitize_text_field($_POST['mcpt-post-description']),
				);

				$mcpt_serialized_data = maybe_serialize( $mcpt_data );

				$temp = add_option( $mcpt_post_name, $mcpt_serialized_data, '', 'yes' );
				if($temp == 1){
					$mcpt_nonce_field = wp_create_nonce( 'mcpt_manage-post' );
					$header_url = admin_url()."admin.php?page=mcpt_manage_post_types&mcpt=".$_POST['mcpt-post-name']."&mcpt_status=Addedd&_wpnonce=<?php echo $mcpt_nonce_field; ?>";
			?>
					<script>
						window.location.href = "<?php echo $header_url; ?>";
					</script>
			<?php
				}else{
			?>
					<div class="error notice">
		    			<p><?php esc_html_e('Error : Already Exists...', 'manage-custom-post-types'); ?></p>
					</div>
			<?php
				}
			}else{
			?>
				<div class="error notice">
	    			<p><?php esc_html_e('Error : Already Exists...', 'manage-custom-post-types'); ?></p>
				</div>
			<?php
			}
		}
	}
?>

<div class="wrap">
<h1 class="wp-heading-inline">
	<?php esc_html_e('Add New Custom Post Type','manage-custom-post-types'); ?>
</h1>

<form name="post" action="<?php echo admin_url(); ?>admin.php?page=mcpt_add_new_post_types" method="post" id="post">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content" style="position: relative;">
				<div id="titlediv">
					<?php
						if( isset($_REQUEST['edit']) and $_REQUEST['edit']){
							echo "<input type='hidden' name='mcpt_edit_id' value='".$result[0]->option_id."'>";
							echo "<input type='hidden' name='mcpt_edit_slug' value='".$mcpt_temp['mcpt_slug']."'>";
						}
					?>
					<div id="titlewrap">
						<input name="mcpt-post-name" size="30" value="<?php if( isset($_REQUEST['edit']) and $_REQUEST['edit']){ echo $mcpt_temp['mcpt_name']; } ?>" id="mcpt-post-name" spellcheck="true" autocomplete="off" type="text" placeholder="<?php esc_html_e('Enter Name here','manage-custom-post-types'); ?>" onkeyup="change_slug()" required>
					</div>
					<br/>
					<div id="titlewrap" style="margin-top: 1%;">
						<input name="mcpt-post-slug" size="30" value="<?php if( isset($_REQUEST['edit']) and $_REQUEST['edit']){ echo $mcpt_temp['mcpt_slug']; } ?>" id="mcpt-post-slug" spellcheck="true" autocomplete="off" type="text" placeholder="<?php esc_html_e('Enter Slug here','manage-custom-post-types'); ?>" required>
					</div>
					<br/>
					<div id="titlewrap" style=" margin-top: 1%;">
						<input name="mcpt-post-icon" size="30" value="<?php if( isset($_REQUEST['edit']) and $_REQUEST['edit']){ echo $mcpt_temp['mcpt_icon']; } ?>" id="mcpt-post-icon" spellcheck="true" autocomplete="off" type="text" placeholder="<?php esc_html_e('Enter Icon here','manage-custom-post-types'); ?>" required>&nbsp;
						<?php esc_html_e('e.g. dashicons-books','manage-custom-post-types'); ?>
					</div>
					<div id="titlewrap" style="margin-top: 2%;">
						<textarea class="wp-editor-area" style="width:100%; height: 178px; display: block;" autocomplete="off" cols="40" name="mcpt-post-description" id="mcpt-post-description" aria-hidden="true" placeholder="<?php esc_html_e('Enter Description here','manage-custom-post-types'); ?>"><?php if( isset($_REQUEST['edit']) and $_REQUEST['edit']){ echo $mcpt_temp['mcpt_description']; } ?></textarea>
					</div>						
				</div>
			</div><!-- /post-body-content -->

			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
					<div id="submitdiv" class="postbox ">
						<h2 class="hndle ui-sortable-handle"><span><?php esc_html_e('Publish','manage-custom-post-types'); ?></span></h2>
						<div class="inside">
							<div class="submitbox" id="submitpost">
							<div id="major-publishing-actions">
								<div id="publishing-action">
									<?php wp_nonce_field( 'mcpt_add-new', '_wpnonce'); ?>
									<input name="<?php if( isset($_REQUEST['edit']) and $_REQUEST['edit']){ echo "mcpt-post-update"; } else { echo "mcpt-post-publish"; } ?>" id="<?php if( isset($_REQUEST['edit']) and $_REQUEST['edit']){ echo "mcpt-post-update"; } else { echo "mcpt-post-publish"; } ?>" class="button button-primary button-large" value="<?php if( isset($_REQUEST['edit']) and $_REQUEST['edit']){ esc_html_e('Update','manage-custom-post-types'); } else { esc_html_e('Publish','manage-custom-post-types'); } ?>" type="submit">
								</div>
								<div class="clear"></div>
							</div>
							</div>
						</div>
					</div>
					<div id="formatdiv" class="postbox">
						<h2 class="hndle ui-sortable-handle"><span><?php esc_html_e('Help','manage-custom-post-types'); ?></span></h2>
						<div class="inside">
							<div id="post-formats-select">
								<fieldset>
									<legend class="screen-reader-text"><?php esc_html_e('Help', 'manage-custom-post-types'); ?></legend>
									<span class="dashicons dashicons-info"></span>
									<a href="https://developer.wordpress.org/resource/dashicons/#admin-post">
										<?php esc_html_e('Dashicons List', 'manage-custom-post-types'); ?>
									</a><br>
								</fieldset>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /post-body -->
	</div>
	<br class="clear">
</form>
</div>