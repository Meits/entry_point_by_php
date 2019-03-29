/**
 * Created by MeitsWorkPc on 19.02.2019.
 */

require('materialize-css');

window.Vue = require('vue');

if(document.getElementById('app')) {
    const app = new Vue({
            el: '#app',
            render: h => h(App)
    });
}

M.AutoInit();