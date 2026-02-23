/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
jQuery(document).ready(function($){
    $.zita = {
        init: function () {
            this.focusForCustomShortcut();
        },
        focusForCustomShortcut: function (){
            var fakeShortcutClasses = [
                'zita-main-header',
                'zita-blog-single',
                'zita-blog-archive',
                'zita-widget-footer',
                'zita-bottom-footer',
                'zita-scroll-to-top-section',
                'zita-social-icon'
                
            ];
            fakeShortcutClasses.forEach(function (element){
                $('.customize-partial-edit-shortcut-'+ element).on('click',function (){
                   wp.customize.preview.send( 'zita-customize-focus-section', element );
                });
            });
        }
    };
    $.zita.init();
    // color
    // $.thShopMania = {
    //     init: function () {
    //         this.focusForCustomShortcutColor();
    //     },
    //     focusForCustomShortcutColor: function (){
    //         var fakeShortcutClasses = [
    //             'zita-above-header-color',
    //             'zita-below-header-color',
    //             'zita-main-header-header-color',
    //             'zita-main-header-header-menu-color',
    //             'zita-footer-above-color',
    //             'ita-footer-widget-color',
    //             'zita-footer-bottom-color',
    //             'zita-color-sidebar-group'
    //         ];
    //         fakeShortcutClasses.forEach(function (element){
    //             $('.customize-partial-edit-shortcut-'+ element).on('click',function (){
    //                wp.customize.preview.send( 'zita-customize-focus-color-section', element );
    //             });
    //         });
    //     }
    // };
    // $.thShopMania.init();
});