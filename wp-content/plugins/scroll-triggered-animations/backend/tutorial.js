jQuery(window).ready(function(){

function toast_sta_tutorial(step){
if(! jQuery('#wpcontent').find('.toast-sta-tutorial-active').length >= 1){
jQuery('#wpcontent').append('<div class="toast-sta-tutorial-active">');
}

jQuery('.class-list, .advanced-animations-input, table, .demo-css, input[type="submit"], .example-area').removeAttr('style');
jQuery('.tutorial-step').remove();
jQuery('form').attr('action', 'options.php');

var nextStep = step + 1;
var previousStep = step - 1;

function stepNavigation(){
if(step != 5 || step != 1){
var html = '<div class="sta-tutorial-navigation"><a class="previous"><< Back</a><a class="next">Next >></a><span class="close-tutorial dashicons dashicons-no-alt"></span></div>';
}
if(step == 1){
var html = '<div class="sta-tutorial-navigation"><a class="next">Next >></a><span class="close-tutorial dashicons dashicons-no-alt"></span></div>';
}
if(step == 5){
var html = '<div class="sta-tutorial-navigation"><a class="previous"><< Back</a><a class="end-tutorial">End Tutorial</a><span class="close-tutorial dashicons dashicons-no-alt"></span></div>';
}

return html;
}

if(step == 1){
step1();
}
if(step == 2){
step2();
}
if(step == 3){
step3();
}
if(step == 4){
step4();
}
if(step == 5){
step5();
}

function step1(){
window.location.hash = 'tutorialstep1';
jQuery('.toast-metabox, .tab').removeClass('active');
jQuery('.toast-metabox[data-tab="default-classes"], .tab[data-tab="default-classes"]').addClass('active');
jQuery('.toplevel_page_toast_sta_items .toast-metabox').css({'overflow':'visible'});
jQuery('.example-area').css({'z-index': 99999});
jQuery('.standard-animations .class-list').css({'position': 'relative', 'overflow': 'visible', 'z-index': 99999});
jQuery('.standard-animations .class-list').append('<div class="tutorial-step"><h3>A quick tutorial!</h3> To add a default animation to your site. Simply choose a class and add it to an element in your page backend. The element will animate on the frontend of your site with the default speeds and delays. <br><br> <strong>Note: You can hover over any of the classes to view a demo in the demo box to the right.</strong>'+stepNavigation()+'</div>')
}

function step2(){
window.location.hash = 'tutorialstep2';
jQuery('.toast-metabox, .tab').removeClass('active');
jQuery('.toast-metabox[data-tab="default-classes"], .tab[data-tab="default-classes"]').addClass('active');
jQuery('.premium-controls, .toplevel_page_toast_sta_items .toast-metabox').css({'overflow': 'visible'})
jQuery('.premium-controls .class-list').css({'position': 'relative', 'overflow': 'visible', 'z-index': 99999});
jQuery('.premium-controls .class-list').append('<div class="tutorial-step"><h3>Controlling speeds and delays</h3>Controlling speeds and delays is also easy with these default classes. Add any of these classes below to change the speeds of your animations. These must be used in combination with the default animation class above.<br><br> <strong>Note: This is a premium feature, consider upgrading to premium to gain this control.</strong>'+stepNavigation()+'</div>')
}

function step3(){
window.location.hash = 'tutorialstep3';
jQuery('.toast-metabox, .tab').removeClass('active');
jQuery('.toast-metabox[data-tab="advanced"], .tab[data-tab="advanced"]').addClass('active');
jQuery('.toplevel_page_toast_sta_items .toast-metabox').css({'overflow':'visible'});
jQuery('.advanced-animations-input').css({'position': 'relative', 'overflow': 'visible', 'z-index': 99999});
jQuery('.advanced-animations-input').append('<div class="tutorial-step"><h3>Start making your own animations</h3>Advanced animations are a little trickier to get your head around but follow these simple steps and they\'ll be working in no time.<br><br> Insert the <strong>Class, ID, or CSS selector</strong> here and click "Activate Element" to begin. Once thats done click next.'+stepNavigation()+'</div>')
}

function step4(){
window.location.hash = 'tutorialstep4';
jQuery('.toast-metabox, .tab').removeClass('active');
jQuery('form').attr('action', 'options.php#tutorialstep4');
jQuery('.toast-metabox[data-tab="advanced"], .tab[data-tab="advanced"]').addClass('active');
jQuery('input[type="submit"], .demo-css').css({'z-index': 99999, 'position': 'relative'});
jQuery('.toplevel_page_toast_sta_items .toast-metabox').css({'overflow':'visible'});
jQuery('textarea[name="toast_sta_settings[toast_sta_animations_css]"]').parents('table').css({'position': 'relative', 'overflow': 'visible', 'z-index': 99999});
jQuery('textarea[name="toast_sta_settings[toast_sta_animations_css]"]').parents('table').append('<div class="tutorial-step"><h3>Creating your advanced animation</h3> Activating an element simply adds a class called "scroll-triggered" when it is due to animate (when it enters your screen dimensions).<br><br> With this knowledge we can target the element before and after it enters the viewport. Copy and paste the demo CSS supplied (to the left) into the textbox and switch "button" for the <strong>Class, ID, or CSS selector</strong> you activated in the step before. <br><br> Click "Save Changes" and find the element on the frontend of your site. You\'ll see that it fades in when it enters the viewport. If you have an understanding of CSS you\'ll know that this gives you a great amount of customisability to create your own animations on a global scope. <br><br> <strong>Note: You dont need to add your CSS here, you can add it within your theme CSS file if you prefer.</strong>'+stepNavigation()+'</div>')
}

function step5(){
window.location.hash = 'tutorialstep5';
jQuery('.toast-metabox, .tab').removeClass('active');
jQuery('.toast-metabox[data-tab="standard"], .tab[data-tab="standard"]').addClass('active');
jQuery('.toplevel_page_toast_sta_items .toast-metabox').css({'overflow':'visible'});
jQuery('input[name="toast_sta_settings[toast_animate_on_page_load]"]').parents('table').css({'position': 'relative', 'overflow': 'visible', 'z-index': 99999});
jQuery('select[name="toast_sta_settings[toast_sta_position_start]"]').parents('td').css({'position': 'relative'});
jQuery('select[name="toast_sta_settings[toast_sta_position_start]"]').parents('td').append('<div class="tutorial-step"><h3>Awesome controls for your animations</h3>Here you can customise the settings for your animations. We\'ve provided descriptions underneath each option to help you undestand exactly what everything does. <br><br> That completes your tutorial but if you still need help with things you can read through the <a href="https://www.toastplugins.co.uk/documentation/scroll-triggered-animations" target="_blank">documentation here</a> or <a href="https://wordpress.org/support/plugin/scroll-triggered-animations/" target="_blank">contact support</a>. <br><br> Thankyou for downloading Scroll Triggered Animations and good luck animating your site!'+stepNavigation()+'</div>')
}

jQuery('.sta-tutorial-navigation a').on('click', function(){
if(jQuery(this).hasClass('next')){
toast_sta_tutorial(nextStep);
}
if(jQuery(this).hasClass('previous')){
toast_sta_tutorial(previousStep);
}

if(jQuery(this).hasClass('end-tutorial')){
jQuery('.toast-sta-tutorial-active').remove();
jQuery('.class-list, .advanced-animations-input, table').removeAttr('style');
jQuery('.tutorial-step').remove();
window.location.hash = ''
}

})

}

if(document.cookie.indexOf('sta_page_loaded_once') != -1){
	if(document.cookie.indexOf('sta_tutorial_complete') != -1){
	}else{
		document.cookie = 'sta_tutorial_complete=1';
		toast_sta_tutorial(1);
	}
}else{
document.cookie = 'sta_page_loaded_once=1';
}

jQuery('.start-tutorial').on('click', function(){
toast_sta_tutorial(1);
})

if(window.location.hash == '#tutorialstep1'){
toast_sta_tutorial(1);
}
if(window.location.hash == '#tutorialstep2'){
toast_sta_tutorial(2);
}
if(window.location.hash == '#tutorialstep3'){
toast_sta_tutorial(3);
}
if(window.location.hash == '#tutorialstep4'){
toast_sta_tutorial(4);
}
if(window.location.hash == '#tutorialstep5'){
toast_sta_tutorial(5);
}
jQuery('body').on('click','.close-tutorial', function(){
jQuery('.tutorial-step, .toast-sta-tutorial-active').remove();
window.location.hash = ''
})

})