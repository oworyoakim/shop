const mix = require('laravel-mix');
const path = require('path');

mix.webpackConfig(webpack => {
    return {
        resolve: {
            extensions: ["*", ".js", ".vue"],
            alias: {
                '@': path.resolve(__dirname, 'resources/js/')
            }
        },
    }
})

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */


// Landlord App
mix.js('resources/js/landlord/app.js', 'public/js/landlord.js').vue({version: 2})
    .sass('resources/js/landlord/sass/app.scss', 'public/css/landlord.css');


// Tenant Admin App
mix.js('resources/js/tenant/admin/app.js', 'public/js/admin.js').vue({version: 2})
    .sass('resources/js/tenant/admin/sass/app.scss', 'public/css/admin.css');


// Tenant Manager App
mix.js('resources/js/tenant/manager/app.js', 'public/js/manager.js').vue({version: 2})
    .sass('resources/js/tenant/manager/sass/app.scss', 'public/css/manager.css');


// Tenant Pos App
mix.js('resources/js/tenant/pos/app.js', 'public/js/app.js').vue({version: 2})
    .sass('resources/js/tenant/pos/sass/app.scss', 'public/css/app.css');




// Auth App
mix.js('resources/js/auth/app.js', 'public/js/auth.js').vue({version: 2})
    .sass('resources/js/auth/sass/app.scss', 'public/css/auth.css');




if (mix.inProduction()) {
    mix.version();
}
