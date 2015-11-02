<?php

/**
 * Widget class for the sub pages sidebar widget.
 *
 * Generates admin widget functionality and generates the public sidebar html
 *
 * @package    Somc_Subpages_Novitas_Consulting
 * @subpackage Somc_Subpages_Novitas_Consulting/includes
 * @author     Andrew Pool <ap@novitas.as>
 */
class Somc_Subpages_Novitas_Consulting_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'Somc_Subpages_Novitas_Consulting_Widget',
            __('Somc Sub Pages Widget', 'text_domain'),
            array('description' => __( 'Displays a list of sub pages of the current page in the sidebar', 'text_domain' ),)
        );
    }

    /**
     * Front-end display of widget.
     *
     * Gets the descendants of the current page and its sub pages and renders the sidebar list html
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        global $post;
        extract($args,EXTR_SKIP);

        $widget_title = $instance['title'];
        $no_pages_notice = 'No children pages...';

        // get page collection
        $oldest_parent = $post->ID;
        $mypages = get_pages( array( 'child_of' => $oldest_parent, 'hierarchical' => 0, 'parent' => $oldest_parent ));
        $counter = 1;
        ?>
        <aside id="pages-2" class="widget widget_subpages">
        <h2 class="widget-title"><?php echo $widget_title ?></h2>
        <div class="subpages-sidebar">
        <ul class="side-parent-ul">
        <?php if(!empty($mypages)) { ?>
            <?php foreach ($mypages as $page) { ?>
                <li class="side-parent-li">
                    <a class="title-link" href="<?php echo $page->guid; ?>"><?php echo $this->get_page_title_excerpt($page); ?></a>
                </li>
                <?php $children = get_pages(array('child_of' => $page->ID));
                if ($children) { ?>
                    <li>
                        <ul class="side-child-ul">
                            <?php foreach ($children as $child) { ?>
                                <li class="side-child-li"><a
                                        href="<?php echo $child->guid; ?>"><?php echo $this->get_page_title_excerpt($child); ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>

                <?php } ?>

                <?php $counter++;
            }
        } else { ?>
             <div class="no-pages-notice"><?php echo $no_pages_notice; ?></div>
            <?php } ?>
        </ul>
        </div>
        </aside>
        <?php
    }

    /**
     * Renders the javascript data object which is used by the sub pages accordion.
     *
     * @since    1.0.0
     * @access   public
     * @return   string    javascript sub pages data object.
     */
    public function render_page_title($page){
        ?>
        <a href="<?php echo get_permalink($page->ID); ?>" title="<?php echo $this->get_page_title_excerpt($page); ?>"><?php $this->get_page_title_excerpt($page); ?></a>
        <?php
    }

    /**
     * Back-end widget form.
     *
     * Creates the widget backend form to allow the user to set the title of the sub pages sidebar widget
     *
     * @since     1.0.0
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'text_domain' );
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
    <?php
    }

    public function get_page_title_excerpt($page){
        $plugin_public = new Somc_Subpages_Novitas_Consulting_Public($this->get_plugin_name(), $this->get_version());
        return $plugin_public->get_page_title_excerpt($page);
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @since     1.0.0
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Somc_Subpages_Novitas_Consulting_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}