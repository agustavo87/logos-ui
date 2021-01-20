const mix = require('laravel-mix');
require('./resources/js/quill/quill.mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss')
    ])
    .editor(__dirname)
    .js('resources/js/logos.js', 'public/js')
    .css('resources/css/logos.css', 'public/css')
    .copyDirectory('resources/images/copy', 'public/images' )
    .sourceMaps()
    .browserSync('localhost:8000');
