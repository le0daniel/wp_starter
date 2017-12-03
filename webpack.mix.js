/* Config */
const theme_root_path = __dirname + '/web/app/themes'
const relative_theme_root_path = 'web/app/themes'
/* %ThemeName% */

/* Helpers */
const assets_to_watch = (...parts)=>{

    let base = theme_root_path + '/' + theme_name + '/';
    let to_watch = []

    parts.map(pattern => {
        to_watch.push(base+pattern)
    });

    return to_watch;
};
const theme_path =(...parts)=>{
    return theme_root_path + '/' + theme_name + '/' + parts.join('/')
};

/* Constants */
const mix = require('laravel-mix');
mix.setPublicPath(relative_theme_root_path + '/' + theme_name + '/static')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');

mix.webpackConfig({
    plugins: [
        new BrowserSyncPlugin({
            files: [].concat(
                assets_to_watch('resources/assets/**/*','resources/views/**/*')
            )
        }),
    ],
    output:{
        chunkFilename: 'chunks/[name].bundle.js',
        publicPath: '/app/themes/'+ theme_name +'/static/'
    }
});
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.js(
        /* From */ theme_path('resources/assets/js/script.js'),
        /* To   */ theme_path('static')
    )
    .sass(
        /* From */ theme_path('resources/assets/scss/style.scss'),
        /* To   */ theme_path('static/style.css')
    )
    .version();
