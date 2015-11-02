<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and functions required to use the shortcode sub pages plugin
 *
 * @package    Somc_Subpages_Novitas_Consulting
 * @subpackage Somc_Subpages_Novitas_Consulting/public
 * @author     Andrew Pool <ap@novitas.as>
 */
class Somc_Subpages_Novitas_Consulting_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
    private $subpages_html;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        $this->subpages_html = '';
	}

    /**
     * Controller function to coordinate the other functions necassary for generating the front end html and javascript.
     *
     * @since    1.0.0
     * @access   public
     */
    public function render_subpages_tree(){
        // sort order button
        $this->render_sort_button();

        // parent html tag which javascript uses to insert the generated accordion
        $this->render_tree_tag();

        // renders all the javascript required for the accordion
        $this->render_javascript();
    }

    /**
     * Renders the javascript data object which is used by the sub pages accordion.
     *
     * @since    1.0.0
     * @access   public
     * @return   string    javascript sub pages data object.
     */
    public function render_javascript_tree_data_function(){
        $javascript_data = "";
        $javascript_data .= "function getTreeData(){";

        // fetch current page id and a collection of all subpages
        global $post;
        $oldest_parent = $post->ID;
        $mypages = get_pages( array( 'child_of' => $oldest_parent, 'hierarchical' => 0, 'parent' => $oldest_parent, 'show_image' => 1 ) );
        $counter = 1;
        $first_page = true;
        $javascript_data .= "var data = [";

        // loop through pages and children pages, and generate the data object
        foreach( $mypages as $page ) {
            if(!$first_page){
                $javascript_data .= ", ";
            }

            // set parent data
            $javascript_data .= "{label: '" . $this->get_page_title_excerpt($page) .  "' , id: " . $page->ID . " , guid: '" . $page->guid . "' , image_url: '" . $this->getImageUrl($page) . "' , image_title: '" . esc_attr($page->post_title) . "'";
            $children = get_pages( array( 'child_of' => $page->ID ));
            if ( $children ) {
                $javascript_data .= ", children: [";
                $first_child = true;
                foreach( $children as $child ) {
                    if(!$first_child){
                        $javascript_data .= ", ";
                    }
                    // set children data
                    $javascript_data .= "{label: '" . $this->get_page_title_excerpt($child) . "', id: " . $child->ID .  " , guid: '" . $child->guid .  "' , image_url: '" . $this->getImageUrl($child, true) . "' , image_title: '" . esc_attr($page->post_title) . "'}";
                    $first_child = false;
                }
                $javascript_data .= "]";
            }
            $javascript_data .= "}";
            $counter++;
            $first_page = false;
        }

        $javascript_data .= "];
                            return data;
                            }
                            ";

        return $javascript_data;
    }

    /**
     * Returns the page title's first 20 characters
     *
     * @since    1.0.0
     * @return   string    javascript sub pages data object.
     *
     * @param    object    $page    object containing the page data.
     * @return   string    truncated page title.
     */
    public function get_page_title_excerpt($page){
        $title = esc_attr($page->post_title);
        $max_length = 20;
        $title_length = strlen ($title);

        // check if the length of the string is greater than our assigned max length
        if ( $title_length > $max_length ) {
            // if it is display a substring of the title
            $title_excerpt = substr($title, 0, $max_length) . '...';
        } else {
            // otherwise just return title
            $title_excerpt = $title;
        }
        return $title_excerpt;
    }

    /**
     * Renders the html button for toggling the sort order of the collapsable list.
     *
     * @since    1.0.0
     * @access   public
     */
    public function render_sort_button(){
        ?>
        <div class="btn-container"><button value="asc" id="subpages-order-btn" class="btn">Ascending Order</button></div>
    <?php
    }

    /**
     * Renders the html parent tag for inserting the collapsable list html.
     *
     * @since    1.0.0
     * @access   public
     */
    public function render_tree_tag(){
        ?>
        <ul id="list-subpages"></ul>
    <?php
    }

    /**
     * Renders most of the javascript required for generating and manipulating the collapsable list html. (remaining javascript is in js\somc-subpages_novitas_consulting-public.js)
     *
     * @since    1.0.0
     * @access   public
     */
    public function render_javascript(){
        ?>
        <script>
            jQuery(function($){

                /* generates javascript function which returns the data object used for generating sub pages accordion */
                <?php echo $this->render_javascript_tree_data_function(); ?>

                /* build accordion from data */
                var list_data = getTreeData();
                sort_object_asc(list_data);
                load_subpages_list(list_data);

                /* function to sort pages and children in asc title */
                function sort_object_asc(list_data)
                {
                    // sort parent objects
                    list_data.sort(function (a, b) {
                        var labelA = a.label.toLowerCase(), labelB = b.label.toLowerCase()
                        if (labelA < labelB) //sort string ascending
                            return -1
                        if (labelA > labelB)
                            return 1
                        return 0 //default return value (no sorting)
                    });

                    // sort children objects
                    $.each(list_data, function (index, element) {
                        if (typeof element['children'] === "object") {
                            element['children'].sort(function (a, b) {
                                var labelA = a.label.toLowerCase(), labelB = b.label.toLowerCase()
                                if (labelA < labelB) //sort string ascending
                                    return -1
                                if (labelA > labelB)
                                    return 1
                                return 0 //default return value (no sorting)
                            });
                        }
                    });
                }

                /* function to sort pages and children in desc title */
                function sort_object_desc(list_data){
                    // sort parent objects
                    list_data.sort(function (a, b) {
                        var labelA = a.label.toLowerCase(), labelB = b.label.toLowerCase()
                        if (labelA < labelB) //sort string ascending
                            return 1
                        if (labelA > labelB)
                            return -1
                        return 0 //default return value (no sorting)
                    });

                    // sort children objects
                    $.each(list_data, function (index, element) {
                        if (typeof element['children'] === "object") {
                            element['children'].sort(function (a, b) {
                                var labelA = a.label.toLowerCase(), labelB = b.label.toLowerCase()
                                if (labelA < labelB) //sort string ascending
                                    return 1
                                if (labelA > labelB)
                                    return -1
                                return 0 //default return value (no sorting)
                            });
                        }
                    });
                }

                /* observer for sort toggle button - re-fetches data and sorts by asc or desc order */
                $(document).on('click', '#subpages-order-btn', function (){
                    var new_data = getTreeData();
                    var order = $('#subpages-order-btn').val();
                    if(order == 'desc'){
                        $('#subpages-order-btn').val('asc');
                        $('#subpages-order-btn').text('Ascending Order');
                        sort_object_asc(new_data);
                    }
                    else{
                        $('#subpages-order-btn').val('desc');
                        $('#subpages-order-btn').text('Descending Order');
                        sort_object_desc(new_data);
                    }
                    load_subpages_list(new_data);
                });

                /* function which constructs the accordian based on the data object */
                function load_subpages_list(list_data){
                    $('#list-subpages').empty();
                    var list_html = '';
                    $.each(list_data, function(index, element) {
                        var child_html = '';
                        var parent_tag = '';

                        // parent elements
                        parent_tag = '<li class="parent-list"><div class="parent-row row">' + getImageHTML(element['image_url'], element['image_title']) + getLinkHTML(element['guid'], element['label']) + '</div>';

                        // loop through children
                        if (typeof element['children'] === "object") {

                            // children elements
                            parent_tag = '<li class=\"expandable parent-list\"><div class="parent-row row">' + getImageHTML(element['image_url'], element['image_title']) + getLinkHTML(element['guid'], element['label']) + '<span class="expand open-arrow"></span></div><ul style="display: none;"><i class="arrow-up"></i>';
                            $.each(element['children'], function(index, element){
                                child_html += '<li class="child-list"><div class="child-row row">' + getImageHTML(element['image_url'], element['image_title']) + getLinkHTML(element['guid'], element['label']) + '</div></li>';
                            });
                            child_html += '</ul>';

                        }
                        list_html += parent_tag;
                        list_html += child_html;
                        list_html += '</li></ul>';
                    });
                    $('#list-subpages').prepend(list_html);
                }

                /* function which generates the image thumbnail html */
                function getImageHTML(image_url, title){
                    if(image_url == '' || image_url == null){
                        return '<div class="col-left col"></div>';
                    }
                    return '<div class="col-left col"><img src="' + image_url + '" alt="' + title + '" title="' + title + '" /></div>';
                }

                /* function which generates the link html */
                function getLinkHTML(link, label){
                    if(link == '' || link == null){
                        return '<div class="col-right col"></div>';
                    }
                    return '<div class="col-right col"><a style="text-decoration:none" href="' + link + '">' + label + '</a></div>';
                }

            });
        </script>
    <?php

    }

    /**
     * Returns the image url of the page
     *
     * @since    1.0.0
     * @access   public
     *
     * @param    object    $page    object containing the page data.
     * @return   string    image url.
     */
    public function getImageUrl($page){
        $img_url = '';
        if (get_the_post_thumbnail( $page->ID)) { // if there is a featured image
            // get the featured image and set its size
            $image = wp_get_attachment_image_src( get_post_thumbnail_id($page->ID));
            $img_url = $image[0]; // get the src of the featured image
        }
        return $img_url;
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
     * @access   public
     *
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/somc-subpages_novitas_consulting-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the scripts for the public-facing side of the site.
	 *
	 * @since    1.0.0
     * @access   public
     *
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/somc-subpages_novitas_consulting-public.js', array( 'jquery' ), $this->version, false );
	}

}
