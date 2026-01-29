$(document).ready(function() {
    
    "use strict";
    
    // Options
    var submenu_animation_speed = 200,
        submenu_opacity_animation = true,
        page_sidebar_fixed = false,
        page_header_fixed = false;
    
    // Elements
    var body = $('body'),
        page_header = $('.page-header'),
        page_sidebar = $('.page-sidebar'),
        page_content = $('.page-content');
    
    
    // Fixed Header
    var fixed_header = function() {
        if(page_header_fixed == true) {
            $('body').addClass('page-header-fixed');
        };
    };
    fixed_header();
    
    if(body.hasClass('page-header-fixed')) {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 30) {
                $('.page-header').addClass('changeBg')
            }
            if ($(this).scrollTop() < 30) {
                $('.page-header').removeClass('changeBg')
            }
        });
    };
    
    
    // Sidebar
    var page_sidebar_init = function() {
        
        // Slimscroll
        $('.secondary-sidebar-menu').slimScroll({
            height: '100%'
        });
        $('.secondary-sidebar-menu').mouseover();  
        
        // Fixed Sidebar
        var fixed_sidebar = function() {
            if((body.hasClass('page-sidebar-fixed'))&&(page_sidebar_fixed == false)) {
                page_sidebar_fixed = true;
            };
            
            if(page_sidebar_fixed == true) {
                body.addClass('page-sidebar-fixed');
                $('#fixed-sidebar-toggle-button').removeClass('icon-radio_button_unchecked');
                $('#fixed-sidebar-toggle-button').addClass('icon-radio_button_checked');
            };
            
            var fixed_sidebar_toggle = function() {
                body.toggleClass('page-sidebar-fixed');
                if(body.hasClass('page-sidebar-fixed')) {
                    page_sidebar_fixed = true;
                } else {
                    page_sidebar_fixed = false;
                }
            };
    
            $('#fixed-sidebar-toggle-button').on('click', function() {
                fixed_sidebar_toggle();
                $(this).toggleClass('icon-radio_button_unchecked');
                $(this).toggleClass('icon-radio_button_checked');
                return false;
            });
        };
        
        
        var small_screen_sidebar = function(){
            $('#sidebar-toggle-button').on('click', function() {
                body.toggleClass('page-sidebar-visible');
                return false;
            });
            $('#sidebar-toggle-button-close').on('click', function() {
                body.toggleClass('page-sidebar-visible');
                return false;
            });
            
            $(window).click(function() {
                if($(body).hasClass('page-sidebar-visible')) {
                    body.toggleClass('page-sidebar-visible');
                }
            });

            $('.page-sidebar').click(function(event){
                event.stopPropagation();
            });
            
            $('.secondary-sidebar').click(function(event){
                event.stopPropagation();
            });

            
        };
        
        var sidebar_settings = function(){
            $('.settings-menu-link').on('click', function(){
                $('body').addClass('settings-visible');
            });
            
            $('.settings-overlay, .settings-menu-close').on('click', function(){
                $('body').removeClass('settings-visible');
            });
        };
        
        var collapsed_sidebar = function(collapseLink){
            $(collapseLink).on('click', function(){
                $('body').toggleClass('collapsed-sidebar');
            });
        };
        
        collapsed_sidebar('#collapsed-sidebar-toggle-button');
        
        fixed_sidebar();
        small_screen_sidebar();
        sidebar_settings();
        
    };
    
    var compactMQL = window.matchMedia('(min-width: 767px) and (max-width: 1200px)');
    var hiddenMQL = window.matchMedia('(min-width: 0px) and (max-width: 767px)');
    
    function compactSidebar(size) {
        if(size.matches) {
            $(body).addClass('collapsed-sidebar');
        } 
    }
    
    
    // Responsive
    
    function showSideSmallScreen(btn) {
        btn.on('click', function(){
            $(body).toggleClass('hidden-sidebar-show');
            alert('asd');
        });
    }
    
    function hiddenSidebar(size) {
        if(size.matches) {
            $(body).addClass('hidden-sidebar');
        } else {
            $(body).removeClass('hidden-sidebar');
        }
    }
    
    compactMQL.addListener(compactSidebar);
    compactSidebar(compactMQL);
    
    hiddenMQL.addListener(hiddenSidebar);
    hiddenSidebar(hiddenMQL);
    
    var sidebarBtn = $('#sidebar-toggle');
    
    showSideSmallScreen(sidebarBtn);
    
    
    // Accordion menu
    var accordion_menu = function() {
        
        var select_sub_menus = $('.accordion-menu li:not(.open) .sub-menu'),
            active_page_sub_menu_link = $('.accordion-menu li.active-page > a');
        
        // Hide all sub-menus
        select_sub_menus.hide();
        
        
        if(submenu_opacity_animation == false) {
            $('.sub-menu li').each(function(i){
                $(this).addClass('animation');
            });
        };
        
        // Accordion
        $('.accordion-menu li a').on('click', function() {
            var sub_menu = $(this).next('.sub-menu'),
                parent_list_el = $(this).parent('li'),
                active_list_element = $('.accordion-menu > li.open'),
                show_sub_menu = function() {
                    sub_menu.slideDown(submenu_animation_speed);
                    parent_list_el.addClass('open');
                    if(submenu_opacity_animation === true) {
                        $('.open .sub-menu li').each(function(i){
                            var t = $(this);
                            setTimeout(function(){ t.addClass('animation'); }, (i+1) * 25);
                        });
                    };
                },
                hide_sub_menu = function() {
                    if(submenu_opacity_animation === true) {
                        $('.open .sub-menu li').each(function(i){
                            var t = $(this);
                            setTimeout(function(){ t.removeClass('animation'); }, (i+1) * 15);
                        });
                    };
                    sub_menu.slideUp(submenu_animation_speed);
                    parent_list_el.removeClass('open');
                },
                hide_active_menu = function() {
                    $('.accordion-menu > li.open > .sub-menu').slideUp(submenu_animation_speed);
                    active_list_element.removeClass('open');
                };
            
            if(sub_menu.length) {
                
                if(!parent_list_el.hasClass('open')) {
                    if(active_list_element.length) {
                        hide_active_menu();
                    };
                    show_sub_menu();
                } else {
                    hide_sub_menu();
                };
                
                return false;
                
            };
        });
        
        if($('.active-page > .sub-menu').length) {
            active_page_sub_menu_link.click();
        };
    };

    
    // Fulscreen Function
    function toggleFullScreen() {
        if (!document.fullscreenElement &&    // alternative standard method
            !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.msRequestFullscreen) {
                document.documentElement.msRequestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
                document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }
    };
    
    // Navbar
    var navbar_init = function(){
        
        $('#toggle-fullscreen').on('click', function(){ 
            toggleFullScreen();
            return false;
            
        });
        
        $('#search-button').on('click', function(){
            body.toggleClass('search-open')
            if(body.hasClass('search-open')) {
                $('.search-form input').focus();
            }
        });
        
        $('#close-search').on('click', function(){
            body.toggleClass('search-open')
        });
        
    };
    
    // Right Sidebar
    var right_sidebar = function(){
        $('.right-sidebar-toggle').on('click', function(){
            var sidebarId = $(this).data("sidebar-id");
            $('#' + sidebarId).toggleClass('visible');
        });
        
        var write_message = function(){
            $(".chat-write form input").on('keypress', function (e) {
                if ((e.which == 13)&&(!$(this).val().length == 0)) {
                    // Security: Create elements safely to prevent XSS
                    let chatText = $(this).val();
                    if($('.right-sidebar-chat .chat-bubbles .chat-bubble:last-child').hasClass('me')) {
                        let span = $('<span class="chat-bubble-text"></span>').text(chatText);
                        span.insertAfter(".right-sidebar-chat .chat-bubbles .chat-bubble:last-child span:last-child");
                    } else {
                        let bubble = $('<div class="chat-bubble me"><div class="chat-bubble-text-container"><span class="chat-bubble-text"></span></div></div>');
                        bubble.find('.chat-bubble-text').text(chatText);
                        bubble.insertAfter(".right-sidebar-chat .chat-bubbles .chat-bubble:last-child");
                    };
                    $(this).val('');
                } else if(e.which == 13) {
                    return;
                }
                var scrollTo_int = $('.right-sidebar-chat').prop('scrollHeight') + 'px';
                $('.right-sidebar-chat').slimscroll({
                    allowPageScroll: true,
                    scrollTo : scrollTo_int
                });
            });
        };
        write_message();
    };
    
    // Plugins
    var plugins_init = function(){
        // Slimscroll
        $('.slimscroll').slimScroll();
        
        
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            var switchery = new Switchery(html, {size: 'small', color: '#637282'});
        });
        
        $('[data-toggle="tooltip"]').tooltip();
        
        $('[data-toggle="modal"]').click(function(e){ e.preventDefault();});
        
        $('[data-toggle="popover"]').popover();
        
        $('.toast').toast();
        
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
        
        var imageCrop = function(){
            if($(".image-crop").length) {
                var $image = $(".image-crop > img");

                $image.cropper({
                    aspectRatio: 7 / 5,
                    preview: ".img-preview"
                });

                $("#zoomIn").on('click', function() {
                    $image.cropper('zoom', 0.1);
                });

                $("#zoomOut").on('click', function() {
                    $image.cropper('zoom', -0.1);
                });

                $("#rotateLeft").on('click', function() {
                    $image.cropper('rotate', 45);
                });

                $("#rotateRight").on('click', function() {
                    $image.cropper('rotate', -45);
                });

                $("#clear").on('click', function() {
                    $image.cropper('clear');
                });

                var $replaceWith = $('#replaceWith');
                $('#replace').click(function () {
                    $image.cropper('replace', $replaceWith.val());
                });
            }
        }
        
        imageCrop();
    
    };
    
    page_sidebar_init();
    accordion_menu();
    navbar_init();
    right_sidebar();
    plugins_init();
    
});