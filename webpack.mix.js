const mix = require('laravel-mix');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

mix.js('resources/js/app.js', 'public/js')
   .vue()
   .sass('resources/sass/app.scss', 'public/css')
   .webpackConfig({
      resolve: {
         alias: {
            'vue$': 'vue/dist/vue.esm-bundler.js',
         },
      },
      plugins: [new VueLoaderPlugin()],
   });
