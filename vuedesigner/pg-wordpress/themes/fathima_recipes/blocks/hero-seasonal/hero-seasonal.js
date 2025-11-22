
( function ( blocks, element, blockEditor ) {
    const el = element.createElement,
        registerBlockType = blocks.registerBlockType,
        ServerSideRender = pgGetFeature4("PgGetServerSideRender")(),
        InspectorControls = blockEditor.InspectorControls,
        useBlockProps = blockEditor.useBlockProps;
        
    const {__} = wp.i18n;
    const {ColorPicker, TextControl, ToggleControl, SelectControl, Panel, PanelBody, Disabled, TextareaControl, BaseControl} = wp.components;
    const {useSelect} = wp.data;
    const {RawHTML, Fragment} = element;
   
    const {InnerBlocks, URLInputButton, RichText} = wp.blockEditor;
    const useInnerBlocksProps = blockEditor.useInnerBlocksProps || blockEditor.__experimentalUseInnerBlocksProps;
    
    const propOrDefault = function(val, prop, field) {
        if(block.attributes[prop] && (val === null || val === '')) {
            return field ? block.attributes[prop].default[field] : block.attributes[prop].default;
        }
        return val;
    }
    
    const block = registerBlockType( 'fathima-recipes/hero-seasonal', {
        apiVersion: 2,
        title: 'Hero - Seasonal Gardening',
        description: 'Hero section promoting seasonal gardening guides with call-to-action buttons',
        icon: 'block-default',
        category: 'my_blocks',
        keywords: [],
        supports: {},
        attributes: {
            background_image: {
                type: ['object', 'null'],
                default: {id: 0, url: '', size: '', svg: '', alt: null},
            },
            badge_text: {
                type: ['string', 'null'],
                default: `Seasonal gardening made simple`,
            },
            main_heading: {
                type: ['string', 'null'],
                default: `Help your garden thrive in every season`,
            },
            description: {
                type: ['string', 'null'],
                default: `Transform your garden with our comprehensive guides covering everything from soil preparation to seasonal maintenance.`,
            },
            primary_button_link: {
                type: ['object', 'null'],
                default: {post_id: 0, url: '/guides', title: '', 'post_type': null},
            },
            primary_button_label: {
                type: ['string', 'null'],
                default: `Get the Seasonal Guide`,
            },
            secondary_button_link: {
                type: ['object', 'null'],
                default: {post_id: 0, url: '/blog', title: '', 'post_type': null},
            },
            secondary_button_label: {
                type: ['string', 'null'],
                default: `Browse Tips & Tricks`,
            },
            avatar_image_1: {
                type: ['object', 'null'],
                default: {id: 0, url: 'https://images.unsplash.com/photo-1653694577641-a8c511df0df9?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fGhlcmIlMjBiZWQlMjBjbG9zZXVwfGVufDB8fHx8MTc2MzQ1NjE5OHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', size: '', svg: '', alt: 'Healthy herb bed'},
            },
            avatar_image_2: {
                type: ['object', 'null'],
                default: {id: 0, url: 'https://images.unsplash.com/photo-1722973681429-04332f5cb901?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fHBlcmVubmlhbCUyMGJsb29tc3xlbnwwfHx8fDE3NjM0NTYxOTl8MA&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', size: '', svg: '', alt: 'Perennial blooms'},
            },
            avatar_image_3: {
                type: ['object', 'null'],
                default: {id: 0, url: 'https://images.unsplash.com/photo-1757917702671-fff5a7b3c2e3?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDEyfHx2ZWdldGFibGUlMjBoYXJ2ZXN0fGVufDB8fHx8MTc2MzQ1NjIwMHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', size: '', svg: '', alt: 'Autumn vegetable harvest'},
            },
            social_proof_text: {
                type: ['string', 'null'],
                default: `Fresh, easy advice for your beds and planters.`,
            }
        },
        example: { attributes: { background_image: {id: 0, url: '', size: '', svg: '', alt: null}, badge_text: `Seasonal gardening made simple`, main_heading: `Help your garden thrive in every season`, description: `Transform your garden with our comprehensive guides covering everything from soil preparation to seasonal maintenance.`, primary_button_link: {post_id: 0, url: '/guides', title: '', 'post_type': null}, primary_button_label: `Get the Seasonal Guide`, secondary_button_link: {post_id: 0, url: '/blog', title: '', 'post_type': null}, secondary_button_label: `Browse Tips & Tricks`, avatar_image_1: {id: 0, url: 'https://images.unsplash.com/photo-1653694577641-a8c511df0df9?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fGhlcmIlMjBiZWQlMjBjbG9zZXVwfGVufDB8fHx8MTc2MzQ1NjE5OHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', size: '', svg: '', alt: 'Healthy herb bed'}, avatar_image_2: {id: 0, url: 'https://images.unsplash.com/photo-1722973681429-04332f5cb901?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDR8fHBlcmVubmlhbCUyMGJsb29tc3xlbnwwfHx8fDE3NjM0NTYxOTl8MA&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', size: '', svg: '', alt: 'Perennial blooms'}, avatar_image_3: {id: 0, url: 'https://images.unsplash.com/photo-1757917702671-fff5a7b3c2e3?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDEyfHx2ZWdldGFibGUlMjBoYXJ2ZXN0fGVufDB8fHx8MTc2MzQ1NjIwMHww&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop', size: '', svg: '', alt: 'Autumn vegetable harvest'}, social_proof_text: `Fresh, easy advice for your beds and planters.` } },
        edit: function ( props ) {
            const blockProps = useBlockProps({ className: 'bg-center bg-cover bg-no-repeat font-sans not-prose relative bg-[url(\'https://images.unsplash.com/photo-1665323707970-1940a5b80871?ixid=M3wyMDkyMnwwfDF8c2VhcmNofDExfHxzdW1tZXIlMjBoZXJiJTIwcGxhbnRlcnxlbnwwfHx8fDE3NjM0NTYyMDB8MA&ixlib=rb-4.1.0q=85&fm=jpg&crop=faces&cs=srgb&w=1200&h=800&fit=crop\')]', style: { ...((propOrDefault( props.attributes.background_image.url, 'background_image', 'url' ) ? ('url(' + propOrDefault( props.attributes.background_image.url, 'background_image', 'url' ) + ')') : null !== null && propOrDefault( props.attributes.background_image.url, 'background_image', 'url' ) ? ('url(' + propOrDefault( props.attributes.background_image.url, 'background_image', 'url' ) + ')') : null !== '') ? {backgroundImage: propOrDefault( props.attributes.background_image.url, 'background_image', 'url' ) ? ('url(' + propOrDefault( props.attributes.background_image.url, 'background_image', 'url' ) + ')') : null} : {}) } });
            const setAttributes = props.setAttributes; 
            
            props.background_image = useSelect(function( select ) {
                return {
                    background_image: props.attributes.background_image.id ? select('core').getMedia(props.attributes.background_image.id) : undefined
                };
            }, [props.attributes.background_image] ).background_image;
            

            props.avatar_image_1 = useSelect(function( select ) {
                return {
                    avatar_image_1: props.attributes.avatar_image_1.id ? select('core').getMedia(props.attributes.avatar_image_1.id) : undefined
                };
            }, [props.attributes.avatar_image_1] ).avatar_image_1;
            

            props.avatar_image_2 = useSelect(function( select ) {
                return {
                    avatar_image_2: props.attributes.avatar_image_2.id ? select('core').getMedia(props.attributes.avatar_image_2.id) : undefined
                };
            }, [props.attributes.avatar_image_2] ).avatar_image_2;
            

            props.avatar_image_3 = useSelect(function( select ) {
                return {
                    avatar_image_3: props.attributes.avatar_image_3.id ? select('core').getMedia(props.attributes.avatar_image_3.id) : undefined
                };
            }, [props.attributes.avatar_image_3] ).avatar_image_3;
            
            
            const innerBlocksProps = null;
            
            
            return el(Fragment, {}, [
                el('header', { ...blockProps }, [' ', el('div', { className: 'absolute bg-black/40 inset-0' }), ' ', el('div', { className: 'relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' }, [' ', el('div', { className: 'py-20 sm:py-24 lg:py-32' }, [' ', el('div', { className: 'max-w-3xl' }, [' ', el('div', { className: 'bg-white/80 backdrop-blur rounded-xl shadow-lg ring-1 ring-white/50 p-6 sm:p-8 lg:p-10' }, [el('span', { className: 'inline-flex items-center gap-2 text-sm sm:text-base text-gray-700' }, [' ', el('span', { className: 'inline-flex h-2.5 w-2.5 rounded-full bg-green-500' }), ' ', el(RichText, { tagName: 'span', value: propOrDefault( props.attributes.badge_text, 'badge_text' ), onChange: function(val) { setAttributes( {badge_text: val }) }, withoutInteractiveFormatting: true, allowedFormats: [] }), ' ']), ' ', el(RichText, { tagName: 'h1', className: 'mt-3 text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-tight text-gray-900', value: propOrDefault( props.attributes.main_heading, 'main_heading' ), onChange: function(val) { setAttributes( {main_heading: val }) }, withoutInteractiveFormatting: true, allowedFormats: [] }), ' ', el(RichText, { tagName: 'p', className: 'mt-4 text-base sm:text-lg lg:text-xl text-gray-700 leading-relaxed', value: propOrDefault( props.attributes.description, 'description' ), onChange: function(val) { setAttributes( {description: val }) } }), ' ', el('div', { className: 'mt-6 flex flex-col sm:flex-row gap-3 sm:gap-4' }, [el(RichText, { tagName: 'a', href: propOrDefault( props.attributes.primary_button_link.url, 'primary_button_link', 'url' ), className: 'inline-flex items-center justify-center px-5 py-3 text-white bg-green-600 hover:bg-green-700 rounded-md shadow-sm text-sm sm:text-base', onClick: function(e) { e.preventDefault(); }, value: propOrDefault( props.attributes.primary_button_label, 'primary_button_label' ), onChange: function(val) { setAttributes( {primary_button_label: val }) }, withoutInteractiveFormatting: true, allowedFormats: [] }), el(RichText, { tagName: 'a', href: propOrDefault( props.attributes.secondary_button_link.url, 'secondary_button_link', 'url' ), className: 'inline-flex items-center justify-center px-5 py-3 text-green-700 bg-white hover:bg-gray-50 rounded-md shadow-sm ring-1 ring-gray-200 text-sm sm:text-base', onClick: function(e) { e.preventDefault(); }, value: propOrDefault( props.attributes.secondary_button_label, 'secondary_button_label' ), onChange: function(val) { setAttributes( {secondary_button_label: val }) }, withoutInteractiveFormatting: true, allowedFormats: [] }), ' ']), ' ', el('div', { className: 'mt-5 flex items-center gap-4' }, [' ', el('div', { className: 'flex -space-x-2' }, [' ', props.attributes.avatar_image_1 && props.attributes.avatar_image_1.svg && pgGetFeature4("pgCreateSVG")(RawHTML, {}, pgGetFeature4("pgMergeInlineSVGAttributes")(propOrDefault( props.attributes.avatar_image_1.svg, 'avatar_image_1', 'svg' ), { className: 'h-8 w-8 rounded-full ring-2 ring-white shadow' })), props.attributes.avatar_image_1 && !props.attributes.avatar_image_1.svg && propOrDefault( props.attributes.avatar_image_1.url, 'avatar_image_1', 'url' ) && el('img', { src: propOrDefault( props.attributes.avatar_image_1.url, 'avatar_image_1', 'url' ), alt: propOrDefault( props.attributes.avatar_image_1?.alt, 'avatar_image_1', 'alt' ), className: 'h-8 ring-2 ring-white rounded-full shadow w-8 ' + (props.attributes.avatar_image_1.id ? ('wp-image-' + props.attributes.avatar_image_1.id) : '') }), ' ', props.attributes.avatar_image_2 && props.attributes.avatar_image_2.svg && pgGetFeature4("pgCreateSVG")(RawHTML, {}, pgGetFeature4("pgMergeInlineSVGAttributes")(propOrDefault( props.attributes.avatar_image_2.svg, 'avatar_image_2', 'svg' ), { className: 'h-8 w-8 rounded-full ring-2 ring-white shadow' })), props.attributes.avatar_image_2 && !props.attributes.avatar_image_2.svg && propOrDefault( props.attributes.avatar_image_2.url, 'avatar_image_2', 'url' ) && el('img', { src: propOrDefault( props.attributes.avatar_image_2.url, 'avatar_image_2', 'url' ), alt: propOrDefault( props.attributes.avatar_image_2?.alt, 'avatar_image_2', 'alt' ), className: 'h-8 ring-2 ring-white rounded-full shadow w-8 ' + (props.attributes.avatar_image_2.id ? ('wp-image-' + props.attributes.avatar_image_2.id) : '') }), ' ', props.attributes.avatar_image_3 && props.attributes.avatar_image_3.svg && pgGetFeature4("pgCreateSVG")(RawHTML, {}, pgGetFeature4("pgMergeInlineSVGAttributes")(propOrDefault( props.attributes.avatar_image_3.svg, 'avatar_image_3', 'svg' ), { className: 'h-8 w-8 rounded-full ring-2 ring-white shadow' })), props.attributes.avatar_image_3 && !props.attributes.avatar_image_3.svg && propOrDefault( props.attributes.avatar_image_3.url, 'avatar_image_3', 'url' ) && el('img', { src: propOrDefault( props.attributes.avatar_image_3.url, 'avatar_image_3', 'url' ), alt: propOrDefault( props.attributes.avatar_image_3?.alt, 'avatar_image_3', 'alt' ), className: 'h-8 ring-2 ring-white rounded-full shadow w-8 ' + (props.attributes.avatar_image_3.id ? ('wp-image-' + props.attributes.avatar_image_3.id) : '') }), ' ']), ' ', el(RichText, { tagName: 'p', className: 'text-sm text-gray-700', value: propOrDefault( props.attributes.social_proof_text, 'social_proof_text' ), onChange: function(val) { setAttributes( {social_proof_text: val }) }, withoutInteractiveFormatting: true, allowedFormats: [] }), ' ']), ' ']), ' ']), ' ']), ' ']), ' ']),                        
                
                    el( InspectorControls, {},
                        [
                            
                        pgGetFeature4("pgMediaImageControl")('background_image', setAttributes, props, 'full', true, 'Background Image', '' ),
                                        
                        pgGetFeature4("pgMediaImageControl")('avatar_image_1', setAttributes, props, 'full', true, 'Avatar Image 1', '' ),
                                        
                        pgGetFeature4("pgMediaImageControl")('avatar_image_2', setAttributes, props, 'full', true, 'Avatar Image 2', '' ),
                                        
                        pgGetFeature4("pgMediaImageControl")('avatar_image_3', setAttributes, props, 'full', true, 'Avatar Image 3', '' ),
                                        
                            el(Panel, {},
                                el(PanelBody, {
                                    title: __('Block properties')
                                }, [
                                    
                                    el(TextControl, {
                                        value: props.attributes.badge_text,
                                        help: __( '' ),
                                        label: __( 'Badge Text' ),
                                        onChange: function(val) { setAttributes({badge_text: val}) },
                                        type: 'text'
                                    }),
                                    el(TextControl, {
                                        value: props.attributes.main_heading,
                                        help: __( '' ),
                                        label: __( 'Main Heading' ),
                                        onChange: function(val) { setAttributes({main_heading: val}) },
                                        type: 'text'
                                    }),
                                    el(BaseControl, {
                                        help: __( '' ),
                                        label: __( 'Description' ),
                                    }, [
                                        el(RichText, {
                                            value: props.attributes.description,
                                            style: {
                                                    border: '1px solid black',
                                                    padding: '6px 8px',
                                                    minHeight: '80px',
                                                    border: '1px solid rgb(117, 117, 117)',
                                                    fontSize: '13px',
                                                    lineHeight: 'normal'
                                                },
                                            onChange: function(val) { setAttributes({description: val}) },
                                        })
                                    ]),
                                    pgGetFeature4("pgUrlControl")('primary_button_link', setAttributes, props, 'Primary Button Link', '', null ),
                                    el(TextControl, {
                                        value: props.attributes.primary_button_label,
                                        help: __( '' ),
                                        label: __( 'Primary Button Label' ),
                                        onChange: function(val) { setAttributes({primary_button_label: val}) },
                                        type: 'text'
                                    }),
                                    pgGetFeature4("pgUrlControl")('secondary_button_link', setAttributes, props, 'Secondary Button Link', '', null ),
                                    el(TextControl, {
                                        value: props.attributes.secondary_button_label,
                                        help: __( '' ),
                                        label: __( 'Secondary Button Label' ),
                                        onChange: function(val) { setAttributes({secondary_button_label: val}) },
                                        type: 'text'
                                    }),
                                    el(TextControl, {
                                        value: props.attributes.social_proof_text,
                                        help: __( '' ),
                                        label: __( 'Social Proof Text' ),
                                        onChange: function(val) { setAttributes({social_proof_text: val}) },
                                        type: 'text'
                                    }),    
                                ])
                            )
                        ]
                    )                            

            ]);
        },

        save: function(props) {
            return null;
        }                        

    } );
} )(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor
);                        
