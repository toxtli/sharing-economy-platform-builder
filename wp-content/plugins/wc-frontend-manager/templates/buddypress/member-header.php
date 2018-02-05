<?php

	//bp_load_theme_functions();

	global $bp;
	
	$bp->displayed_user->id = get_current_user_id();
	$bp->displayed_user->userdata = wp_get_current_user();
	$bp->displayed_user->domain = $bp->loggedin_user->domain;
	
	// Adding Menu Item
	$pages = get_option("wcfm_page_options");
	$wcfm_page = get_post( $pages['wc_frontend_manager_page_id'] );
	
	$args = array(
					'name' => $wcfm_page->post_title,
					'slug' => $wcfm_page->post_name,
					'default_subnav_slug' => $wcfm_page->post_name,
					'position' => 50,
					'screen_function' => 'bp_wcfm_user_nav_item_screen',
					'item_css_id' => $wcfm_page->post_name
	);

	bp_core_new_nav_item( $args );
	
	// Avatar height - padding - 1/2 avatar height.
	$top_offset    = 150;
	$avatar_height = apply_filters( 'bp_core_avatar_full_height', $top_offset );
	
	if ( $avatar_height > $top_offset ) {
		$top_offset = $avatar_height;
	}
	
	$avatar_offset = $avatar_height - 5; // - round( (int) bp_core_avatar_full_height() / 2 );

	// Header content offset + spacing.
	$top_offset  = bp_core_avatar_full_height() - 10;
	$left_offset = bp_core_avatar_full_width() + 20;
	
	$params["height"] = $top_offset + round( $avatar_height / 2 );
	
	$params['cover_image'] = bp_attachments_get_attachment('url', array(
																																			'object_dir' => 'members',
																																			'item_id' => $bp->displayed_user->id,
																																		));

	$cover_image = ( !empty( $params['cover_image'] ) ) ? 'background-image: url(' . $params['cover_image'] . ');' : '';

	$hide_avatar_style = '';

	// Adjust the cover image header, in case avatars are completely disabled.
	if ( ! buddypress()->avatar->show_avatars ) {
		$hide_avatar_style = '
			#wcfm-main-content #item-header-cover-image #item-header-avatar {
				display:  none;
			}
		';

		if ( bp_is_user() ) {
			$hide_avatar_style = '
				#wcfm-main-content #item-header-cover-image #item-header-avatar a {
					display: block;
					height: ' . $top_offset . 'px;
					margin: 0 15px 19px 0;
				}

				#wcfm-main-content div#item-header #item-header-cover-image #item-header-content {
					margin-left: auto;
				}
			';
		}
	}
?>

  <div id="buddypress">
  
		<div id="item-header">
	
			<div id="cover-image-container">
				<a id="header-cover-image" href="<?php echo bp_displayed_user_domain(); ?>"></a>
	
				<div id="item-header-cover-image">
					<div id="item-header-avatar">
						<a href="<?php echo bp_displayed_user_domain(); ?>">
			
							<?php bp_displayed_user_avatar( 'type=full' ); ?>
			
						</a>
					</div><!-- #item-header-avatar -->
			
					<div id="item-header-content">
			
						<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
							<h2 class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></h2>
						<?php endif; ?>
			
						<div id="item-buttons"><?php
			
							/**
							 * Fires in the member header actions section.
							 *
							 * @since 1.2.6
							 */
							do_action( 'bp_member_header_actions' ); ?></div><!-- #item-buttons -->
			
						<span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_user_last_activity( bp_displayed_user_id() ) ); ?>"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>
			
						<?php
			
						/**
						 * Fires before the display of the member's header meta.
						 *
						 * @since 1.2.0
						 */
						do_action( 'bp_before_member_header_meta' ); ?>
			
						<div id="item-meta">
			
							<?php if ( bp_is_active( 'activity' ) ) : ?>
			
								<div id="latest-update">
			
									<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>
			
								</div>
			
							<?php endif; ?>
			
							<?php
			
							 /**
								* Fires after the group header actions section.
								*
								* If you'd like to show specific profile fields here use:
								* bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
								*
								* @since 1.2.0
								*/
							 do_action( 'bp_profile_header_meta' );
			
							 ?>
			
						</div><!-- #item-meta -->
			
					</div><!-- #item-header-content -->
			
				</div><!-- #item-header-cover-image -->
			</div><!-- #cover-image-container -->
	
		</div><!-- #item-header -->
	
		<div id="item-nav">
			<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
				<ul>
	
					<?php bp_get_displayed_user_nav(); ?>
	
					<?php do_action( 'bp_member_options_nav' ); ?>
	
				</ul>
			</div>
		</div><!-- #item-nav -->
	</div>
	<div class="wcfm-clearfix"></div><br />
	
  <?php
	echo '<style>
		/* Cover image */
		#wcfm-main-content #header-cover-image {
			height: ' . $params["height"] . 'px;
			' . $cover_image . '
		}

		#wcfm-main-content #create-group-form #header-cover-image {
			margin: 1em 0;
			position: relative;
		}

		.bp-user #wcfm-main-content #item-header {
			padding-top: 0;
		}

		#wcfm-main-content #item-header-cover-image #item-header-avatar {
			margin-top: '. $avatar_offset .'px;
			float: left;
			overflow: visible;
			width: auto;
		}

		#wcfm-main-content div#item-header #item-header-cover-image #item-header-content {
			clear: both;
			float: left;
			margin-left: ' . $left_offset . 'px;
			margin-top: -' . $top_offset . 'px;
			width: auto;
		}

		body.single-item.groups #wcfm-main-content div#item-header #item-header-cover-image #item-header-content,
		body.single-item.groups #wcfm-main-content div#item-header #item-header-cover-image #item-actions {
			clear: none;
			margin-top: ' . $params["height"] . 'px;
			margin-left: 0;
			max-width: 50%;
		}

		body.single-item.groups #wcfm-main-content div#item-header #item-header-cover-image #item-actions {
			max-width: 20%;
			padding-top: 20px;
		}

		' . $hide_avatar_style . '

		#wcfm-main-content div#item-header-cover-image .user-nicename a,
		#wcfm-main-content div#item-header-cover-image .user-nicename {
			font-size: 200%;
			color: #fff;
			margin: 0 0 0.6em;
			text-rendering: optimizelegibility;
			text-shadow: 0 0 3px rgba( 0, 0, 0, 0.8 );
		}

		#wcfm-main-content #item-header-cover-image #item-header-avatar img.avatar {
			background: rgba( 255, 255, 255, 0.8 );
			border: solid 2px #fff;
		}

		#wcfm-main-content #item-header-cover-image #item-header-avatar a {
			border: 0;
			text-decoration: none;
		}

		#wcfm-main-content #item-header-cover-image #item-buttons {
			margin: 0 0 10px;
			padding: 0 0 5px;
		}

		#wcfm-main-content #item-header-cover-image #item-buttons:after {
			clear: both;
			content: "";
			display: table;
		}

		@media screen and (max-width: 782px) {
			#wcfm-main-content #item-header-cover-image #item-header-avatar,
			.bp-user #wcfm-main-content #item-header #item-header-cover-image #item-header-avatar,
			#wcfm-main-content div#item-header #item-header-cover-image #item-header-content {
				width: 100%;
				text-align: center;
			}

			#wcfm-main-content #item-header-cover-image #item-header-avatar a {
				display: inline-block;
			}

			#wcfm-main-content #item-header-cover-image #item-header-avatar img {
				margin: 0;
			}

			#wcfm-main-content div#item-header #item-header-cover-image #item-header-content,
			body.single-item.groups #wcfm-main-content div#item-header #item-header-cover-image #item-header-content,
			body.single-item.groups #wcfm-main-content div#item-header #item-header-cover-image #item-actions {
				margin: 0;
			}

			body.single-item.groups #wcfm-main-content div#item-header #item-header-cover-image #item-header-content,
			body.single-item.groups #wcfm-main-content div#item-header #item-header-cover-image #item-actions {
				max-width: 100%;
			}

			#wcfm-main-content div#item-header-cover-image h2 a,
			#wcfm-main-content div#item-header-cover-image h2 {
				color: inherit;
				text-shadow: none;
				margin: 25px 0 0;
				font-size: 200%;
			}

			#wcfm-main-content #item-header-cover-image #item-buttons div {
				float: none;
				display: inline-block;
			}

			#wcfm-main-content #item-header-cover-image #item-buttons:before {
				content: "";
			}

			#wcfm-main-content #item-header-cover-image #item-buttons {
				margin: 5px 0;
			}
		}
	</style>';
	
	?>