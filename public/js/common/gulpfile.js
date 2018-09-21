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

// 9. select2
elixir(function(mix) {
    mix
        .copy('bower_components/select2/dist/css/*.min.css', lib + 'select2/css/')
        .copy('bower_components/select2/dist/js/select2.min.js', lib + 'select2/js/')
        .copy('bower_components/select2/dist/js/i18n/zh-CN.js', lib + 'select2/js/i18n/');
});

// 10. ueditor
elixir(function(mix) {
    mix
        .copy('resources/assets/js/common/ueditor', 'public/js/common/ueditor');
});


// 11. dataTables
elixir(function(mix) {
    mix.copy('bower_components/datatables.net-bs/css/*.css', lib + 'datatables/css/')
    mix.copy('bower_components/datatables.net-bs/js/*.js', lib + 'datatables/js/')
    mix.copy('bower_components/datatables.net/js/*.js', lib + 'datatables/');
});

// 12. echarts
elixir(function(mix) {
    mix
        .copy('bower_components/echarts/build/dist/echarts.js', lib + 'echarts/')
        .copy('bower_components/echarts/build/dist/chart/bar.js', lib + 'echarts/chart/')
        .copy('bower_components/echarts/build/dist/chart/line.js', lib + 'echarts/chart/')
        .copy('bower_components/echarts/build/dist/chart/pie.js', lib + 'echarts/chart/')
        .copy('bower_components/echarts/build/dist/chart/funnel.js', lib + 'echarts/chart/')
        .copy('bower_components/echarts/build/dist/chart/map.js', lib + 'echarts/chart/');
});

// 13. jquery-mousewheel
elixir(function(mix) {
    mix
        .copy('bower_components/jquery-mousewheel/jquery.mousewheel.js', lib + 'jquery-mousewheel/');
});

// 14. jquery-jstree
elixir(function(mix) {
    mix
        .copy('bower_components/jstree/dist/themes/default/style.min.css', lib + 'jstree/themes/default/')
        .copy('bower_components/jstree/dist/themes/default/*.png', lib + 'jstree/themes/default/')
        .copy('bower_components/jstree/dist/themes/default/*.gif', lib + 'jstree/themes/default/')
        .copy('bower_components/jstree/dist/jstree.min.js', lib + 'jstree/js/')
        .copy('bower_components/jstree/dist/jstree.js', lib + 'jstree/js/');
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

// custom settings.
// 2.1 copy images
elixir(function(mix) {
    mix.copy('resources/assets/images/', images);
});

// 2.2 copy fonts
elixir(function(mix) {
    mix.copy('resources/assets/fonts/', fonts);
});

// 2.3 custom css 
elixir(function(mix) {
    mix.less('newjiyou.less');
});

elixir(function(mix) {
    mix.styles('login.css', 'public/css/login.css');
    mix.styles('vehicleTypeChoose.css', 'public/css/vehicleTypeChoose.css');
    mix.styles('font-awesome.css', 'public/css/font-awesome.css');
});

elixir(function(mix) {
    mix.copy('resources/assets/js/', 'public/js/');
});

// 3. version
elixir(function(mix) {
    mix.version([
        'css/newjiyou.css',
        'css/font-awesome.css',
        'css/login.css',
        'css/vehicleTypeChoose.css',
        
        'js/newjiyou.js',
        'js/login.js',
        'js/resetPassword.js',
        'js/goodCategoryTree.js',

        //htmldiff
        'public/js/htmldiff/htmldiff.js',
        
        // common
        'public/js/common/address.js',
        'public/js/common/checkAll.js',
        'public/js/common/datetimePicker.js',
        'public/js/common/vehicleTypeChoose.js',
        'public/js/common/goodBrandChoose.js',
        'public/js/common/shopGoodChoose.js',
        'public/js/common/admin-lte/js/app.min.js',
        'public/js/common/initEcharts.js',
        'public/js/common/vehicleType.js',
        'public/js/common/goodBrandAndCategory.js',
        'public/js/common/fileUpload.js',
        'public/js/common/province.js',
        'public/js/common/echarts.min.js',

        'public/js/crm/returnVisit.js',
        'public/js/crm/returnVisitAdd.js',
        'public/js/crm/returnVisits.js',
        'public/js/crm/followTask.js',
    
        'public/js/wiki/wikiCategory.js',
        'public/js/wiki/wikiCategoryTree.js',
        'public/js/wiki/wikiArticle.js',
        'public/js/wiki/wikiFileInput.js',

        'public/js/customer/customer.js',
        'public/js/customer/onlineShop.js',
        'public/js/customer/shop.js',
        'public/js/customer/invoice.js',
        'public/js/customer/changeLog.js',
        'public/js/customer/contract.js',

        'public/js/user/role.js',
        'public/js/user/permission.js',
        'public/js/user/user.js',

        'public/js/statistical/receiptCustomer.js',
        'public/js/statistical/receiptFollow.js',
        'public/js/statistical/receiptFollowComment.js',

        'public/js/product/productCategory.js'
    ]);
});