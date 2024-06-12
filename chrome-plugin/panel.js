let counter = 1;

window.addEventListener('zaffius-bam-event', (e) => {

    const fullPath = e.detail.fullPath;
    const div = document.getElementById(e.detail.id);

    div.addEventListener('mouseover', (e) => {
        e.target.style.textDecoration = 'underline';
    })
    div.addEventListener('mouseout', (e) => {
        e.target.style.textDecoration = 'none';
    })
    div.addEventListener('click', () => {
        const request = new XMLHttpRequest();
        request.open('GET', fullPath, true);
        request.send();
    })

})

chrome.devtools.network.onRequestFinished.addListener(
    function (request) {
        if (request._resourceType === 'xhr') {

            let {url, method} = request.request;

            const domain = url.split('/').splice(0, 3).join('/')

            url = url.split('?')[0] // Remove query params
            url = url.split('/').splice(3).join('/')

            // not myself
            if (url === 'api/zaffius-bam') return;

            const fullPath = `${domain}/api/zaffius-bam?url=${encodeURI(url)}&method=${method}`

            const div = document.createElement('div');
            div.setAttribute('style', "display:flex;");

            const id = `zaffius-bam-${counter}`;
            div.innerHTML = `
                <div style="width:50px;">${method}</div>
                <div id="${id}"
                     style="width:700px;cursor:pointer;">${url}</div>`;

            document.getElementById('container').prepend(div);

            window.dispatchEvent(
                new CustomEvent('zaffius-bam-event', {
                    detail: {id,fullPath}
                }));

        }

    }
)
;


