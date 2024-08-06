import './bootstrap.js';
import * as Turbo from '@hotwired/turbo';
import 'flowbite/dist/flowbite.turbo.min.js';

import './styles/app.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

// following two event listeners are for handling form submissions within a turbo frame
// see https://symfonycasts.com/screencast/turbo/server-frame-redirect
// in short: form submissions which will respond with a location redirect response after
// success, do not lead to a redirect of the whole page by default.
document.addEventListener('turbo:before-fetch-request', (event) => {
    // Turbo-Frame header is added to any ajax request which happens inside a frame
    const frameId = event.detail.fetchOptions.headers['Turbo-Frame'];
    if (!frameId) {
        return;
    }

    // get the DOM frame element and check if our custom data attribute is set
    const frame = document.getElementById(frameId)
    if (!frame || !frame.dataset.turboFormRedirect) {
        return;
    }

    // if so, add a custom header so that symfony can detect it and transform the response
    event.detail.fetchOptions.headers['Turbo-Frame-Redirect'] = 1;
});

document.addEventListener('turbo:before-fetch-response', (event) => {
    const fetchResponse = event.detail.fetchResponse;
    const redirectLocation = fetchResponse.response.headers.get('Turbo-Location');
    if (!redirectLocation) {
        return;
    }
    event.preventDefault();
    Turbo.cache.clear();
    Turbo.visit(redirectLocation);
});