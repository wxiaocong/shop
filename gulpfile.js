var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */
var lib = 'public/lib/';
var css = 'public/css/';
var fonts = 'public/fonts/';
var images = 'public/images/';
var js = 'public/js/';
var imageAdmins = 'public/images/admins';
var imageUsers = 'public/images/users';

elixir.config.sourcemaps = false;

// libs 
// 1. jquery
elixir(function(mix) {
    mix
        .copy('bower_components/jquery/dist/jquery.min.js', lib + 'jquery/');
});

// 2. jquery-cookie
elixir(function(mix) {
    mix
        .copy('bower_components/jquery-cookie/jquery.cookie.js', lib + 'jquery-cookie/');
});

// 3. jquery-validation
elixir(function(mix) {
    mix
        .copy('bower_components/jquery-validation/dist/*.min.js', lib + 'jquery-validation/');
});

// 4. bootstrap
elixir(function(mix) {
    mix
        .copy('bower_components/bootstrap/dist/css/bootstrap.min.css', lib + 'bootstrap/css/')
        .copy('bower_components/bootstrap/dist/fonts/*.*', lib + 'bootstrap/fonts/')
        .copy('bower_components/bootstrap/dist/js/bootstrap.min.js', lib + 'bootstrap/js/')
        .copy('bower_components/bootstrap/js/tooltip.js', lib + 'bootstrap/js/')
        .copy('bower_components/bootstrap/js/popover.js', lib + 'bootstrap/js/');
});

//adminlte
elixir(function(mix) {
    mix
        .copy('bower_components/admin-lte/dist/css/AdminLTE.min.css', lib + 'admin-lte/css/')
        .copy('bower_components/admin-lte/dist/css/skins/_all-skins.min.css', lib + 'admin-lte/css/skins/')
        .copy('bower_components/admin-lte/dist/js/adminlte.min.js', lib + 'admin-lte/js/');
});


// 5. eonasdan-bootstrap-datetimepicker
elixir(function(mix) {
    mix
        .copy('bower_components/eonasdan-bootstrap-datetimepicker/build/css/*.min.css', lib + 'datetimepicker/css/')
        .copy('bower_components/eonasdan-bootstrap-datetimepicker/build/js/*.min.js', lib + 'datetimepicker/js/');
});

// 6. moment
elixir(function(mix) {
    mix
        .copy('bower_components/moment/min/moment.min.js', lib +'moment/')
        .copy('bower_components/moment/locale/zh-cn.js', lib +'moment/locale/moment-zh-cn.js');
});

// 7. html5shiv and response
elixir(function(mix) {
    mix
        .copy('bower_components/html5shiv/dist/html5shiv-printshiv.min.js', lib + 'html5shiv/')
        .copy('bower_components/respond/dest/respond.min.js', lib + 'respond/');

});

// 8. jsencrypt
elixir(function(mix) {
    mix
        .copy('bower_components/jsencrypt/bin/jsencrypt.min.js', lib + 'jsencrypt/');
});

// 10. ueditor
elixir(function(mix) {
    mix
        .copy('resources/assets/js/common/ueditor', 'public/js/common/ueditor');
});

// 13. jquery-mousewheel
elixir(function(mix) {
    mix
        .copy('bower_components/jquery-mousewheel/jquery.mousewheel.js', lib + 'jquery-mousewheel/');
});

// 15. jquery-file-upload
elixir(function(mix) {
    mix
        .copy('bower_components/blueimp-file-upload/js/jquery.fileupload.js', lib + 'blueimp-file-upload/js/')
        .copy('bower_components/jquery.ui/ui/widget.js', lib + 'jquery.ui/')
        .copy('bower_components/blueimp-file-upload/css/jquery.fileupload.css', lib + 'blueimp-file-upload/css/')
        .copy('bower_components/jquery.ui/ui/sortable.js', lib + 'jquery.ui/')
        .copy('bower_components/jquery.ui/ui/mouse.js', lib + 'jquery.ui/')
        .copy('bower_components/jquery.ui/ui/core.js', lib + 'jquery.ui/');
});

// 16. uploadify
elixir(function(mix) {
    mix
        .copy('bower_components/uploadify/jquery.uploadify.js', lib + 'uploadify/');
});

// custom settings.
// 2.1 copy images
elixir(function(mix) {
    mix.copy('resources/assets/images', images);
});
elixir(function(mix) {
    mix.copy('resources/assets/images/admins', imageAdmins);
});
elixir(function(mix) {
    mix.copy('resources/assets/images/users', imageUsers);
});

// 2.2 copy fonts
elixir(function(mix) {
    mix.copy('resources/assets/fonts/', fonts);
});

// 2.3 custom css 
/*elixir(function(mix) {
    mix.less('51gai.less');
});*/

elixir(function(mix) {
    mix.styles('font-awesome.css', 'public/css/font-awesome.css');
});

elixir(function(mix) {
    mix
        .copy('resources/assets/js/', 'public/js/')
        .copy('resources/assets/css/', 'public/css/');
});

// 3. version
elixir(function(mix) {
    mix.version([
        'css/font-awesome.css',
        'css/admins/admin.css',
        'css/users/address.css',
        'css/users/aui.css',
        'css/users/cart.css',
        'css/users/home.css',
        'css/users/main.css',
        'css/users/order.css',
        'css/users/purchase.css',

        'js/52gai.js',
        'js/admins/login.js',
        'js/admins/model.js',
        'js/admins/goodCategory.js',
        'js/admins/good.js',
        'js/admins/editGood.js',
        'js/admins/goodSpeed.js',
        'js/admins/order.js',
        'js/admins/adPosition.js',
        'js/admins/adminRight.js',
        'js/admins/adminUser.js',
        'js/admins/adminRole.js',
        'js/common/fileUpload.js',
        'js/common/h5FileUpload.js',
        'js/common/datetimePicker.js',
        'js/users/city-picker.js',
        'js/users/front.js',
        'js/users/goods-detail.js',
        'js/users/login.js',
        'js/users/purchase.js',
        'js/users/cart-purchase.js',
    ]);
});