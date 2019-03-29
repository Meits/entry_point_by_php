/**
 * Created by MeitsWorkPc on 19.02.2019.
 */
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/index.sass', 'public/css/style.css');