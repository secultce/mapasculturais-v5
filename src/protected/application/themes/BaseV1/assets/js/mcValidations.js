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
        return !!MapasCulturais.Video.getYoutubeID(url);
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
