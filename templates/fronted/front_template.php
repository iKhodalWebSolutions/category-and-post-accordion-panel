<?php if ( ! defined( 'ABSPATH' ) ) exit;   $vcode = $this->_config["vcode"];   ?>
<script type='text/javascript' language='javascript'><?php echo $this->apcp_js_obj( $this->_config ); ?></script>
<?php    
$_categories = $this->_config["category_id"];
$_is_rtl_enable = $this->_config["apcp_enable_rtl"];
$apcp_enable_post_count = $this->_config["apcp_enable_post_count"];
$apcp_hide_empty_category = $this->_config["apcp_hide_empty_category"];
$apcp_default_category_open = $this->_config["apcp_default_category_open"];
$apcp_short_category_name_by = $this->_config["apcp_short_category_name_by"];
$apcp_show_all_pane = $this->_config["apcp_show_all_pane"];
$apcp_show_last_updated_post = $this->_config["apcp_show_last_updated_post"]; 
$apcp_enable_sub_category_tree = $this->_config["apcp_enable_sub_category_tree"]; 
$apcp_read_more_link = $this->_config["apcp_read_more_link"]; 
$apcp_hide_paging = $this->_config["apcp_hide_paging"]; 
$apcp_hide_post_image = $this->_config["apcp_hide_post_image"]; 
$apcp_hide_post_short_content = $this->_config["apcp_hide_post_short_content"]; 
$apcp_select_paging_type = $this->_config["apcp_select_paging_type"]; 
$apcp_allow_autoclose_accordion = $this->_config["apcp_allow_autoclose_accordion"]; 
$apcp_hide_post_short_content_length = $this->_config["apcp_hide_post_short_content_length"]; 

$apcp_image_content_width = intval($this->_config["apcp_image_content_width"]);	
$_auto_close_cls = "";
if( trim($apcp_allow_autoclose_accordion) == "yes" ) 
	$_auto_close_cls = "apcp_allow_autoclose_accordion";
	
$apcp_order_category_ids = $this->_config["apcp_order_category_ids"]; 
$apcp_image_height = intval($this->_config["apcp_image_height"]);  
$apcp_shorting_posts_by = $this->_config["apcp_shorting_posts_by"]; 
$apcp_post_ordering_type = $this->_config["apcp_post_ordering_type"];

$apcp_space_margin_between_posts = $this->_config["apcp_space_margin_between_posts"];
$apcp_posts_grid_alignment = $this->_config["apcp_posts_grid_alignment"];
$apcp_posts_loading_effect_on_pagination = $this->_config["apcp_posts_loading_effect_on_pagination"];
$apcp_mouse_hover_effect = $this->_config["apcp_mouse_hover_effect"];
$apcp_show_author_image_and_name = $this->_config["apcp_show_author_image_and_name"]; 
$template = $this->_config["apcp_template"];

 
if( $apcp_short_category_name_by != "id" ) 
	$apcp_order_category_ids = ""; 

$_u_agent = $_SERVER['HTTP_USER_AGENT'];
$_m_browser = '';  
if(strpos($_u_agent,'MSIE')>-1)
	$_m_browser = 'cls-ie-browser';
														
?> 
<div id="richpostaccordion" style="width:<?php echo esc_attr( $this->_config["apcp_widget_width"] ); ?>"  class="<?php echo ((trim($_is_rtl_enable)=="yes")?"apcp-rtl-enabled":""); ?> cls-<?php echo $apcp_posts_grid_alignment; ?>  <?php echo $template; ?>   <?php echo esc_attr($_auto_close_cls); ?>">
	<?php if($this->_config["apcp_hide_widget_title"]=="no"){ ?>
		<div class="ik-pst-tab-title-head" style="background-color:<?php echo esc_attr( $this->_config["header_background_color"] ); ?>;color:<?php echo esc_attr( $this->_config["header_text_color"] ); ?>"  >
			<?php echo esc_html( $this->_config["apcp_widget_title"] ); ?>   
		</div>
	<?php } ?> 
	<span class='wp-load-icon'>
		<img width="18px" height="18px" src="<?php echo apcp_media.'images/loader.gif'; ?>" />
	</span>
	<div class="wea_content <?php echo $_m_browser; ?> lt-grid <?php echo esc_attr( $apcp_select_paging_type ); ?>">
		
		<?php  
			$_category_res = array();
			
			if( trim($_categories)=="0" || trim($_categories) == "" )
				$_category_res = $this->getCategories("",$apcp_order_category_ids);
			else 
				$_category_res = $this->getCategories($_categories,$apcp_order_category_ids); 
			 
			if( count( $_category_res ) > 0 ) {
				
				$_padding_depth = 0;
				$_total_post_count = 0;
				foreach( $_category_res as $_category ) {
					$_category->padding_depth = 13;
					if( $_category->depth > 0 &&  trim($apcp_enable_sub_category_tree) == "yes" ) {
						if($_category->depth==1)
						$_category->padding_depth = $_category->depth * 26; 
						else
						$_category->padding_depth = $_category->depth * 20; 
					} 
					$_total_post_count = $_total_post_count + $_category->count;
				}
				
				if( trim($apcp_show_all_pane) == "yes" ) {
			
					$_category_res_n = array();
					
					if( count( $_category_res ) > 0 ) {
						$_category_res_n[] = (object) array( "category" => __( 'All', 'richpostaccordion' ), "depth" => '0', "padding_depth" => '13', "id" => 'all', "count" => $_total_post_count  );
						foreach( $_category_res as $_category_item ) {
							$_category_res_n[] = $_category_item;
						}
					}
					
					$_category_res = $_category_res_n;
					
				}
			
				foreach( $_category_res as $_category ) {  
				
					$_category_name = $_category->category;
					$_category_id = $_category->id; 
					$_post_count = 0;
					
					if( trim( $apcp_enable_post_count ) == "yes" ||  trim( $apcp_hide_empty_category ) == "yes" ) {
					
						$_post_count = $_category->count;
						
						if( trim( $apcp_hide_empty_category ) == "yes"  && intval( $_post_count ) <= 0 )
							continue;
						
					}

					$_sub_cat_tree = "";
					if((trim($_is_rtl_enable)=="yes")){
						$_sub_cat_tree = "padding-right:".$_category->padding_depth."px";
					}else {
						$_sub_cat_tree = "padding-left:".$_category->padding_depth."px";
					}
					?>
					<div class="item-pst-list">
						<?php
							$_image_width_item = 0;
							if( intval($apcp_image_content_width) > 0 ) {
								$_image_width_item = intval($apcp_image_content_width); 
							}	 
						?>
						<input type="hidden" class="imgwidth" value = "<?php echo $_image_width_item; ?>" />
						<div class="pst-item <?php echo ((( trim( $apcp_default_category_open ) != ""  && ( $apcp_default_category_open ) == $_category_id ))?"pn-active":""); ?>"  onmouseout="apcp_cat_tab_ms_out( this )" onmouseover="apcp_cat_tab_ms_hover( this )" id="<?php echo esc_attr($vcode).'-'.esc_attr($this->cat_replace_dash((($_category_id=="all")?$_categories:esc_js($_category_id )))); ?>" onclick="apcp_fillPosts( this.id, '<?php echo (($_category_id=="all")?esc_js($_categories):esc_js($_category_id )); ?>', request_obj_<?php echo esc_js( $vcode ); ?>, 1 )"  style="color:<?php echo esc_attr($this->_config["category_tab_text_color"] ); ?>;background-color:<?php echo esc_attr( $this->_config["category_tab_background_color"] ); ?>;<?php echo esc_attr($_sub_cat_tree); ?>" >
							<div class="pst-item-text"  onmouseout="apcp_cat_tab_ms_out( this.parentNode )" onmouseover="apcp_cat_tab_ms_hover( this.parentNode )">
								<?php 
							 
									echo esc_html( $_category_name );  
									echo (( trim( $apcp_enable_post_count ) == "yes" )?" (".$_post_count.")":""); 
									 
									if( trim($apcp_show_last_updated_post) == "yes" ) { 
										$post_list = $this->getSqlResult( $_category_id, "", 0, 1, 0, 0, 0, 1 );
										if( count( $post_list ) > 0 ) { 
											echo " - <span class='sc-post-title-date'>(".esc_html( $post_list[0]->post_title ).", ".human_time_diff(strtotime($post_list[0]->post_date))." ".__( 'ago', 'richpostaccordion' ).")</span>";  
										} 
									} 
								?>								
							</div>
							<div class="ld-pst-item-text"></div>
							<div class="clr"></div>
						</div>						
						<div class="item-posts <?php echo $apcp_mouse_hover_effect; ?>"> 
						
							<input type="hidden" class="ikh_templates" value="<?php echo $apcp_posts_grid_alignment; ?>" />
							<input type="hidden" class="ikh_posts_loads_from" value="<?php echo $apcp_posts_loading_effect_on_pagination; ?>" />
							<input type="hidden" class="ikh_border_difference" value="0" />
							<input type="hidden" class="ikh_margin_bottom" value="<?php echo $apcp_space_margin_between_posts; ?>" />
							<input type="hidden" class="ikh_margin_left" value="<?php echo $apcp_space_margin_between_posts; ?>" />
							<input type="hidden" class="ikh_image_height" value="<?php echo $apcp_image_height; ?>" />
							<input type="hidden" class="ikh_item_area_width" value="<?php echo $_image_width_item; ?>" /> 
							<div class="item-posts-wrap">
							<?php
									// Default category opened category start
									if( trim( $apcp_default_category_open ) != "" && trim( $apcp_default_category_open ) == $_category_id ) { 
										 
										 $post_search_text = ""; 
										 $category_id = $apcp_default_category_open;
										 $_limit_start = 0;
										 $_limit_end = $this->_config["apcp_number_of_post_display"];
										 $is_default_category_with_hidden = 0; 
										
										if(trim($apcp_default_category_open) != "all"){
											$__current_term = get_term($apcp_default_category_open);
											$__current_term_count =  $__current_term->count;
										}
										else
										{ 
										   $__current_term_count =  $_total_post_count;
										} 
										
										if( trim($apcp_default_category_open) == "all" )
											$category_id = $_categories;
										
										
										if( $__current_term_count > 0 ) {
											$_category_res = $this->getCategories();
											if( count( $_category_res ) > 0 && !( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'yes' ) ) { 
												?> 
												<div class="ik-post-category"> 
												 
													<?php if( sanitize_text_field( $this->_config["hide_searchbox"] ) == 'no' ) { ?>
														 
														 <input type="text" name="txtSearch" placeholder="<?php echo __( 'Search', 'richpostaccordion' ); ?>" value="<?php echo esc_html( htmlspecialchars( stripslashes( $post_search_text ) ) ); ?>" class="ik-post-search-text"  /> 
													<?php } ?>
													<span  class="ik-search-button" onclick='apcp_fillPosts( "<?php echo esc_js( $this->_config["vcode"]."-".$this->cat_replace_dash($category_id) ); ?>", "<?php echo (($_category_id=="all")?esc_js($_categories):esc_js($_category_id )); ?>", request_obj_<?php echo esc_js( $this->_config["vcode"] ); ?>, 2)'> <img width="18px"  alt="Search" height="18px" src="<?php echo apcp_media.'images/searchicon-3.png'; ?>" />
													</span>
													
													<div class="clrb"></div>
												</div>
											 <?php
											}
										} else { echo "<input type='hidden' value='".$category_id."' class='ik-drp-post-category' />"; }
										$_total_posts =  $__current_term_count; 
										if( $_total_posts > 0 ) {
											$post_list = $this->getSqlResult( $category_id, $post_search_text, 0, $_limit_end ); 
											foreach ( $post_list as $_post ) { 
												$image  = $this->getPostImage( $_post->post_image, $apcp_image_content_width, $apcp_image_height ); 
												$_author_name = esc_html($_post->display_name);
												$_author_image = get_avatar($_post->post_author,25);
												?>
												<div style="width:<?php echo $this->_config["apcp_image_content_width"]; ?>px" class='ikh-post-item-box pid-<?php echo esc_attr( $_post->post_id ); ?>'> 
													<div class="ikh-post-item ikh-simple"> 
													<?php 
													ob_start();
													if( $apcp_hide_post_image == "no" ) { ?> 
														<div  class='ikh-image' >  
															<a href="<?php echo get_permalink( $_post->post_id ); ?>"> 
																<?php echo $image; ?>
															</a>    
														</div>  
													<?php } 
													$_ob_image = ob_get_clean(); 
													
													 
													ob_start();
													?>  
														<div class='ikh-content'> 
															<div class="ikh-content-data">
																<div class='ik-post-name'> 
																	<?php if( sanitize_text_field( $this->_config["hide_post_title"] ) =='no') { ?> 
																		<a href="<?php echo get_permalink( $_post->post_id ); ?>" style="color:<?php echo esc_attr( $this->_config["post_title_color"] ); ?>" >
																			<?php echo esc_html( $_post->post_title ); ?>
																		</a>	
																	<?php } ?>	  
																	<?php if( sanitize_text_field( $this->_config["apcp_hide_posted_date"] ) =='no') { ?> 
																		<div class='ik-post-date'>
																			 <i><?php echo date(get_option("date_format"),strtotime($_post->post_date)); ?></i>
																		</div>
																	<?php } ?>
																	<?php if( $apcp_hide_post_short_content == "no" ) { ?>
																		<div class='ik-post-sub-content'>
																			<?php  				
																			 if( strlen( strip_tags( $_post->post_content ) ) > intval( $apcp_hide_post_short_content_length ) ) 	
																				echo substr( strip_tags( $_post->post_content ), 0, $apcp_hide_post_short_content_length )."..";  
																			 else
																				echo trim( strip_tags( $_post->post_content ) ); 						
																			?> 
																		</div>
																	<?php } ?>
															
																</div> 
															
																<?php if( sanitize_text_field( $this->_config["apcp_hide_comment_count"] ) =='no') { ?> 
																	<div class='ik-post-comment'>
																		<?php 
																			$_total_comments = (get_comment_count($_post->post_id)); 			
																			if($_total_comments["total_comments"] > 0) {
																				echo $_total_comments["total_comments"]; 
																				?> <?php echo (($_total_comments["total_comments"]>1)?__( 'Comments', 'richpostaccordion' ):__( 'Comment', 'richpostaccordion' )); 
																			}
																		?>
																	</div>
																<?php } ?>	  
																 
																<?php if( sanitize_text_field( $this->_config["apcp_show_author_image_and_name"] ) =='yes') { ?> 
																	<div class='ik-post-author'>
																		<?php echo (($_author_image!==FALSE)?$_author_image:"<img src='".apcp_media."images/user-icon.png' width='25' height='25' />"); ?> <?php echo __( 'By', 'richpostaccordion' ); ?> <?php echo $_author_name; ?>
																	</div>
																<?php } ?>	
																
																<?php if( $apcp_read_more_link == "no" ) { ?>
																		<div class="apcp-read-more-link">
																			<a class="lnk-post-content" href="<?php echo get_permalink( $_post->post_id ); ?>" >
																				<?php echo __( 'Read More', 'richpostaccordion' ); ?>
																			</a>
																		</div>
																<?php } ?>  
															</div>	
														</div>	
														
													<?php   
													$_ob_content = ob_get_clean(); 
													
													if($apcp_mouse_hover_effect=='ikh-image-style-40'|| $apcp_mouse_hover_effect=='ikh-image-style-41' ){
														echo $_ob_content;
														echo $_ob_image;
													} else {
														echo $_ob_image;
														echo $_ob_content;														
													}													
													?> 
													<div class="cls1"></div>
													</div> 
												</div> 
												<?php 
											}
											
											if( $apcp_hide_paging == "no" &&  $apcp_select_paging_type == "load_more_option" && $_total_posts > sanitize_text_field( $this->_config["apcp_number_of_post_display"] ) ) { 
											
													?>
													<div class="clr"></div>
													<div class='ik-post-load-more'  align="center" onclick='apcp_loadMorePosts( "<?php echo esc_js( $category_id ); ?>", "<?php echo esc_js( $_limit_start+$_limit_end ); ?>", "<?php echo esc_js( $this->_config["vcode"]."-".$this->cat_replace_dash($category_id) ); ?>", "<?php echo esc_js( $_total_posts ); ?>", request_obj_<?php echo esc_js( $this->_config["vcode"] ); ?> )'>
														<?php echo __('Load More', 'richpostaccordion' ); ?>
													</div>
													<?php   
												 
											} else if( $apcp_hide_paging == "no" &&  $apcp_select_paging_type == "next_and_previous_links" ) { 
											
												 ?><div class="clr"></div>
													<div class="apcp-simple-paging"><?php
													echo $this->displayPagination(  0, $_total_posts, $category_id, $_limit_start, $_limit_end, $this->_config["vcode"], 2 );
													?></div><div class="clr"></div><?php
											
											} else if( $apcp_hide_paging == "no" &&  $apcp_select_paging_type == "simple_numeric_pagination" ) { 
													?><div class="clr"></div>
													<div class="apcp-simple-paging"><?php
													echo $this->displayPagination(  0, $_total_posts, $category_id, $_limit_start, $_limit_end, $this->_config["vcode"], 1 );
													?></div><div class="clr"></div><?php
											} else {
												?><div class="clr"></div><?php
											}
									} else {
									
										?><div class="ik-post-no-items"><?php echo __( 'No posts found.', 'richpostaccordion' ); ?></div><?php 
									
									}	
									
									$this->_config["category_id"] = $category_id;
									?><script type='text/javascript' language='javascript'><?php echo $this->apcp_js_obj( $this->_config ); ?></script><?php
								} 
								// End Default category opened.
							?> 
							</div>
						</div>
						<div class="clr"></div>
					 </div> 
					 <div class="clr"></div>
				   <?php
				   
				}
				
			} 
		?>
		<div class="clr"></div>
	</div>
</div>
