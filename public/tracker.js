(function (window, document) {
    "use strict";

    if (window.__AMOPOINT_TRACKER_LOADED__) {
        return;
    }

    window.__AMOPOINT_TRACKER_LOADED__ = true;

    var version = "1.0.0";
    var script = document.currentScript || lastScript();
    var globalConfig = window.AMOPOINT_TRACKER || {};
    var endpoint = option("endpoint") || endpointFromScript();
    var storageKey = option("storageKey") || "amopoint:visitor-id";
    var autoTrack = option("autoTrack") !== false && attr("auto-track") !== "false";
    var trackSpa = option("trackSpa") !== false && attr("spa") !== "false";
    var minInterval = Number(option("minInterval") || attr("min-interval") || 800);
    var lastUrl = "";
    var lastSentAt = 0;

    function lastScript() {
        var scripts = document.getElementsByTagName("script");
        return scripts[scripts.length - 1] || null;
    }

    function attr(name) {
        if (!script) {
            return null;
        }

        return script.getAttribute("data-" + name);
    }

    function option(name) {
        if (Object.prototype.hasOwnProperty.call(globalConfig, name)) {
            return globalConfig[name];
        }

        return attr(name.replace(/[A-Z]/g, function (char) {
            return "-" + char.toLowerCase();
        }));
    }

    function endpointFromScript() {
        var source = script && script.src ? script.src : window.location.href;
        return new URL("/api/visits", source).toString();
    }

    function uuid() {
        if (window.crypto && window.crypto.randomUUID) {
            return window.crypto.randomUUID();
        }

        return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (char) {
            var random = Math.random() * 16 | 0;
            var value = char === "x" ? random : (random & 0x3 | 0x8);
            return value.toString(16);
        });
    }

    function storedId(key, storage) {
        try {
            var existing = storage.getItem(key);

            if (existing) {
                return existing;
            }

            var created = uuid();
            storage.setItem(key, created);

            return created;
        } catch (error) {
            return uuid();
        }
    }

    function visitorId() {
        return storedId(storageKey, window.localStorage);
    }

    function browserName() {
        var ua = navigator.userAgent || "";

        if (/edg\//i.test(ua)) {
            return "Edge";
        }

        if (/opr\//i.test(ua)) {
            return "Opera";
        }

        if (/firefox\//i.test(ua)) {
            return "Firefox";
        }

        if (/samsungbrowser\//i.test(ua)) {
            return "Samsung Internet";
        }

        if (/chrome\//i.test(ua) || /crios\//i.test(ua)) {
            return "Chrome";
        }

        if (/safari\//i.test(ua)) {
            return "Safari";
        }

        return "Unknown";
    }

    function deviceType() {
        var ua = navigator.userAgent || "";
        var platform = navigator.platform || "";
        var touch = navigator.maxTouchPoints || 0;

        if (/bot|crawl|spider|slurp|bingpreview/i.test(ua)) {
            return "bot";
        }

        if (/ipad|tablet/i.test(ua) || (platform === "MacIntel" && touch > 1)) {
            return "tablet";
        }

        if (/mobi|android|iphone|ipod/i.test(ua)) {
            return "mobile";
        }

        return "desktop";
    }

    function cleanText(value, maxLength) {
        if (typeof value !== "string") {
            return null;
        }

        var cleaned = value.replace(/\s+/g, " ").trim();

        if (!cleaned) {
            return null;
        }

        return cleaned.slice(0, maxLength);
    }

    function safeUrl(value) {
        try {
            var url = new URL(value);

            if (url.protocol !== "http:" && url.protocol !== "https:") {
                return null;
            }

            url.search = "";
            url.hash = "";

            return url.toString();
        } catch (error) {
            return null;
        }
    }

    function payload(referrer) {
        return {
            visitor_id: visitorId(),
            site_host: cleanText(option("siteHost") || window.location.hostname, 255),
            page_title: cleanText(document.title || "", 255),
            page_url: safeUrl(window.location.href),
            referrer: safeUrl(referrer || document.referrer || ""),
            city: cleanText(option("city"), 120),
            country: cleanText(option("country"), 2),
            device_type: deviceType(),
            browser: browserName(),
            platform: cleanText(navigator.platform || "", 120),
            screen_width: window.screen ? window.screen.width : null,
            screen_height: window.screen ? window.screen.height : null,
            language: cleanText(navigator.language || "", 20),
            timezone: cleanText(Intl.DateTimeFormat().resolvedOptions().timeZone || "", 80)
        };
    }

    function post(data) {
        var body = JSON.stringify(data);

        if (navigator.sendBeacon) {
            var blob = new Blob([body], { type: "application/json" });

            if (navigator.sendBeacon(endpoint, blob)) {
                return;
            }
        }

        if (window.fetch) {
            window.fetch(endpoint, {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: body,
                credentials: "omit",
                keepalive: true,
                mode: "cors"
            }).catch(function () {});
        }
    }

    function track(referrer) {
        var now = Date.now();
        var currentUrl = window.location.href;

        if (currentUrl === lastUrl && now - lastSentAt < minInterval) {
            return;
        }

        lastUrl = currentUrl;
        lastSentAt = now;
        post(payload(referrer));
    }

    function trackSoon(referrer) {
        if ("requestIdleCallback" in window) {
            window.requestIdleCallback(function () {
                track(referrer);
            }, { timeout: 1500 });

            return;
        }

        window.setTimeout(function () {
            track(referrer);
        }, 0);
    }

    function installSpaTracking() {
        var previousUrl = window.location.href;

        function onChange() {
            var referrer = previousUrl;
            previousUrl = window.location.href;
            trackSoon(referrer);
        }

        ["pushState", "replaceState"].forEach(function (name) {
            var original = history[name];

            history[name] = function () {
                var result = original.apply(this, arguments);
                onChange();
                return result;
            };
        });

        window.addEventListener("popstate", onChange);
    }

    window.AmopointTracker = {
        version: version,
        track: function () {
            trackSoon(document.referrer || "");
        }
    };

    if (trackSpa) {
        installSpaTracking();
    }

    if (autoTrack) {
        trackSoon(document.referrer || "");
    }
}(window, document));
