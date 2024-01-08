<?php
/**
 * Plugin Name: Simple Accordion Post Slider
 * Description: A simple accordion post slider plugin for WordPress. [latest_posts_accordion posts_per_page="5" categories="category1, category2"]

 * Version: 1.0
 * Author: Hassan Naqvi
 */

// Enqueue scripts and styles
function simple_accordion_slider_enqueue_scripts() {
    // Enqueue jQuery if not already loaded
    wp_enqueue_script('jquery');

   
    // Enqueue your style.css file
    wp_enqueue_style('simple-accordion-slider-style', plugin_dir_url(__FILE__) . 'style.css');
}

// Hook into WordPress
add_action('wp_enqueue_scripts', 'simple_accordion_slider_enqueue_scripts');


function custom_latest_posts_accordion($atts) {
    $atts = shortcode_atts(
        array(
            'posts_per_page' => -1,
            'categories'     => '',
        ),
        $atts
    );

    $args = array(
        'posts_per_page' => intval($atts['posts_per_page']),
        'post_status'    => 'publish',
    );

    // Include specific categories
    if (!empty($atts['categories'])) {
        $args['category_name'] = sanitize_text_field($atts['categories']);
    }

    $latest_posts = new WP_Query($args);

    if ($latest_posts->have_posts()) {
        $output = '<ul class="c-accordion">';

        while ($latest_posts->have_posts()) {
            $latest_posts->the_post();

            $post_id = 'post-' . get_the_ID();
            $post_image = get_the_post_thumbnail_url(get_the_ID(), 'full');
            $post_title = get_the_title();
            $post_content = wp_trim_words(get_the_content(), 30); // Change 20 to the desired number of words
            $post_permalink = get_permalink(); // Get the post permalink

            $output .= '<li id="' . esc_attr($post_id) . '" class="c-accordion__item" style="--cover: url(' . esc_url($post_image) . ')">';
            $output .= '<a href="' . esc_url($post_permalink) . '" class="c-accordion__action">';
            $output .= '<div class="c-accordion__content">';
            $output .= '<h2 class="c-accordion__title c-accordion__title--hero c-accordion__title--hover-show">' . esc_html($post_title) . '</h2>';
            $output .= '<p class="c-accordion__description">' . esc_html($post_content) . '</p>';
            $output .= '</div>';
            $output .= '<div class="c-accordion__aside">';
            $output .= '<h2 class="c-accordion__title c-accordion__title--hover-hide">' . esc_html($post_title) . '</h2>';
            $output .= '</div>';
            $output .= '</a>';
            $output .= '</li>';
        }

        $output .= '</ul>';

        wp_reset_postdata();

        return $output;
    } else {
        return '<p>No posts found</p>';
    }
}

// Register the shortcode
add_shortcode('latest_posts_accordion', 'custom_latest_posts_accordion');
