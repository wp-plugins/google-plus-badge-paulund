<?php
/*
 * Plugin Name: Paulund Google Badge Widget
 * Plugin URI: http://www.paulund.co.uk
 * Description: A widget that allows you to display Your Google Plus badge
 * Version: 1.0
 * Author: Paul Underwood
 * Author URI: http://www.paulund.co.uk
 * License: GPL2 

    Copyright 2012  Paul Underwood

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License,
    version 2, as published by the Free Software Foundation. 

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details. 
*/

/**
 * Register the Widget
 */
add_action( 'widgets_init', create_function( '', 'register_widget("pu_google_badge_widget");' ) ); 

/**
 * Create the widget class and extend from the WP_Widget
 */
 class pu_google_badge_widget extends WP_Widget {
 	
	private $google_title = FALSE;
	private $google_profile_id = FALSE;
	private $google_width = "300";
	private $google_badge_type = FALSE;
	private $google_theme = FALSE;
	private $google_profile_type = FALSE;
	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		
		parent::__construct(
			'pu_google_badge_widget',		// Base ID
			'Google Plus Badge Widget',		// Name
			array(
				'classname'		=>	'pu_google_badge_widget',
				'description'	=>	__('A widget that allows you to display your Google Plus badge.', 'framework')
			)
		);

		// Load JavaScript and stylesheets
		$this->register_scripts_and_styles();

	} // end constructor
	
	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	public function register_scripts_and_styles() {
		
		

	} // end register_scripts_and_styles
	
	/**
	 * Add Facebook javascripts
	 */
	public function add_js(){
		echo '<link href="https://plus.google.com/'.$this->google_profile_id.'" rel="'.$this->google_profile_type.'" /><script type="text/javascript">
			window.___gcfg = {lang: \'en\'};
			(function() 
			{var po = document.createElement("script");
			po.type = "text/javascript"; po.async = true;po.src = (\'https:\' == document.location.protocol ? \'https\' : \'http\') + "://apis.google.com/js/plusone.js";
			var s = document.getElementsByTagName("script")[0];
			s.parentNode.insertBefore(po, s);
			})();</script>';
	}
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$this->google_title = apply_filters('widget_title', $instance['title'] );
		
		$this->google_profile_id = $instance['profile_id'];
		$this->google_width = $instance['width'];
		$this->google_badge_type = $instance['badge_type'];
		$this->google_theme = $instance['theme'];
		$this->google_profile_type = $instance['profile_type'];
		
		add_action('wp_footer', array(&$this,'add_js'));

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $this->google_title )
			echo $before_title . $this->google_title  . $after_title;

		/* Display Google Badge */
		if($this->google_badge_type == "Icon"){
			?><a href="https://plus.google.com/<?php echo $this->google_profile_id; ?>?prsrc=3" style="text-decoration:none;"><img src="https://ssl.gstatic.com/images/icons/gplus-32.png" alt="" style="border:0;width:32px;height:32px;"/></a><?php
		} else {
			?>
	            <g:plus href="https://plus.google.com/<?php echo $this->google_profile_id; ?>" 
	            width="<?php echo $this->google_width; ?>" 
	            height="<?php echo ($this->google_badge_type == "Small Badge" ? "69" : "131"); ?>" 
	            theme="<?php echo $this->google_theme; ?>"></g:plus>
			<?php 
		}

		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags to remove HTML (important for text inputs)		
		foreach($new_instance as $k => $v){
			$instance[$k] = strip_tags($v);
		}

		return $instance;
	}
	
	/**
	 * Create the form for the Widget admin
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => $this->google_title,
		'profile_id' => $this->google_profile_id,
		'width' => $this->google_width,
		'badge_type' => $this->google_badge_type,
		'theme' => $this->google_theme,
		'profile_type' => $this->google_profile_type
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<!-- Profile id: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'profile_id' ); ?>"><?php _e('Profile Id', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'profile_id' ); ?>" name="<?php echo $this->get_field_name( 'profile_id' ); ?>" value="<?php echo $instance['profile_id']; ?>" />
		</p>
		
		<!-- Badge Width: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width', 'framework') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" />
		</p>
		
		<!-- Badge Type: Select -->
		<p>
			<label for="<?php echo $this->get_field_id( 'badge_type' ); ?>"><?php _e('Badge Type', 'framework') ?></label>
			<select id="<?php echo $this->get_field_id( 'badge_type' ); ?>" name="<?php echo $this->get_field_name( 'badge_type' ); ?>">
				<option <?php echo ($instance['badge_type'] == "Icon" ? 'selected="selected"' : ''); ?>>Icon</option>
				<option <?php echo ($instance['badge_type'] == "Small Badge" ? 'selected="selected"' : ''); ?>>Small Badge</option>
				<option <?php echo ($instance['badge_type'] == "Large Badge" ? 'selected="selected"' : ''); ?>>Large Badge</option>
			</select>
		</p>
		
		<!-- Google Theme: Select -->
		<p>
			<label for="<?php echo $this->get_field_id( 'theme' ); ?>"><?php _e('Google Theme', 'framework') ?></label>
			<select id="<?php echo $this->get_field_id( 'theme' ); ?>" name="<?php echo $this->get_field_name( 'theme' ); ?>">
				<option <?php echo ($instance['theme'] == "Light" ? 'selected="selected"' : ''); ?>>Light</option>
				<option <?php echo ($instance['theme'] == "Dark" ? 'selected="selected"' : ''); ?>>Dark</option>
			</select>
		</p>
		
		<!-- Google Profile Type: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'profile_type' ); ?>"><?php _e('Profile Type', 'framework') ?></label>
			<select id="<?php echo $this->get_field_id( 'profile_type' ); ?>" name="<?php echo $this->get_field_name( 'profile_type' ); ?>">
				<option <?php echo ($instance['profile_type'] == "Publisher" ? 'selected="selected"' : ''); ?>>Publisher</option>
				<option <?php echo ($instance['profile_type'] == "Author" ? 'selected="selected"' : ''); ?>>Author</option>
			</select>
		</p>
		
	<?php
	}
}
?>