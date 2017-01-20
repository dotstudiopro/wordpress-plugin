	<?php
		$headline_video = channel_headline_video();
		if(isset($headline_video->player)) {
				$minifyVid = get_option('ds_player_minivid');
				$autoRedir = get_option('ds_player_autoredir');
				$autoPlay = get_option('ds_player_autoplay');
		?>


			<div class='ds-metabox'>

					<!-- TITLE -->
					<div class='row'>
							<div class='col-md-12'>
								<h1 class='ds-video-headliner-title'><?php echo $headline_video->title ?></h1>
							</div>
					</div>

					<!-- DESCRIPTION -->
	        <div class="row">
	        		<div class='col-md-12 ds-metabox'>
			      		<span class='ds-video-headliner-description'><?php echo $headline_video->description ?></span>
			      		<hr>
								<a class='ds-more'>Show More</a>
	        		</div>
	        </div>

	        <!-- METADATA AND SHARING -->
					<div class='row'>
							<!-- meta -->
							<div class='col-md-9'>
								<ul class='ds-videometalist'>
							  			<li><?php echo $headline_video->duration ?> min</li>
				              <li><?php echo $headline_video->country ?></li>
				              <li>Rating:<?php echo $headline_video->rating ?></li>
				              <li><?php echo $headline_video->language ?></li>
				              <li><?php echo $headline_video->year ?></li>
			              	<li><?php echo $headline_video->company ?></li>
			         	</ul>
		       	 	</div>
		       	 	<!-- sharing -->
		       	 	<div class='col-md-3'>
				        <?php
								if(is_file( dirname( __FILE__ ) ."/../components/sharing.php" ) ){
									include( dirname( __FILE__ ) ."/../components/sharing.php" );
								} else if( is_file( dirname( __FILE__ ) . "/ds-sharing.php" ) ){
									include( dirname( __FILE__ ) . "/ds-sharing.php" );
								}
								?>
		       	 	</div>
	        </div>
	    </div>

	    <!-- VIDEO PLAYER -->
			<div class='ds-video-headliner'>
					<div id="anibox">&nbsp;</div>
			    <div class='row'>
			    		<div class='col-md-8 ds-video'>
									<div class='ds-video-fluidMedia'>
											<div class='ds-player-togglemode'><i class='fa fa-arrows-alt fa-2x'>&nbsp;</i></div>
											<div class="player" data-minifyVid='<?php echo $minifyVid;?>' data-autoRedir='<?php echo $autoRedir;?>' data-autoPlay='<?php echo $autoPlay;?>'></div>
											<script src="<?php echo $headline_video->player ?>"></script>
									</div>
			    		</div>
			    		<!-- STANDARD MODE PLAYLIST -->
			    		<div class='col-md-4 ds-vid-playlist ds-playlist-standard-mode active-playlist'>
			    				
			    		</div>
			    </div>

			    <div class='row'>
			    		<!-- THEATER MODE PLAYLIST -->			    		
			    		<div class='col-md-12 ds-vid-playlist ds-playlist-theater-mode'>
			    				<div><label>Related Videos</label></div>
			    				<div class='ds-playlist-theater-mode-wrapper'>
				    				<div class='related-videos-carousel'>
				    						<?php echo do_shortcode("[ds_owl_carousel  channels='all-things-cannabis,art-attack,awesome-ink,cartographies,collegehumor,cupcake-jemma,epic-meal-time' autoplay_hover_pause='1' autoplay='0' autoplay_timeout='3000' autoplay_speed='1000' notitle='1' items='10']"); ?>
												<?php 
													/*
														$rec_id = '587fd28799f815f820681c9c';
														$rec_size = 5;				    						
														cho ds_owl_recommended_videos_html(array('rec_id' => $rec_id, 'rec_size' => $rec_size)); 
													*/
												?>
				    				</div>
				    			</div>
			    		</div>
			    </div>
			</div>

		<?php

		}
