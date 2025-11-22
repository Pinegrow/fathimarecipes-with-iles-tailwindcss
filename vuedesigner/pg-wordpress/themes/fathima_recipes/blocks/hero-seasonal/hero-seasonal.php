<header <?php if(empty($_GET['context']) || $_GET['context'] !== 'edit') echo get_block_wrapper_attributes( array('class' => "bg-center bg-cover bg-no-repeat font-sans not-prose relative bg-[url('https://images.unsplash.com/photo-1665323707970-1940a5b80871?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDExfHxzdW1tZXIlMjBoZXJiJTIwcGxhbnRlcnxlbnwwfHx8fDE3NjM0NTYyMDB8MA&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop')]", 'style' => ";".( PG_Blocks_v4::getImageUrl( $args, 'background_image', 'full' ) ? ( 'background-image: url('.PG_Blocks_v4::getImageUrl( $args, 'background_image', 'full' ).')' ) : '' )."", ) ); else echo 'data-wp-block-props="true"'; ?>>
    <div class="absolute bg-black/40 inset-0"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-20 sm:py-24 lg:py-32">
            <div class="max-w-3xl">
                <div class="backdrop-blur bg-white/80 p-6 ring-1 ring-white/50 rounded-xl shadow-lg sm:p-8 lg:p-10">
                    <div class="flex gap-2 items-center justify-end ml-auto text-gray-700 text-sm w-full sm:text-base"> <span class="inline-flex h-2.5 w-2.5 rounded-full bg-green-500"></span> <span><?php echo PG_Blocks_v4::getAttribute( $args, 'badge_text' ) ?></span> 
                    </div>
                    <h1 class="mt-3 text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-tight text-gray-900 text-right"><?php echo PG_Blocks_v4::getAttribute( $args, 'main_heading' ) ?></h1>
                    <p class="mt-4 text-base sm:text-lg lg:text-xl text-gray-700 leading-relaxed text-right"><?php echo PG_Blocks_v4::getAttribute( $args, 'description' ) ?></p>
                    <div class="mt-6 flex flex-col sm:flex-row gap-3 sm:gap-4 justify-end">
                        <a href="<?php echo (!empty($_GET['context']) && $_GET['context'] === 'edit') ? 'javascript:void()' : PG_Blocks_v4::getLinkUrl( $args, 'primary_button_link' ) ?>" class="inline-flex items-center justify-center px-5 py-3 text-white bg-green-600 hover:bg-green-700 rounded-md shadow-sm text-sm sm:text-base"><?php echo PG_Blocks_v4::getAttribute( $args, 'primary_button_label' ) ?></a>
                        <a href="<?php echo (!empty($_GET['context']) && $_GET['context'] === 'edit') ? 'javascript:void()' : PG_Blocks_v4::getLinkUrl( $args, 'secondary_button_link' ) ?>" class="inline-flex items-center justify-center px-5 py-3 text-green-700 bg-white hover:bg-gray-50 rounded-md shadow-sm ring-1 ring-gray-200 text-sm sm:text-base"><?php echo PG_Blocks_v4::getAttribute( $args, 'secondary_button_label' ) ?></a>
                    </div>
                    <div class="mt-5 flex items-center gap-4 justify-end">
                        <div class="flex -space-x-2">
                            <?php if ( !PG_Blocks_v4::getImageSVG( $args, 'avatar_image_1', false) && PG_Blocks_v4::getImageUrl( $args, 'avatar_image_1', 'full' ) ) : ?>
                                <img src="<?php echo PG_Blocks_v4::getImageUrl( $args, 'avatar_image_1', 'full' ) ?>" alt="<?php echo PG_Blocks_v4::getImageField( $args, 'avatar_image_1', 'alt', true); ?>" class="<?php echo (PG_Blocks_v4::getImageField( $args, 'avatar_image_1', 'id', true) ? ('wp-image-' . PG_Blocks_v4::getImageField( $args, 'avatar_image_1', 'id', true)) : '') ?> h-8 ring-2 ring-white rounded-full shadow w-8">
                            <?php endif; ?>
                            <?php if ( PG_Blocks_v4::getImageSVG( $args, 'avatar_image_1', false) ) : ?>
                                <?php echo PG_Blocks_v4::mergeInlineSVGAttributes( PG_Blocks_v4::getImageSVG( $args, 'avatar_image_1' ), array( 'class' => 'h-8 w-8 rounded-full ring-2 ring-white shadow' ) ) ?>
                            <?php endif; ?>
                            <?php if ( !PG_Blocks_v4::getImageSVG( $args, 'avatar_image_2', false) && PG_Blocks_v4::getImageUrl( $args, 'avatar_image_2', 'full' ) ) : ?>
                                <img src="<?php echo PG_Blocks_v4::getImageUrl( $args, 'avatar_image_2', 'full' ) ?>" alt="<?php echo PG_Blocks_v4::getImageField( $args, 'avatar_image_2', 'alt', true); ?>" class="<?php echo (PG_Blocks_v4::getImageField( $args, 'avatar_image_2', 'id', true) ? ('wp-image-' . PG_Blocks_v4::getImageField( $args, 'avatar_image_2', 'id', true)) : '') ?> h-8 ring-2 ring-white rounded-full shadow w-8">
                            <?php endif; ?>
                            <?php if ( PG_Blocks_v4::getImageSVG( $args, 'avatar_image_2', false) ) : ?>
                                <?php echo PG_Blocks_v4::mergeInlineSVGAttributes( PG_Blocks_v4::getImageSVG( $args, 'avatar_image_2' ), array( 'class' => 'h-8 w-8 rounded-full ring-2 ring-white shadow' ) ) ?>
                            <?php endif; ?>
                            <?php if ( !PG_Blocks_v4::getImageSVG( $args, 'avatar_image_3', false) && PG_Blocks_v4::getImageUrl( $args, 'avatar_image_3', 'full' ) ) : ?>
                                <img src="<?php echo PG_Blocks_v4::getImageUrl( $args, 'avatar_image_3', 'full' ) ?>" alt="<?php echo PG_Blocks_v4::getImageField( $args, 'avatar_image_3', 'alt', true); ?>" class="<?php echo (PG_Blocks_v4::getImageField( $args, 'avatar_image_3', 'id', true) ? ('wp-image-' . PG_Blocks_v4::getImageField( $args, 'avatar_image_3', 'id', true)) : '') ?> h-8 ring-2 ring-white rounded-full shadow w-8">
                            <?php endif; ?>
                            <?php if ( PG_Blocks_v4::getImageSVG( $args, 'avatar_image_3', false) ) : ?>
                                <?php echo PG_Blocks_v4::mergeInlineSVGAttributes( PG_Blocks_v4::getImageSVG( $args, 'avatar_image_3' ), array( 'class' => 'h-8 w-8 rounded-full ring-2 ring-white shadow' ) ) ?>
                            <?php endif; ?>
                        </div>
                        <p class="text-sm text-gray-700"><?php echo PG_Blocks_v4::getAttribute( $args, 'social_proof_text' ) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>