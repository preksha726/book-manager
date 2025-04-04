<?php
class Book_Manager {

    // Initialize the plugin
    public function init() {
        add_action('init', array($this, 'register_book_post_type'));
        add_action('save_post_book', array($this, 'save_book_meta'), 10, 3);
        add_action('admin_menu', array($this, 'add_book_meta_box'));
        add_shortcode('book_list', array($this, 'display_books_on_frontend'));
    }

    // Register the "Book" custom post type
    public function register_book_post_type() {
        $labels = array(
            'name'               => 'Books',
            'singular_name'      => 'Book',
            'menu_name'          => 'Books',
            'name_admin_bar'     => 'Book',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Book',
            'new_item'           => 'New Book',
            'edit_item'          => 'Edit Book',
            'view_item'          => 'View Book',
            'all_items'          => 'All Books',
            'search_items'       => 'Search Books',
            'parent_item_colon'  => 'Parent Books:',
            'not_found'          => 'No books found.',
            'not_found_in_trash' => 'No books found in Trash.'
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'      => 5,
            'supports'           => array('title', 'editor'),
            'has_archive'        => true,
            'show_in_rest'       => true,
            'rewrite'            => array('slug' => 'books')
        );

        register_post_type('book', $args);
    }

    // Add meta box to add custom fields (genre and name)
    public function add_book_meta_box() {
        add_meta_box(
            'book_details',
            'Book Details',
            array($this, 'display_book_meta_box'),
            'book',
            'normal',
            'high'
        );
    }

    // Display the fields inside the meta box
    public function display_book_meta_box($post) {
        $genre = get_post_meta($post->ID, '_book_genre', true);
        $name = get_post_meta($post->ID, '_book_name', true);

        wp_nonce_field('book_details_nonce', 'book_details_nonce_field');

        echo '<label for="book_genre">Genre</label>';
        echo '<input type="text" id="book_genre" name="book_genre" value="' . esc_attr($genre) . '" />';

        echo '<label for="book_name">Book Name</label>';
        echo '<input type="text" id="book_name" name="book_name" value="' . esc_attr($name) . '" />';
    }

    // Save the custom fields when the book is saved
    public function save_book_meta($post_id, $post, $update) {
        if (!isset($_POST['book_details_nonce_field']) || !wp_verify_nonce($_POST['book_details_nonce_field'], 'book_details_nonce')) {
            return;
        }

        if (isset($_POST['book_genre'])) {
            update_post_meta($post_id, '_book_genre', sanitize_text_field($_POST['book_genre']));
        }

        if (isset($_POST['book_name'])) {
            update_post_meta($post_id, '_book_name', sanitize_text_field($_POST['book_name']));
        }
    }

    // Shortcode to display books on the frontend
 public function display_books_on_frontend() {
    $args = array(
        'post_type' => 'book',
        'posts_per_page' => -1
    );

    $query = new WP_Query($args);
    $microservice = new Microservice_Integration();

    if ($query->have_posts()) {
        $output = '<div class="book-list">';
        while ($query->have_posts()) {
            $query->the_post();
            $genre = get_post_meta(get_the_ID(), '_book_genre', true);
            $name = get_post_meta(get_the_ID(), '_book_name', true);
            $recommendations = $microservice->fetch_recommendations($genre);
            
            $output .= '<div class="book-item">';
            $output .= '<h3>' . get_the_title() . '</h3>';
            $output .= '<p>Genre: ' . esc_html($genre) . '</p>';
            $output .= '<p>Name: ' . esc_html($name) . '</p>';
            $output .= '<p>Recommendations: ' . esc_html($recommendations) . '</p>';
            $output .= '</div>';
        }
        $output .= '</div>';
        wp_reset_postdata();
        return $output;
    } else {
        return 'No books found.';
    }
  }
}
