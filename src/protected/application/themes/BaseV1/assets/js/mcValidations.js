var McValidations = (function () {

    function showError(errorKey, $errorTag, labels) {
        $errorTag.html(labels[errorKey]).show();
        return false;
    }

    function requireField(value, errorKey, $errorTag, labels) {
        if (!value) return showError(errorKey, $errorTag, labels);
        return true;
    }

    function requireHttps(url, errorKey, $errorTag, labels) {
        if (!url.startsWith('https://')) return showError(errorKey, $errorTag, labels);
        return true;
    }

    function isValidUrl(url, errorKey, $errorTag, labels) {
        if (!url) return showError(errorKey, $errorTag, labels);

        try {
            new URL(url);
            return true;
        } catch {
            return showError(errorKey, $errorTag, labels);
        }
    }

    function isValidYouTube(url, errorKey, $errorTag, labels) {
        let parsed;
        try {
            parsed = new URL(url);
        } catch {
            return false;
        }

        const host = parsed.hostname.replace(/^www\./, '');
        const youtubeHosts = ['youtube.com', 'youtu.be'];

        if (!youtubeHosts.some(h => host.endsWith(h))) {
            return false;
        }

        let videoId = null;

        if (host.endsWith('youtu.be')) {
            videoId = parsed.pathname.slice(1);
        } else if (host.endsWith('youtube.com')) {
            videoId = parsed.searchParams.get('v');
        }

        if (!videoId || !/^[a-zA-Z0-9_-]{11}$/.test(videoId)) {
            return false;
        }

        return true;
    }

    function isValidVimeo(url, errorKey, $errorTag, labels) {
        let parsed;
        try {
            parsed = new URL(url);
        } catch {
            return false;
        }

        const host = parsed.hostname.replace(/^www\./, '');
        const path = parsed.pathname;

        if (!host.endsWith('vimeo.com')) {
            return false;
        }

        if (!/\d+/.test(path)) {
            return false;
        }

        return true;
    }

    return {
        showError,
        requireField,
        requireHttps,
        isValidUrl,
        isValidYouTube,
        isValidVimeo
    };

})();
