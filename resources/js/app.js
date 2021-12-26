require('./bootstrap');

window.axios.defaults.baseURL = function () {
    let url = document.getElementById("base_url")?.href
    return url || '/'
}()

import {createInertiaApp} from '@inertiajs/inertia-svelte'
import {InertiaProgress} from '@inertiajs/progress'

// Force reload on popstate.
/*window.addEventListener("popstate", (e) => {
    e.stopImmediatePropagation()
    location.reload()
})*/

createInertiaApp({
    resolve: name => require(`./Pages/${name}.svelte`),
    setup({el, App, props}) {
        new App({target: el, props})
    },
})

InertiaProgress.init()
