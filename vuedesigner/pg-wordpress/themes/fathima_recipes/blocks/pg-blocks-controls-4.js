(function() {

function pgPostSelectorControl(prop, setAttributes, props, title, help, post_type) {
    const {__} = wp.i18n;
    const el = wp.element.createElement;
    const {Button, Popover, BaseControl, Panel, PanelBody, PanelRow} = wp.components;
    const {useState} = wp.element;
    const LinkControl = wp.blockEditor.LinkControl || wp.blockEditor.__experimentalLinkControl;

    title = title || __('Select ' + prop);

    let [isVisible, setIsVisible] = useState(false);
    const toggleVisible = () => {
        setIsVisible((state) => !state);
    };

    const findItemById = function (id) {
        if (props.attributes[prop]) {
            for (let i = 0; i < props.attributes[prop].length; i++) {
                if (props.attributes[prop][i].id === id) return i;
            }
        }
        return -1;
    }

    const moveItem = function (id, add) {
        const idx = findItemById(id);
        if (idx >= 0) {
            if (idx + add >= 0 && (idx + add) < props.attributes[prop].length) {
                const list = props.attributes[prop].slice(0);
                const mi = list[idx];
                list.splice(idx, 1);
                list.splice(idx + add, 0, mi)
                const d = {}
                d[prop] = list;
                setAttributes(d);
            }
        }
    }

    const renderItems = function () {
        var r = [];
        if (props.attributes[prop]) {
            let i = 0;
            props.attributes[prop].forEach(function (item) {
                r.push(el('div', {
                    className: 'pg-posts-control-list-item',
                    key: item.id
                }, [
                    props[prop + '_' + item.id] ? props[prop + '_' + item.id].title.raw : 'Loading...',
                    el('a', {
                        href: '#',
                        className: 'pg-posts-control-list-item-remove',
                        onClick: function () {
                            const idx = findItemById(item.id);
                            if (idx >= 0) {
                                const list = props.attributes[prop].slice(0);
                                list.splice(idx, 1);
                                const d = {}
                                d[prop] = list;
                                setAttributes(d);
                            }
                        }
                    }, '×'),
                    el('a', {
                        href: '#',
                        className: 'pg-posts-control-list-item-move up dashicons dashicons-arrow-up-alt2',
                        onClick: function (e) {
                            e.preventDefault();
                            moveItem(item.id, -1);
                        }
                    }, ''),
                    el('a', {
                        href: '#',
                        className: 'pg-posts-control-list-item-move down dashicons dashicons-arrow-down-alt2',
                        onClick: function (e) {
                            e.preventDefault();
                            moveItem(item.id, 1);
                        }
                    }, '')
                ]))
                i++;
            })
        }
        return r;
    }

    const items = props.attributes[prop] || [];

    return el(Panel, {}, el(PanelBody, {
        title: __(title)
    }, [
        el(BaseControl, {
            help: help && __(help)
        }, [
            el(PanelRow, {},
                el('div', {
                    className: 'pg-posts-control-list'
                }, [
                    items.length === 0 && el('div', {
                        className: 'pg-posts-control-list-item-empty'
                    }, [__('No posts selected')]),
                    items.length > 0 && renderItems()
                ])
            ),
            el(PanelRow, {}, [
                el(Button, {
                    onClick: toggleVisible,
                    isSecondary: true
                }, [
                    __('Select a ' + post_type),
                ])
            ])
        ]),
        isVisible && el(Popover, {
            className: 'pg-control-popover'
        }, [
            el('a', {
                href: '#',
                tabIndex: '-1',
                className: 'pg-control-close-popup',
                onClick: function () {
                    setIsVisible(false);
                },
            }, [
                '×'
            ]),
            el(LinkControl, {
                onChange: function (val, p) {
                    if (findItemById(val.id) < 0) {
                        var d = {};
                        d[prop] = props.attributes[prop].slice(0);
                        d[prop].push({id: val.id});
                        setAttributes(d);
                    }
                    setIsVisible(false);
                },
                noDirectEntry: true,
                searchInputPlaceholder: __('Select a post'),
                showInitialSuggestions: true,
                settings: [],
                hasRichPreviews: true,
                suggestionsQuery: post_type ? {
                    type: 'post',
                    subtype: post_type,
                } : {}
            }),
        ])
    ]))
}

function pgMediaImageControl(prop, setAttributes, props, image_size, inline_svg, title, help) {
    const propMedia = prop;

    const {__} = wp.i18n;
    const el = window.wp.element.createElement;
    const {
        BaseControl,
        Button,
        ResponsiveWrapper,
        Panel,
        PanelBody,
        PanelRow,
        TextareaControl,
        Popover
    } = wp.components;
    const {MediaUploadCheck, MediaUpload} = wp.blockEditor;
    const {RawHTML, useState} = wp.element;

    image_size = image_size || 'full';

    let [isEditSVGVisible, setIsEditSVGVisible] = useState({
        visible: false,
        value: ''
    });

    function onSelectMedia(media) {
        var d = {}
        d[prop] = {
            id: media.id,
            url: media?.sizes[image_size]?.url || media.url,
            size: image_size,
            svg: '',
            alt: media.alt || ''
        }
        setAttributes(d);
    }

    function setSVG(svg) {
        var d = {}
        d[prop] = {
            id: 0,
            url: '',
            size: '',
            svg: inline_svg ? svg : '',
            alt: ''
        }
        setAttributes(d);
    }

    function removeMedia() {
        var d = {}
        d[prop] = {
            id: 0,
            url: '',
            size: '',
            svg: '',
            alt: ''
        }
        setAttributes(d);
    }

    function selectMediaSize(url, size) {
        var d = {};
        d[prop] = {
            id: props[prop].id,
            url: url,
            size: size,
            svg: '',
            alt: props[prop].alt || ''
        }
        setAttributes(d);
    }

    function getId() {
        return props.attributes[prop].id || 0;
    }

    function getUrl() {
        return props.attributes[prop].url || '';
    }

    function getAlt() {
        return props.attributes[prop].alt || '';
    }

    function getSVG() {
        if (!inline_svg) return '';
        return props.attributes[prop].svg || '';
    }

    function getResolutionOptions() {
        const sizes = props[propMedia]?.media_details?.sizes;
        if (!sizes) return [];
        return Object.keys(sizes).map((k) => ({
            value: k,
            label: `${k} (${sizes[k].width}x${sizes[k].height})`
        }));
    }

    const chosenUrl = getUrl();
    const previewUrl = chosenUrl || (props[propMedia] && props[propMedia].source_url);

    return el(Panel, {}, el(PanelBody, {
        title: __(`${title || prop}`)
    }, [
        el(PanelRow, {}, [
            el(MediaUploadCheck, {}, [
                el(MediaUpload, {
                    onSelect: onSelectMedia,
                    value: getId(),
                    allowedTypes: ['image'],
                    render: (open) => el(Button, {
                        className: getUrl() === '' ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview',
                        style: {
                            maxHeight: '200px',
                            overflow: 'hidden'
                        },
                        onClick: open.open
                    }, [
                        (getUrl() === '' && getSVG() === '') && __('Select an image'),
                        !getSVG() && props[propMedia] && props[propMedia].source_url &&
                        el(ResponsiveWrapper, {
                                naturalWidth: props[propMedia].media_details.width,
                                naturalHeight: props[propMedia].media_details.height,
                                isInline: true
                            },
                            el('img', {
                                src: previewUrl,
                                style: {
                                    objectFit: 'contain'
                                }
                            })
                        ),
                        getId() === 0 && getUrl() && el('img', {
                            src: getUrl(),
                            style: {
                                width: '100%',
                                display: 'block',
                                objectFit: 'contain',
                                height: '100%',
                                objectPosition: 'center center'
                            }
                        }),
                        inline_svg && getSVG() && el('div', {
                            className: 'pg-control-svg-edit-preview'
                        }, el(RawHTML, {}, getSVG()))
                    ])

                }, []),
            ]),
        ]),

        inline_svg && !getUrl() && !getSVG() && el(PanelRow, {}, [
            el(Button, {
                onClick: function () {
                    setIsEditSVGVisible({visible: true, value: getSVG()});
                },
                style: {
                    marginRight: '4px',
                },
                isLink: true
            }, [
                __('Set inline SVG')
            ]),
        ]),

        (getUrl() || getSVG()) && el(PanelRow, {}, [
            el(MediaUploadCheck, {}, [
                el(MediaUpload, {
                    title: __('Replace image'),
                    onSelect: onSelectMedia,
                    value: getId(),
                    allowedTypes: ['image'],
                    render: (open) => el(Button, {
                        onClick: open.open,
                        isSecondary: true
                    }, [
                        __('Replace image'),
                    ])

                }, []),
            ]),
            inline_svg && el(Button, {
                onClick: function () {
                    setIsEditSVGVisible({visible: true, value: getSVG()});
                },
                style: {
                    marginRight: '6px',
                    marginLeft: '6px'
                },
                isSecondary: true
            }, [
                __('Edit SVG')
            ]),
            el(Button, {
                onClick: removeMedia,
                isLink: true,
                isDestructive: false
            }, [
                __('Remove image')
            ])
        ]),

        getId() !== 0 && props[propMedia] && pgImageSizeControl(prop, setAttributes, props, getResolutionOptions(), function (value) {
                selectMediaSize(props[propMedia].media_details.sizes[value].source_url, value);
            }),

        isEditSVGVisible.visible && el(Popover, {
            className: 'pg-control-svg-edit',
            onClose: () => setIsEditSVGVisible({ ...isEditSVGVisible, visible: false }),
            style: {}
        }, [
            el(BaseControl, {}, [
                el(TextareaControl, {
                    value: getSVG(),
                    help: __('Paste or edit the SVG image code.'),
                    label: __('Edit inline SVG'),
                    className: 'pg-control-svg-edit-code',
                    onChange: function (val) {
                        //setIsEditSVGVisible((s) => ({ ...s, value: val }));
                        setSVG(val);
                    },
                }),
            ]),
            el(Button, {
                onClick: function () {
                    setIsEditSVGVisible({...isEditSVGVisible, visible: false});
                },
                style: {
                    marginRight: '6px'
                },
                isSecondary: true
            }, [
                __('Close')
            ]),
            el(Button, {
                onClick: function () {
                    setSVG(isEditSVGVisible.value);
                    setIsEditSVGVisible({...isEditSVGVisible, visible: false});
                },
                isLink: true
            }, [
                __('Cancel')
            ]),
        ]),
        help && el(BaseControl, {
            help: __(help),
        }, [])
    ]))
}

function pgColorControl(prop, setAttributes, props, title, help) {
    const {__} = wp.i18n;
    const el = wp.element.createElement;
    const {Button, Popover, BaseControl, ColorPicker, PanelRow} = wp.components;
    const {useState} = wp.element;

    let [isVisible, setIsVisible] = useState(false);

    const toggleVisible = () => {
        setIsVisible((state) => !state);
    };

    return el(BaseControl, {
        help: help && __(help)
    }, [
        el(PanelRow, {}, [
            el(Button, {
                onClick: toggleVisible,
                isSecondary: true
            }, [
                el('span', {}, __(title || 'Select color')),
                el('span', {
                    style: {
                        backgroundColor: props.attributes[prop],
                        border: '1px solid rgba(0,0,0,0.15)',
                        width: '20px',
                        height: '20px',
                        borderRadius: '50%',
                        marginLeft: '8px'
                    }
                })
            ]),
            props.attributes[prop] !== '' && el(Button, {
                onClick: function () {
                    var d = {}
                    d[prop] = '';
                    setAttributes(d);
                },
                isLink: true,
                isDestructive: false
            }, [
                __('Clear')
            ]),
            isVisible && el(Popover, {
                className: 'pg-control-color-popover'
            }, [
                el('a', {
                    href: '#',
                    tabIndex: -1,
                    className: 'pg-control-close-popup',
                    onClick: function () {
                        setIsVisible(false);
                    },
                }, [
                    '×'
                ]),
                el(ColorPicker, {
                    color: props.attributes[prop],
                    label: __(prop),
                    onChange: function (value) {
                        setAttributes({ [prop]: value });
                    },
                    onClose: function (value) {
                        setAttributes({ [prop]: value });
                    },
                    onChangeComplete: function (value) {
                        var d = {}
                        d[prop] = value.color ? value.color.toRgbString() : ('rgba(' + value.rgb.r + ',' + value.rgb.g + ',' + value.rgb.b + ',' + value.rgb.a + ')');
                        setAttributes(d);
                    }
                }),
            ])
        ]),
    ])
}

function pgUrlControl(prop, setAttributes, props, title, help, post_type) {
    const {__} = wp.i18n;
    const el = wp.element.createElement;
    const {Button, Popover, BaseControl, Panel, PanelBody, PanelRow, SelectControl, Icon} = wp.components;
    const {useState} = wp.element;
    const LinkControl = wp.blockEditor.LinkControl || wp.blockEditor.__experimentalLinkControl;
    const {withSelect, useSelect} = wp.data;

    title = title || prop;

    let linkControlKey = 1;

    let [isVisible, setIsVisible] = useState(false);
    const toggleVisible = () => {
        setIsVisible((state) => !state);
    };

    const getPostTypeOptions = useSelect((select) => {
        const {getPostTypes} = select('core');

        const items = (getPostTypes() || []).filter(function (item) {
            return !item.slug.startsWith('wp_');
        });

        const options = items.map(function (item) {
            return {
                label: item.labels.name,
                value: item.slug,
            };
        });

        options.unshift({value: null, label: '-'});
        return options;
    });

    return el(BaseControl, {
        help: help && __(help),
        label: __(title || prop)
    }, [
        el(PanelRow, {}, [
            el(Button, {
                onClick: toggleVisible,
                isSecondary: true,
                style: {
                    flexGrow: 1,
                    marginRight: '8px'
                }
            }, [
                el(Icon, {icon: 'admin-links', style: {marginRight: '4px'}}),
                !props.attributes[prop].url && el('span', {}, __('Select link')),
                props.attributes[prop].url && el('span', {
                    style: {
                        maxWidth: '170px',
                        display: 'inline-block',
                        textOverflow: 'ellipsis',
                        overflow: 'hidden'
                    }
                }, props.attributes[prop].title || props.attributes[prop].url),
                props.attributes[prop].url && el(Button, {
                    onClick: function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        var d = {}
                        d[prop] = {
                            post_type: props.attributes[prop].post_type || null,
                            post_id: 0,
                            url: ''
                        };
                        setAttributes(d);
                    },
                    isLink: true,
                    isDestructive: false,
                    style: {
                        fontSize: '16px',
                        textDecoration: 'none',
                        marginLeft: 'auto'
                    }
                }, [
                    __('×')
                ]),
            ]),

        ]),
        isVisible && el(Popover, {
            onClose: () => setIsVisible(false)
        }, [
            el('div', {
                style: {
                    margin: '0 16px 16px 16px'
                }
            }, [
                el('a', {
                    href: '#',
                    style: {
                        right: '-15px'
                    },
                    tabIndex: -1,
                    className: 'pg-control-close-popup',
                    onClick: function () {
                        setIsVisible(false);
                    },
                }, [
                    '×'
                ]),
                el(SelectControl, {
                    value: props.attributes[prop].post_type,
                    label: __('Post type'),
                    disabled: post_type != null,
                    onChange: function (val) {
                        var d = {}
                        d[prop] = {
                            post_type: val === '-' ? null : val,
                            post_id: 0,
                            url: ''
                        };
                        linkControlKey++;
                        //setIsVisible(false);
                        setAttributes(d)
                    },
                    options: getPostTypeOptions
                }),
            ]),
            el(LinkControl, {
                key: props.attributes[prop].post_type || 'none',
                value: {
                    url: props.attributes[prop].url
                },
                onChange: function (val) {
                    var d = {};
                    d[prop] = {
                        post_type: props.attributes[prop].post_type,
                        post_id: val.id,
                        url: val.url,
                        title: val.title
                    };
                    if (val.url === props.attributes[prop].url && !val.id) {
                        d[prop].post_id = props.attributes[prop].post_id;
                        d[prop].title = props.attributes[prop].title;
                    }
                    setIsVisible(false);
                    setAttributes(d)
                },
                noDirectEntry: false,
                searchInputPlaceholder: __('Select a post'),
                showInitialSuggestions: true,
                forceIsEditingLink: true,
                settings: [],
                suggestionsQuery: props.attributes[prop].post_type ? {
                    type: 'post',
                    subtype: props.attributes[prop].post_type,
                } : {}
            }),
        ])
    ])

}

function pgImageSizeControl(prop, setAttributes, props, options, onChange ) {
    const {__} = wp.i18n;
    const el = wp.element.createElement;
    const {BaseControl, PanelRow, SelectControl} = wp.components;

    const title = __('Image size');

    return el(PanelRow, {}, [el(BaseControl, {
    }, [
        el(SelectControl, {
            label: title,
            value: props.attributes[prop].size,
            options: options,
            onChange: onChange
        })
        ])
    ])
}

function pgMergeInlineSVGAttributes(svg, props) {
    for (let prop in props) {
        let val = props[prop];
        if (typeof val === 'object') {
            const r = [];
            for (const key in val) {
                r.push(`${key.replace(/([A-Z])/g, function (a) {
                    return '-' + a.toLowerCase();
                })}:${val[key]};`)
            }
            val = r.join('');
        }
        if (prop === 'className') prop = 'class';
        const re = new RegExp('(<svg[^>]*\\s*)(' + prop + '="[^"]*")', 'i');
        if (svg.match(re)) {
            const safeVal = String(val).replace(/"/g, '&quot;');
            svg = svg.replace(re, '$1' + prop + '="' + safeVal + '"');
        } else {
            svg = svg.replace(/<svg/, '<svg ' + prop + '="' + val + '"')
        }
    }
    return svg;
}

class PgHTMLToReact {
    constructor() {
    }

    loadHTML(code) {
        const parser = new DOMParser();
        this.doc = parser.parseFromString(code, "text/html");
        this.removeScripts(this.doc);

        var svgs = this.doc.querySelectorAll('svg');
        for (let i = 0; i < svgs.length; i++) {
            this.cleanNode(svgs[i]);
        }
    }

    cleanNode(node) {
        if (node.attributes) {
            const remove = [];
            for (let i = 0; i < node.attributes.length; i++) {
                let name = node.attributes[i].name;
                if (name.startsWith('on')) remove.push(name);
            }
            remove.forEach(function (a) {
                node.removeAttribute(a);
            })
        }
        for (let i = 0; i < node.childNodes.length; i++) {
            this.cleanNode(node.childNodes[i]);
        }
    }

    attributesToProps(node) {
        const props = {}
        for (let i = 0; i < node.attributes.length; i++) {
            let name = node.attributes[i].name;
            if (name.startsWith('on')) continue;

            if (name === 'class') {
                name = 'className';
            }
            if (name === 'style') {
                const a = (node.attributes[i].value || '').split(';');
                const styles = {}
                a.forEach(function (p) {
                    const b = p.split(':');
                    if (b.length > 1) {
                        const sp = b.shift().trim().replace(/-([a-z])/gi, function (s, g) {
                            return g.toUpperCase();
                        })
                        styles[sp] = b.join(':').trim();
                    }
                })
                props.style = styles;
            } else {
                props[name] = node.attributes[i].value === null ? '' : node.attributes[i].value;
            }
        }
        return props;
    }

    removeScripts(node) {
        var scripts = node.querySelectorAll('script');
        for (let i = 0; i < scripts.length; i++) {
            scripts[i].parentNode.removeChild(scripts[i]);
        }
    }

    render(el, lib) {
        const _this = this;

        el = el || window.wp.element.createElement;
        lib = lib || {}

        const doNode = function (node) {
            if (node.nodeType === 1) {

                let tag = node.tagName.toLowerCase();
                const props = _this.attributesToProps(node);

                const children = [];
                let skip_children = false;

                if (tag === 'svg') {
                    props.dangerouslySetInnerHTML = {__html: node.innerHTML}
                    skip_children = true;
                }

                if (!skip_children) {
                    for (let i = 0; i < node.childNodes.length; i++) {
                        const ch = doNode(node.childNodes[i]);
                        ch && children.push(ch);
                    }
                }
                if (lib[tag]) {
                    tag = lib[tag];
                }

                if (children.length) {
                    return el(tag, props, children);
                } else {
                    return el(tag, props);
                }
            } else if (node.nodeType === 3) {
                return node.data;
            }
        }
        const nodes = Array.from(this.doc.body.childNodes)
            .map((n) => doNode(n))
            .filter(Boolean);

        return nodes.length === 1 ? nodes[0] : el(wp.element.Fragment, {}, nodes);
    }
}



const pgCreateSVGCache = {}

function pgCreateSVG(dummytag, dummyprops, svg) {
    const pgReact = new PgHTMLToReact();
    const el = window.wp.element.createElement;

    let props = pgCreateSVGCache[svg] || null;
    if (!props) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(svg, "image/svg+xml");

        const svgel = doc.querySelector('svg');
        pgReact.removeScripts(doc);
        if(svgel) {
            pgReact.cleanNode(svgel);
            props = pgReact.attributesToProps(svgel);
            props.dangerouslySetInnerHTML = {__html: svgel.innerHTML}
        } else {
            props = {}
            props.dangerouslySetInnerHTML = {__html: '<text x="0" y="0" font-size="10" fill="red">Invalid SVG</text>'}
        }

        pgCreateSVGCache[svg] = props;
    }
    return el('svg', {...props});
}

function PgGetServerSideRender() {

    const { useDebounce, usePrevious } = wp.compose;
    const { RawHTML, useEffect, useRef, useState, Fragment } = wp.element;
    const { __, sprintf } = wp.i18n;
    const apiFetch = wp.apiFetch;
    const { addQueryArgs } = wp.url;
    const { Placeholder, Spinner } = wp.components;

    const useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps || wp.blockEditor.__experimentalUseInnerBlocksProps;

    const el = wp.element.createElement;

    // Adapted from fast-deep-equal (unchanged)
    function equal(a, b) {
        if (a === b) return true;

        if (a && b && typeof a == 'object' && typeof b == 'object') {
            if (a.constructor !== b.constructor) return false;

            var length, i, keys;
            if (Array.isArray(a)) {
                length = a.length;
                if (length != b.length) return false;
                for (i = length; i-- !== 0;)
                    if (!equal(a[i], b[i])) return false;
                return true;
            }

            if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
            if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
            if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();

            keys = Object.keys(a);
            length = keys.length;
            if (length !== Object.keys(b).length) return false;

            for (i = length; i-- !== 0;)
                if (!Object.prototype.hasOwnProperty.call(b, keys[i])) return false;

            for (i = length; i-- !== 0;) {
                var key = keys[i];

                if (key === '_owner' && a.$$typeof) {
                    continue;
                }
                if (!equal(a[key], b[key])) return false;
            }

            return true;
        }
        return a !== a && b !== b;
    }

    function rendererPath(block, attributes = null, urlQueryArgs = {}) {
        return addQueryArgs(`/wp/v2/block-renderer/${block}`, {
            context: 'edit',
            ...(null !== attributes ? { attributes } : {}),
            ...urlQueryArgs,
        });
    }

    // --- Default placeholders (wrapped by a stable outer wrapper in the render function) ---

    function DefaultEmptyResponsePlaceholder({ className }) {
        return el(Placeholder, { className }, __('Block rendered as empty.'));
    }

    function DefaultErrorResponsePlaceholder({ response, className }) {
        const errorMessage = sprintf(
            __('Error loading block: %s'),
            response && response.errorMsg ? response.errorMsg : __('Unknown error')
        );
        return el(Placeholder, { className }, errorMessage);
    }

    function DefaultLoadingResponsePlaceholder({ className, children }) {
        // Shows a spinner and (optionally) previous HTML as children.
        return el(Placeholder, { className }, [
            el(Spinner, {}),
            children
        ]);
    }

    // --- Main component ---

    return function PgServerSideRenderComponent(props) {
        const {
            attributes,
            block,
            className,
            httpMethod = 'GET',
            urlQueryArgs,
            EmptyResponsePlaceholder = DefaultEmptyResponsePlaceholder,
            ErrorResponsePlaceholder = DefaultErrorResponsePlaceholder,
            LoadingResponsePlaceholder = DefaultLoadingResponsePlaceholder,
            innerBlocksProps,
            blockProps
        } = props;

        const isMountedRef = useRef(true);
        const fetchRequestRef = useRef();

        // Keep both response and status, mirroring the official SSR.
        const [response, setResponse] = useState(null);
        const [status, setStatus] = useState('idle');

        // Store last successful HTML so we can keep it visible while loading (SWR).
        const prevContentRef = useRef('');

        const prevProps = usePrevious(props);

        // Data attributes used by the PHP render to mark anchors in the HTML
        const innerAttribute = 'data-wp-inner-blocks';
        const blocksPropsAttribute = 'data-wp-block-props';

        // Sanitize helper
        const SanitizeBlockAttributes =
            wp.blocks.__experimentalSanitizeBlockAttributes ||
            wp.blocks.SanitizeBlockAttributes ||
            ((/*blockName, attrs*/) => attributes);

        // Track whether the current parse inserted blockProps into the tree
        let sawBlockPropsAnchor = false;

        // Element factory that strips scripts/handlers and grafts special anchors.
        function pgel(tag, props, ...children) {
            if (tag === 'script') return null;

            if (props) {
                if (props.class) {
                    props.className = props.class;
                    delete props.class;
                }
                // Remove event handlers
                for (const prop in props) {
                    if (Object.prototype.hasOwnProperty.call(props, prop)) {
                        if (prop.match(/^on[A-Z]+/)) {
                            props[prop] = null;
                        }
                    }
                }
                // Replace special anchors with the provided props
                if (innerAttribute in props) {
                    delete props[innerAttribute];
                    // Mount the InnerBlocks anchor element
                    return el(tag, { ...props, ...innerBlocksProps });
                }
                if (blocksPropsAttribute in props) {
                    delete props[blocksPropsAttribute];
                    sawBlockPropsAnchor = true;
                    return el(tag, { ...props, ...blockProps }, children.length ? children : undefined);
                }
            }
            return children.length ? el(tag, props, children) : el(tag, props);
        }

        function fetchData() {
            isMountedRef.needsFetch = false;
            if (!isMountedRef.current) return;

            setStatus('loading');

            const sanitizedAttributes =
                attributes && SanitizeBlockAttributes
                    ? SanitizeBlockAttributes(block, attributes)
                    : attributes;

            const isPostRequest = httpMethod === 'POST';
            const urlAttributes = isPostRequest ? null : (sanitizedAttributes ?? null);
            const path = rendererPath(block, urlAttributes, urlQueryArgs);
            const data = isPostRequest ? { attributes: sanitizedAttributes ?? null } : null;

            const fetchRequest = (fetchRequestRef.current = apiFetch({
                path,
                data,
                method: isPostRequest ? 'POST' : 'GET',
            })
                .then((fetchResponse) => {
                    if (
                        isMountedRef.current &&
                        fetchRequest === fetchRequestRef.current &&
                        fetchResponse
                    ) {
                        setResponse(fetchResponse.rendered);
                        setStatus('success');
                    }
                })
                .catch((error) => {
                    if (
                        isMountedRef.current &&
                        fetchRequest === fetchRequestRef.current
                    ) {
                        setResponse({
                            error: true,
                            errorMsg: error.message,
                        });
                        setStatus('error');
                    }
                }));

            return fetchRequest;
        }

        const debouncedFetchData = useDebounce(fetchData, 500);

        // Unmount guard
        useEffect(() => {
            return () => {
                isMountedRef.current = false;
                isMountedRef.needsFetch = false;
            };
        }, []);

        // Keep last successful HTML for SWR
        useEffect(() => {
            if (status === 'success' && typeof response === 'string' && response) {
                prevContentRef.current = response;
            }
        }, [status, response]);

        // Trigger fetches
        useEffect(() => {
            // First fetch: do not debounce
            if (prevProps === undefined) {
                fetchData();
            } else if (!equal(prevProps.attributes, props.attributes)) {
                isMountedRef.needsFetch = true;
                debouncedFetchData();
            } else if (
                prevProps.block !== block ||
                prevProps.httpMethod !== httpMethod ||
                !equal(prevProps.urlQueryArgs, urlQueryArgs)
            ) {
                isMountedRef.needsFetch = true;
                debouncedFetchData();
            } else if (isMountedRef.needsFetch) {
                fetchData();
            }
        }, [props.attributes, block, httpMethod, urlQueryArgs]);

        // --- Render states (keep a stable outer wrapper with blockProps at all times) ---

        if (status === 'loading') {
            // Keep previous HTML mounted inside the loading placeholder.
            return el('div', { ...blockProps },
                el(LoadingResponsePlaceholder, { ...props }, [
                    !!prevContentRef.current && el(RawHTML, { className }, prevContentRef.current)
                ])
            );
        }

        if (status === 'success' && response === '') {
            return el('div', { ...blockProps },
                el(EmptyResponsePlaceholder, { ...props })
            );
        }

        if (status === 'error') {
            return el('div', { ...blockProps },
                el(ErrorResponsePlaceholder, { response, ...props })
            );
        }

        // Success with content (string)
        if (typeof response === 'string' && response) {
            try {
                // Cache parse/compile for the current code
                if (isMountedRef.currentCode !== response) {
                    isMountedRef.currentFunction = new PgHTMLToReact();
                    isMountedRef.currentFunction.loadHTML(response);
                    isMountedRef.currentCode = response;
                }

                sawBlockPropsAnchor = false;
                const rendered = isMountedRef.currentFunction.render(pgel);

                // If the server output didn't mark a blockProps anchor,
                // wrap to keep a stable editor root and append InnerBlocks anchor.
                if (!sawBlockPropsAnchor) {
                    return el('div', { ...blockProps }, [
                        rendered,
                        el('div', { ...innerBlocksProps })
                    ]);
                }

                return rendered;
            } catch (err) {
                // Fallback to raw HTML + InnerBlocks if compile fails
                // (also keeps a stable wrapper)
                console.warn('Unable to compile dynamic block to JSX:', err);
                return el('div', { ...blockProps }, [
                    el(RawHTML, { className }, response),
                    el('div', { ...innerBlocksProps })
                ]);
            }
        }

        // Initial/idle (no response yet) — render a stable wrapper with a simple spinner
        return el('div', { ...blockProps },
            el(Placeholder, { className }, el(Spinner, {}))
        );
    };
}

const getFeature = function(key) {
    const map = {
        pgPostSelectorControl: pgPostSelectorControl,
        pgMediaImageControl: pgMediaImageControl,
        pgColorControl: pgColorControl,
        pgUrlControl: pgUrlControl,
        pgImageSizeControl: pgImageSizeControl,
        pgMergeInlineSVGAttributes: pgMergeInlineSVGAttributes,
        PgHTMLToReact: PgHTMLToReact,
        pgCreateSVG: pgCreateSVG,
        PgGetServerSideRender: PgGetServerSideRender
    }

    if(!map[key]) {
        console.error(`[pgGetFeature] Unknown feature ${key}`)
        return null;
    }
    return map[key];
}

window.pgGetFeature4 = getFeature;

})()

