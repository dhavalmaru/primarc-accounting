<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/datatables.min.css',
        // 'css/site.css',
        // 'css/custom.css',
        // 'css/dashboard.css',
        'js/jquery-ui-1.11.2/jquery-ui.min.css',

        'bootstrap/css/bootstrap.min.css',
        'css/updated_css.css',

        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css',
        'dist/css/AdminLTE.min.css',
        'dist/css/skins/_all-skins.min.css',
        'plugins/iCheck/flat/blue.css',
        'plugins/jvectormap/jquery-jvectormap-1.2.2.css',
        'plugins/datepicker/datepicker3.css',
        'plugins/daterangepicker/daterangepicker.css',
        'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',

        'dist/css/jquery.dataTables.css',
        'dist/css/dataTables.fixedColumns.css',
    ];
    public $js = [
        // 'js/jquery/jquery.min.js',
        // 'js/jquery/jquery-ui.min.js',
        // 'js/plugins/moment.min.js',
        // 'js/jquery-ui-1.11.2/jquery-ui.min.js',

        // 'js/plugins/validationengine/languages/jquery.validationEngine-en.js',
        // 'js/plugins/validationengine/jquery.validationEngine.js',
        
        'plugins/jQuery/jquery-2.2.3.min.js',
        'https://code.jquery.com/ui/1.11.4/jquery-ui.min.js',
        'bootstrap/js/bootstrap.min.js',

        // 'js/datatables.min.js',
        'js/custom.js',

        'js/plugins/jquery-validation/jquery.validate.js',
        'js/validations.js',
        
            
            'plugins/jvectormap/jquery-jvectormap-1.2.2.min.js',
            'plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
            'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
            'plugins/slimScroll/jquery.slimscroll.min.js',
            'plugins/fastclick/fastclick.js',

            'dist/js/app.min.js',

            'dist/js/jquery.dataTables.js',
            'dist/js/dataTables.fixedColumns.js', 
            // 'js/fixed_clmn.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
