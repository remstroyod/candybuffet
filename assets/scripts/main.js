/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================
 * ================================================== */

(function ($) {
    // Use this variable to set up the common and page specific functions. If you
    // rename this variable, you will also need to rename the namespace below.
    var Sage = {
        // All pages
        'common': {
            init: function () {

            },
            finalize: function () {
                $('.js__form').on('validation-submit', function () {
                    var formData = new FormData(this);

                    $(this).find('button').prop('disabled', true);

                    $.ajax({
                        url: settings.ajaxurl,
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                          if (response && response.success) {
                            response.data.file && window.open(response.data.file, 'window');
                          }
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                });
            }
        },
        'home': {
            init: function () {
                var $flickity = $('.promo-slider');

                $flickity
                    .on('ready.flickity dragEnd.flickity change.flickity', function (e) {
                        $(e.target).find('.is-selected video').each(function () {
                            this.play();
                        });
                    })
                    .on('dragStart.flickity', function (e) {
                        var video = $(e.target).find('video').each(function () {
                            this.pause();
                        });
                    });
            }
        },
        'single_product': {
            init: function () {
                $('#modal-help-taste')
                    .on('show.bs.modal', UTIL.showBsModal)
                    .on('hidden.bs.modal', UTIL.closeBsModal);

                $('.select2-product_attr_taste').on('select2:select', function (event) {
                    var id = event.params.data.element.dataset.id;
                    if (typeof id !== 'undefined') {
                      $('.tab-content .taste.active').removeClass('active').parent().find('.taste-id-' + id).addClass('active');
                    }
                });

                function onResize() {
                    const seo_element = document.querySelector('.info-content');
                    if (!seo_element) {
                      return;
                    }

                    if ($(window).width() < 1000 && !document.querySelector('.card > .info-content')) {
                        document.querySelector('.card > .card__image').appendChild(seo_element);
                    } else if ($(window).width() >= 1000 && !document.querySelector('.page-wrap__content > .info-content')) {
                        document.querySelector('.page-wrap__content').appendChild(seo_element);
                    }
                }

              $(window).on('resize', onResize);
              onResize();
            }
        },
        'page_cart': {
            finalize: function () {
              let instance = null;

              const stickySubNav = function () {
                if (instance === null) {
                  instance = $('.cart-box__widgets').fixTo('.cart-box', {
                    mind: 'header',
                    useNativeSticky: false
                  });
                }

                if ($(window).width() > 768) {
                  instance.fixTo('start');
                } else {
                   instance.fixTo('stop');
                }
              };

              stickySubNav();

              $(window).resize(stickySubNav);
            }
        },
        'page_template_template_tastes': {
            finalize: function () {
                $('.tiles-box__nav ul').fixTo('.tiles-box__inner', {
                    mind: 'header',
                    useNativeSticky: false
                });
            }
        }
    };

    // The routing fires all common scripts, followed by the page specific scripts.
    // Add additional events for more control over timing e.g. a finalize event
    var UTIL = {
        showBsModal: function () {
            function _getScrollbarSize() {
                if (window.scrollbarSize === undefined) {
                    var scrollDiv = document.createElement("div");
                    scrollDiv.style.cssText = 'width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;';
                    document.body.appendChild(scrollDiv);
                    window.scrollbarSize = scrollDiv.offsetWidth - scrollDiv.clientWidth;
                    document.body.removeChild(scrollDiv);
                }
                return window.scrollbarSize;
            }

            function _hasScrollBar() {
                return document.body.scrollHeight > $(window).height();
            }

            if (_hasScrollBar()) {
                var s = _getScrollbarSize();
                if (s) {
                    $('body').css({height: 'auto'});
                    $('html').css({marginRight: s, overflow: 'hide'});
                }
            }
        },
        closeBsModal: function () {
            $('html').css({marginRight: '', overflow: ''});
        },
        fire: function (func, funcname, args) {
            var fire;
            var namespace = Sage;
            funcname = (funcname === undefined) ? 'init' : funcname;
            fire = func !== '';
            fire = fire && namespace[func];
            fire = fire && typeof namespace[func][funcname] === 'function';

            if (fire) {
                namespace[func][funcname](args);
            }
        },
        loadEvents: function () {
            // Fire common init JS
            UTIL.fire('common');

            // Fire page-specific init JS, and then finalize JS
            $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function (i, classnm) {
                UTIL.fire(classnm);
                UTIL.fire(classnm, 'finalize');
            });

            // Fire common finalize JS
            UTIL.fire('common', 'finalize');
        }
    };

    // Load Events
    $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
