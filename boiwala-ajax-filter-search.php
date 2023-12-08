<?php
/*
Plugin Name: Boiwala Ajax Filter Search
Description: Custom Ajax search filter for books.
Version: 1.0
Author: Shamimsweb
*/

function boiwala_ajax_filter_search_enqueue_scripts() {
    wp_enqueue_script('boiwala-ajax-filter-search', plugin_dir_url(__FILE__) . 'filter.js', array('jquery'), '1.0', true);
    wp_localize_script('boiwala-ajax-filter-search', 'ajax_info', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_enqueue_style('boiwala-ajax-filter-search-style', plugin_dir_url(__FILE__) . 'filter-style.css');
}
add_action('wp_enqueue_scripts', 'boiwala_ajax_filter_search_enqueue_scripts');

function boiwala_ajax_filter_search_shortcode() {
    ob_start(); ?>

    <div id="boiwala-ajax-filter-search">
        <form action="" method="get">
            <input type="text" name="search" id="search" value="" placeholder="Search Here..">
            <div class="column-wrap">
                <div class="column">
                    <label for="year">Year of Published</label>
                    <input type="number" name="year" id="year">
                </div>
                <div class="column">
                    <label for="isbn">ISBN Number</label>
                    <select name="isbn" id="isbn">
                        <option value="">ISBN</option>
                        <option value="isbn-6565">isbn-6565</option>
                        <option value="isbn-6564">isbn-6564</option>
                        <option value="isbn-6563">isbn-6563</option>
                    </select>
                </div>
            </div>
            <div class="column-wrap">
                <div class="column">
                    <label for="price">Book Price</label>
                    <select name="price" id="price">
                        <option value="">Price</option>
                        <option value="500">500</option>
                        <option value="400">400</option>
                    </select>
                </div>
                <div class="column">
                    <label for="categories">Book Categories</label>
                    <select name="categories" id="categories">
                        <option value="">Any Categories</option>
                        <option value="novel">Novel</option>
                        <option value="poem">Poem</option>
                        <option value="story">Story</option>
                        <option value="rhyms">Rhyms</option>
                    </select>
                </div>
            </div>
            <input type="submit" id="submit" name="submit" value="Search">
        </form>

        <?php
        $args = array(
            'post_type' => 'book',
            'posts_per_page' => -1,
        );

        $search_query = new WP_Query($args);
        $result = array();

        while ($search_query->have_posts()) {
            $search_query->the_post();
            $result[] = array(
                "id" => get_the_ID(),
                "title" => get_the_title(),
                "content" => get_the_content(),
                "permalink" => get_permalink(),
                "year" => get_post_meta(get_the_ID(), 'year_publsihed', true),
                "isbn" => get_post_meta(get_the_ID(), 'book_isbn_number', true),
                "price" => get_post_meta(get_the_ID(), 'book_price', true),
                "categories" => strip_tags(get_the_term_list(get_the_ID(), 'books-category', '', ', ')),
                "poster" => wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()), 'full')
            );
        }

        wp_reset_query(); ?>

        <ul id="boiwala-ajax-filter-search-results">
            <?php foreach ($result as $item): ?>
                <li id="book-<?php echo $item['id']; ?>">
                    <a href="<?php echo $item['permalink']; ?>" title="<?php echo $item['title']; ?>">
                        <img src="<?php echo $item['poster']; ?>" alt="<?php echo $item['title']; ?>" />
                        <div class="book-info">
                            <h4><?php echo $item['title']; ?></h4>
                            <p>Year: <?php echo $item['year']; ?></p>
                            <p>ISBN: <?php echo $item['isbn']; ?></p>
                            <p>Price: <?php echo $item['price']; ?></p>
                            <p>Categories: <?php echo $item['categories']; ?></p>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('boiwala_ajax_filter_search', 'boiwala_ajax_filter_search_shortcode');

function boiwala_ajax_filter_search_callback() {
    header("Content-Type: application/json");

    $meta_query = array('relation' => 'AND');

    if (isset($_GET['year'])) {
        $year = sanitize_text_field($_GET['year']);
        $meta_query[] = array(
            'key' => 'year_publsihed',
            'value' => $year,
            'compare' => '='
        );
    }

    if (isset($_GET['isbn'])) {
        $isbn = sanitize_text_field($_GET['isbn']);
        $meta_query[] = array(
            'key' => 'book_isbn_number',
            'value' => $isbn,
            'compare' => '='
        );
    }

    if (isset($_GET['price'])) {
        $price = sanitize_text_field($_GET['price']);
        $meta_query[] = array(
            'key' => 'book_price',
            'value' => $price,
            'compare' => '='
        );
    }

    $tax_query = array();

    if (isset($_GET['categories'])) {
        $categories = sanitize_text_field($_GET['categories']);
        $tax_query[] = array(
            'taxonomy' => 'books-category',
            'field' => 'slug',
            'terms' => $categories
        );
    }

    $args = array(
        'post_type' => 'book',
        'posts_per_page' => -1,
        'meta_query' => $meta_query,
        'tax_query' => $tax_query
    );

    if (isset($_GET['search'])) {
        $search = sanitize_text_field($_GET['search']);
        $search_query = new WP_Query(array(
            'post_type' => 'book',
            'posts_per_page' => -1,
            'meta_query' => $meta_query,
            'tax_query' => $tax_query,
            's' => $search
        ));
    } else {
        $search_query = new WP_Query($args);
    }

    if ($search_query->have_posts()) {
        $result = array();

        while ($search_query->have_posts()) {
            $search_query->the_post();
            $result[] = array(
                "id" => get_the_ID(),
                "title" => get_the_title(),
                "content" => get_the_content(),
                "permalink" => get_permalink(),
                "year" => get_post_meta(get_the_ID(), 'year_publsihed', true),
                "isbn" => get_post_meta(get_the_ID(), 'book_isbn_number', true),
                "price" => get_post_meta(get_the_ID(), 'book_price', true),
                "categories" => strip_tags(get_the_term_list(get_the_ID(), 'books-category', '', ', ')),
                "poster" => wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'full')
            );
        }
        wp_reset_query();

        echo json_encode($result);
    } else {
        echo json_encode(array());
    }
    wp_die();
}

add_action('wp_ajax_boiwala_ajax_filter_search', 'boiwala_ajax_filter_search_callback');
add_action('wp_ajax_nopriv_boiwala_ajax_filter_search', 'boiwala_ajax_filter_search_callback');
?>
