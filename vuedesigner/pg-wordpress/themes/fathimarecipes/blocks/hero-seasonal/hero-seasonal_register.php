<?php

        PG_Blocks_v4::register_block_type( array(
            'name' => 'fathima-recipes/hero-seasonal',
            'title' => __( 'Hero - Seasonal Gardening', 'fathima_recipes' ),
            'description' => __( 'Hero section promoting seasonal gardening guides with call-to-action buttons', 'fathima_recipes' ),
            'render_template' => 'blocks/hero-seasonal/hero-seasonal.php',
            'supports' => array(),
            'base_url' => get_template_directory_uri(),
            'base_path' => get_template_directory(),
            'js_file' => 'blocks/hero-seasonal/hero-seasonal.js',
            'attributes' => array(
                'background_image' => array(
                    'type' => array('object', 'null'),
                    'default' => array('id' => 0, 'url' => '', 'size' => '', 'svg' => '', 'alt' => null)
                ),
                'badge_text' => array(
                    'type' => array('string', 'null'),
                    'default' => 'Seasonal gardening made simple'
                ),
                'main_heading' => array(
                    'type' => array('string', 'null'),
                    'default' => 'Help your garden thrive in every season'
                ),
                'description' => array(
                    'type' => array('string', 'null'),
                    'default' => 'Transform your garden with our comprehensive guides covering everything from soil preparation to seasonal maintenance.'
                ),
                'primary_button_link' => array(
                    'type' => array('object', 'null'),
                    'default' => array('post_id' => 0, 'url' => '/guides', 'post_type' => '', 'title' => '')
                ),
                'primary_button_label' => array(
                    'type' => array('string', 'null'),
                    'default' => 'Get the Seasonal Guide'
                ),
                'secondary_button_link' => array(
                    'type' => array('object', 'null'),
                    'default' => array('post_id' => 0, 'url' => '/blog', 'post_type' => '', 'title' => '')
                ),
                'secondary_button_label' => array(
                    'type' => array('string', 'null'),
                    'default' => 'Browse Tips & Tricks'
                ),
                'avatar_image_1' => array(
                    'type' => array('object', 'null'),
                    'default' => array('id' => 0, 'url' => 'https://images.unsplash.com/photo-1653694577641-a8c511df0df9?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fGhlcmIlMjBiZWQlMjBjbG9zZXVwfGVufDB8fHx8MTc2MzQ1NjE5OHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', 'size' => '', 'svg' => '', 'alt' => 'Healthy herb bed')
                ),
                'avatar_image_2' => array(
                    'type' => array('object', 'null'),
                    'default' => array('id' => 0, 'url' => 'https://images.unsplash.com/photo-1722973681429-04332f5cb901?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fHBlcmVubmlhbCUyMGJsb29tc3xlbnwwfHx8fDE3NjM0NTYxOTl8MA&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', 'size' => '', 'svg' => '', 'alt' => 'Perennial blooms')
                ),
                'avatar_image_3' => array(
                    'type' => array('object', 'null'),
                    'default' => array('id' => 0, 'url' => 'https://images.unsplash.com/photo-1757917702671-fff5a7b3c2e3?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDEyfHx2ZWdldGFibGUlMjBoYXJ2ZXN0fGVufDB8fHx8MTc2MzQ1NjIwMHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', 'size' => '', 'svg' => '', 'alt' => 'Autumn vegetable harvest')
                ),
                'social_proof_text' => array(
                    'type' => array('string', 'null'),
                    'default' => 'Fresh, easy advice for your beds and planters.'
                )
            ),
            'example' => array(
'background_image' => array('id' => 0, 'url' => '', 'size' => '', 'svg' => '', 'alt' => null), 'badge_text' => 'Seasonal gardening made simple', 'main_heading' => 'Help your garden thrive in every season', 'description' => 'Transform your garden with our comprehensive guides covering everything from soil preparation to seasonal maintenance.', 'primary_button_link' => array('post_id' => 0, 'url' => '/guides', 'post_type' => '', 'title' => ''), 'primary_button_label' => 'Get the Seasonal Guide', 'secondary_button_link' => array('post_id' => 0, 'url' => '/blog', 'post_type' => '', 'title' => ''), 'secondary_button_label' => 'Browse Tips & Tricks', 'avatar_image_1' => array('id' => 0, 'url' => 'https://images.unsplash.com/photo-1653694577641-a8c511df0df9?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fGhlcmIlMjBiZWQlMjBjbG9zZXVwfGVufDB8fHx8MTc2MzQ1NjE5OHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', 'size' => '', 'svg' => '', 'alt' => 'Healthy herb bed'), 'avatar_image_2' => array('id' => 0, 'url' => 'https://images.unsplash.com/photo-1722973681429-04332f5cb901?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fHBlcmVubmlhbCUyMGJsb29tc3xlbnwwfHx8fDE3NjM0NTYxOTl8MA&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', 'size' => '', 'svg' => '', 'alt' => 'Perennial blooms'), 'avatar_image_3' => array('id' => 0, 'url' => 'https://images.unsplash.com/photo-1757917702671-fff5a7b3c2e3?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDEyfHx2ZWdldGFibGUlMjBoYXJ2ZXN0fGVufDB8fHx8MTc2MzQ1NjIwMHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', 'size' => '', 'svg' => '', 'alt' => 'Autumn vegetable harvest'), 'social_proof_text' => 'Fresh, easy advice for your beds and planters.'
            ),
            'dynamic' => true,
            'version' => '1.0.2'
        ) );
