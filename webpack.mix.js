let mix = require('laravel-mix');

const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const source = 'platform/plugins/' + directory;
const dist = 'public/vendor/core/plugins/' + directory;

mix.sass(`${source}/resources/sass/ai-content.scss`, `${dist}/css`)
    .js(source + '/resources/js/ai-content.js', dist + '/js');

if (mix.inProduction()) {
    mix.copy(`${dist}/css/ai-content.css`, `${source}/public/css`)
        .copy(dist + '/js/ai-content.js', source + '/public/js');
}
