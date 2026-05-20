<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Amopoint Counter</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f6f7f9;
            --panel: #ffffff;
            --panel-soft: #f0f4f3;
            --text: #182029;
            --muted: #667085;
            --line: #d9dee5;
            --brand: #0f766e;
            --brand-dark: #115e59;
            --amber: #c47a16;
            --rose: #be3b5d;
            --blue: #2f6fb2;
            --shadow: 0 14px 40px rgba(15, 23, 42, .08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        button,
        input,
        select {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        .shell {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
            padding: 24px;
            background: #111827;
            color: #f9fafb;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 44px;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #14b8a6;
            color: #082f2b;
            font-weight: 800;
        }

        .brand-title {
            font-size: 16px;
            font-weight: 750;
        }

        .brand-subtitle {
            margin-top: 2px;
            color: #a7b0bf;
            font-size: 12px;
        }

        .snippet {
            margin-top: auto;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, .12);
        }

        .snippet-label {
            margin-bottom: 10px;
            color: #a7b0bf;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .snippet code {
            display: block;
            overflow-wrap: anywhere;
            padding: 12px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .08);
            color: #e5e7eb;
            font-size: 12px;
            line-height: 1.45;
        }

        .main {
            min-width: 0;
            padding: 28px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }

        h1,
        h2,
        p {
            margin: 0;
        }

        h1 {
            font-size: 24px;
            line-height: 1.2;
            font-weight: 760;
        }

        .muted {
            color: var(--muted);
            font-size: 13px;
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: var(--shadow);
        }

        .auth {
            display: grid;
            place-items: center;
            min-height: calc(100vh - 56px);
        }

        .auth-panel {
            width: min(440px, 100%);
            padding: 28px;
        }

        .form {
            display: grid;
            gap: 14px;
            margin-top: 24px;
        }

        .field {
            display: grid;
            gap: 7px;
        }

        label {
            color: #344054;
            font-size: 13px;
            font-weight: 700;
        }

        input,
        select {
            width: 100%;
            min-height: 42px;
            border: 1px solid #cfd6df;
            border-radius: 8px;
            background: #fff;
            color: var(--text);
            padding: 9px 11px;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, .16);
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 9px 14px;
            background: var(--brand);
            color: #fff;
            font-weight: 750;
        }

        .button:hover {
            background: var(--brand-dark);
        }

        .button.secondary {
            border-color: #cfd6df;
            background: #fff;
            color: #263241;
        }

        .button.secondary:hover {
            background: #f3f5f7;
        }

        .alert {
            display: none;
            margin-top: 14px;
            border-radius: 8px;
            padding: 10px 12px;
            background: #fff1f3;
            color: #9f1239;
            font-size: 13px;
        }

        .dashboard {
            display: none;
        }

        .controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .controls select {
            width: auto;
            min-width: 150px;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .metric {
            padding: 18px;
        }

        .metric-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 750;
            text-transform: uppercase;
        }

        .metric-value {
            margin-top: 10px;
            font-size: 30px;
            line-height: 1;
            font-weight: 780;
        }

        .charts {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(320px, .85fr);
            gap: 16px;
            align-items: start;
        }

        .chart-panel {
            position: relative;
            overflow: hidden;
            padding: 0;
            transition: box-shadow .18s ease, transform .18s ease;
        }

        .chart-panel::before {
            display: block;
            height: 4px;
            background: linear-gradient(90deg, var(--brand), var(--blue));
            content: "";
        }

        .chart-panel:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 46px rgba(15, 23, 42, .11);
        }

        .widget-body {
            padding: 18px;
        }

        .panel-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .panel-title h2 {
            font-size: 16px;
            font-weight: 760;
        }

        .widget-action {
            display: grid;
            flex: 0 0 auto;
            place-items: center;
            width: 38px;
            height: 38px;
            border: 1px solid #cfd6df;
            border-radius: 8px;
            background: #fff;
            color: #263241;
        }

        .widget-action:hover {
            border-color: rgba(15, 118, 110, .45);
            background: #f0fdfa;
            color: var(--brand-dark);
        }

        .widget-action svg,
        .back-icon {
            width: 17px;
            height: 17px;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2;
            fill: none;
        }

        .canvas-wrap {
            position: relative;
            width: 100%;
            height: 340px;
            transition: height .18s ease;
        }

        canvas {
            display: block;
            width: 100%;
            height: 100%;
        }

        .city-list {
            display: grid;
            gap: 8px;
            margin-top: 14px;
        }

        .city-row {
            display: grid;
            grid-template-columns: 12px minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            min-height: 28px;
            color: #344054;
            font-size: 13px;
        }

        .swatch {
            width: 10px;
            height: 10px;
            border-radius: 3px;
        }

        .loading {
            opacity: .62;
            pointer-events: none;
        }

        .expanded-view {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 30;
            overflow: auto;
            background: var(--bg);
        }

        .expanded-view.is-open {
            display: block;
        }

        .expanded-shell {
            min-height: 100vh;
            padding: 28px;
        }

        .expanded-topbar {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .expanded-title {
            min-width: 0;
        }

        .expanded-title h1 {
            margin-top: 3px;
        }

        .expanded-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 16px;
            align-items: start;
        }

        .expanded-chart-panel,
        .expanded-side-panel {
            padding: 18px;
        }

        .expanded-chart-panel {
            min-height: calc(100vh - 132px);
        }

        .expanded-canvas-wrap {
            position: relative;
            height: min(70vh, 720px);
            min-height: 460px;
        }

        .detail-list {
            display: grid;
            gap: 10px;
        }

        .detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 34px;
            border-bottom: 1px solid #eef1f4;
            color: #344054;
            font-size: 13px;
        }

        .detail-row:last-child {
            border-bottom: 0;
        }

        .detail-row strong {
            color: var(--text);
            font-size: 15px;
        }

        body.widget-open {
            overflow: hidden;
        }

        @media (max-width: 920px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                min-height: auto;
            }

            .snippet {
                margin-top: 0;
            }

            .charts,
            .metrics,
            .expanded-grid {
                grid-template-columns: 1fr;
            }

            .expanded-chart-panel {
                min-height: auto;
            }

            .expanded-canvas-wrap {
                height: 460px;
                min-height: 360px;
            }
        }

        @media (max-width: 560px) {
            .main,
            .sidebar,
            .expanded-shell {
                padding: 18px;
            }

            .topbar,
            .expanded-topbar {
                align-items: stretch;
                flex-direction: column;
            }

            .controls,
            .controls select,
            .controls .button {
                width: 100%;
            }

            .expanded-canvas-wrap {
                height: 380px;
                min-height: 320px;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">A</div>
                <div>
                    <div class="brand-title">Amopoint Counter</div>
                    <div class="brand-subtitle">Visits analytics</div>
                </div>
            </div>

            <div class="snippet">
                <div class="snippet-label">Tracker</div>
                <code>&lt;script async src="{{ url('/tracker.js') }}"&gt;&lt;/script&gt;</code>
            </div>
        </aside>

        <main class="main">
            <section id="authView" class="auth">
                <div class="panel auth-panel">
                    <h1>Sign in</h1>
                    <p class="muted">Access to visit statistics is protected by Sanctum token auth.</p>

                    <form id="loginForm" class="form">
                        <div class="field">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" autocomplete="username" required>
                        </div>
                        <div class="field">
                            <label for="password">Password</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required>
                        </div>
                        <button class="button" type="submit">Sign in</button>
                    </form>

                    <div id="authError" class="alert"></div>
                </div>
            </section>

            <section id="dashboardView" class="dashboard">
                <div class="topbar">
                    <div>
                        <h1>Statistics</h1>
                        <p id="userLine" class="muted"></p>
                    </div>
                    <div class="controls">
                        <select id="daysSelect" aria-label="Period">
                            <option value="1">24 hours</option>
                            <option value="7" selected>7 days</option>
                            <option value="30">30 days</option>
                        </select>
                        <select id="deviceSelect" aria-label="Device">
                            <option value="">All devices</option>
                            <option value="desktop">Desktop</option>
                            <option value="mobile">Mobile</option>
                            <option value="tablet">Tablet</option>
                            <option value="bot">Bot</option>
                            <option value="unknown">Unknown</option>
                        </select>
                        <button id="refreshButton" class="button secondary" type="button">Refresh</button>
                        <button id="logoutButton" class="button" type="button">Logout</button>
                    </div>
                </div>

                <div id="dashboardError" class="alert"></div>

                <div class="metrics">
                    <div class="panel metric">
                        <div class="metric-label">Page views</div>
                        <div id="pageViews" class="metric-value">0</div>
                    </div>
                    <div class="panel metric">
                        <div class="metric-label">Unique visits</div>
                        <div id="uniqueVisitors" class="metric-value">0</div>
                    </div>
                    <div class="panel metric">
                        <div class="metric-label">Cities</div>
                        <div id="citiesCount" class="metric-value">0</div>
                    </div>
                </div>

                <div class="charts">
                    <div class="panel chart-panel">
                        <div class="widget-body">
                            <div class="panel-title">
                                <div>
                                    <h2>Visits by hour</h2>
                                    <p id="periodLine" class="muted"></p>
                                </div>
                                <button class="widget-action" type="button" data-expand-widget="hourly" aria-label="Expand visits by hour" title="Expand">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M15 3h6v6"></path>
                                        <path d="M10 14 21 3"></path>
                                        <path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"></path>
                                    </svg>
                                </button>
                            </div>
                            <div id="hourlyChartWrap" class="canvas-wrap">
                                <canvas id="hourlyChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="panel chart-panel">
                        <div class="widget-body">
                            <div class="panel-title">
                                <div>
                                    <h2>Cities</h2>
                                    <p class="muted">Share of page views</p>
                                </div>
                            </div>
                            <div id="cityChartWrap" class="canvas-wrap">
                                <canvas id="cityChart"></canvas>
                            </div>
                            <div id="cityList" class="city-list"></div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <section id="expandedView" class="expanded-view" aria-hidden="true">
        <div class="expanded-shell">
            <div class="expanded-topbar">
                <button id="widgetBackButton" class="button secondary" type="button">
                    <svg class="back-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m12 19-7-7 7-7"></path>
                        <path d="M19 12H5"></path>
                    </svg>
                    Back
                </button>
                <div class="expanded-title">
                    <p class="muted">Expanded widget</p>
                    <h1 id="expandedTitle">Widget</h1>
                    <p id="expandedSubtitle" class="muted"></p>
                </div>
            </div>

            <div class="expanded-grid">
                <div class="panel expanded-chart-panel">
                    <div id="expandedChartWrap" class="expanded-canvas-wrap">
                        <canvas id="expandedHourlyChart"></canvas>
                    </div>
                </div>

                <aside class="panel expanded-side-panel">
                    <div id="expandedStats" class="detail-list"></div>
                </aside>
            </div>
        </div>
    </section>

    <script src="{{ asset('/vendor/chart.umd.min.js') }}"></script>
    <script>
        (function () {
            "use strict";

            var tokenKey = "amopoint:access-token";
            var authView = document.getElementById("authView");
            var dashboardView = document.getElementById("dashboardView");
            var loginForm = document.getElementById("loginForm");
            var authError = document.getElementById("authError");
            var dashboardError = document.getElementById("dashboardError");
            var userLine = document.getElementById("userLine");
            var pageViews = document.getElementById("pageViews");
            var uniqueVisitors = document.getElementById("uniqueVisitors");
            var citiesCount = document.getElementById("citiesCount");
            var periodLine = document.getElementById("periodLine");
            var daysSelect = document.getElementById("daysSelect");
            var deviceSelect = document.getElementById("deviceSelect");
            var refreshButton = document.getElementById("refreshButton");
            var logoutButton = document.getElementById("logoutButton");
            var hourlyChartWrap = document.getElementById("hourlyChartWrap");
            var cityChartWrap = document.getElementById("cityChartWrap");
            var cityList = document.getElementById("cityList");
            var expandedView = document.getElementById("expandedView");
            var expandedTitle = document.getElementById("expandedTitle");
            var expandedSubtitle = document.getElementById("expandedSubtitle");
            var expandedChartWrap = document.getElementById("expandedChartWrap");
            var expandedHourlyChart = document.getElementById("expandedHourlyChart");
            var expandedStats = document.getElementById("expandedStats");
            var widgetBackButton = document.getElementById("widgetBackButton");
            var expandButtons = Array.prototype.slice.call(document.querySelectorAll("[data-expand-widget]"));
            var colors = ["#0f766e", "#2f6fb2", "#c47a16", "#be3b5d", "#5b6f82", "#7c5cbd", "#408565"];
            var token = sessionStorage.getItem(tokenKey);
            var lastStats = null;
            var activeWidget = null;
            var hourlyChartInstance = null;
            var cityChartInstance = null;
            var expandedHourlyChartInstance = null;

            if (window.Chart) {
                Chart.defaults.font.family = "Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, sans-serif";
                Chart.defaults.color = "#667085";
            }

            function api(path, options) {
                options = options || {};
                var headers = Object.assign({
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                }, options.headers || {});

                if (token) {
                    headers.Authorization = "Bearer " + token;
                }

                return fetch(path, Object.assign({}, options, { headers: headers })).then(function (response) {
                    return response.json().catch(function () {
                        return {};
                    }).then(function (body) {
                        if (!response.ok) {
                            var message = body.message || "Request failed.";
                            throw new Error(message);
                        }

                        return body;
                    });
                });
            }

            function showAuth(message) {
                hideExpandedWidget();
                dashboardView.style.display = "none";
                authView.style.display = "grid";

                if (message) {
                    authError.textContent = message;
                    authError.style.display = "block";
                } else {
                    authError.style.display = "none";
                }
            }

            function showDashboard() {
                authView.style.display = "none";
                dashboardView.style.display = "block";
            }

            function setLoading(value) {
                dashboardView.classList.toggle("loading", value);
            }

            function number(value) {
                return new Intl.NumberFormat("en-US").format(value || 0);
            }

            function loadUser() {
                return api("/api/auth/me").then(function (body) {
                    userLine.textContent = body.data.name + " / " + body.data.email;
                });
            }

            function loadStatistics() {
                dashboardError.style.display = "none";
                setLoading(true);
                var params = new URLSearchParams({
                    days: daysSelect.value,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone || "UTC"
                });

                if (deviceSelect.value) {
                    params.set("device_type", deviceSelect.value);
                }

                return api("/api/statistics/summary?" + params.toString())
                    .then(renderStatistics)
                    .finally(function () {
                        setLoading(false);
                    });
            }

            function safeLoadStatistics() {
                return loadStatistics().catch(function (error) {
                    if (error.message === "Unauthenticated.") {
                        sessionStorage.removeItem(tokenKey);
                        token = null;
                        showAuth();
                        return;
                    }

                    dashboardError.textContent = error.message;
                    dashboardError.style.display = "block";
                });
            }

            function renderStatistics(body) {
                var data = body.data;
                lastStats = data;
                pageViews.textContent = number(data.totals.page_views);
                uniqueVisitors.textContent = number(data.totals.unique_visitors);
                citiesCount.textContent = number(data.totals.cities);
                periodLine.textContent = formatPeriod(data.period.from, data.period.to);
                updateChartHeights(data);
                drawLineChart(document.getElementById("hourlyChart"), data.hourly, false);
                drawPieChart(document.getElementById("cityChart"), data.cities, false);
                renderCityList(data.cities);

                if (activeWidget) {
                    renderExpandedWidget(activeWidget);
                } else {
                    syncWidgetFromLocation();
                }
            }

            function formatPeriod(from, to) {
                var formatter = new Intl.DateTimeFormat("en-US", {
                    month: "short",
                    day: "2-digit",
                    hour: "2-digit",
                    minute: "2-digit",
                    hour12: false
                });

                return formatter.format(new Date(from)) + " - " + formatter.format(new Date(to));
            }

            function formatHour(value) {
                return new Intl.DateTimeFormat("en-GB", {
                    hour: "2-digit",
                    minute: "2-digit",
                    hour12: false
                }).format(new Date(value));
            }

            function formatDay(value) {
                return new Intl.DateTimeFormat("en-US", {
                    month: "short",
                    day: "2-digit"
                }).format(new Date(value));
            }

            function formatDateTime(value) {
                return formatDay(value) + ", " + formatHour(value);
            }

            function sameDay(left, right) {
                return left.getFullYear() === right.getFullYear()
                    && left.getMonth() === right.getMonth()
                    && left.getDate() === right.getDate();
            }

            function chartMax(rows) {
                return rows.reduce(function (max, row) {
                    return Math.max(max, row.unique_visits || 0, row.page_views || 0);
                }, 1);
            }

            function chartHeight(rows, expanded) {
                var max = chartMax(rows);
                var pointExtra = Math.min(expanded ? 150 : 80, Math.max(0, rows.length - 48) * .7);
                var valueExtra = Math.min(expanded ? 190 : 120, Math.max(0, max - 8) * 6);
                var base = expanded ? 500 : 340;
                var limit = expanded ? 760 : 540;

                return Math.round(Math.min(limit, base + pointExtra + valueExtra));
            }

            function updateChartHeights(data) {
                var compactHeight = chartHeight(data.hourly, false);
                hourlyChartWrap.style.height = compactHeight + "px";
                cityChartWrap.style.height = Math.max(320, Math.min(420, compactHeight - 40)) + "px";

                if (activeWidget === "hourly") {
                    expandedChartWrap.style.height = chartHeight(data.hourly, true) + "px";
                } else {
                    expandedChartWrap.style.height = "620px";
                }
            }

            function ensureChartLibrary() {
                if (window.Chart) {
                    return true;
                }

                dashboardError.textContent = "Chart library failed to load.";
                dashboardError.style.display = "block";
                return false;
            }

            function destroyChart(instance) {
                if (instance) {
                    instance.destroy();
                }
            }

            function hourlyTickLabel(rows, index, expanded) {
                var row = rows[index];

                if (!row) {
                    return "";
                }

                var current = new Date(row.hour);
                var previous = index > 0 && rows[index - 1] ? new Date(rows[index - 1].hour) : null;
                var isBoundary = index === 0 || index === rows.length - 1 || !previous || !sameDay(current, previous);

                if (expanded || rows.length <= 30) {
                    return isBoundary && rows.length > 30
                        ? [formatDay(row.hour), formatHour(row.hour)]
                        : formatHour(row.hour);
                }

                return isBoundary ? [formatDay(row.hour), formatHour(row.hour)] : formatHour(row.hour);
            }

            function hourlyTickLimit(rows, expanded, canvas) {
                var width = canvas.parentElement ? canvas.parentElement.clientWidth : 720;

                if (expanded) {
                    return width < 760 ? 9 : 14;
                }

                if (rows.length <= 24) {
                    return width < 640 ? 6 : 10;
                }

                return width < 640 ? 5 : 8;
            }

            function yStep(max) {
                if (max <= 10) {
                    return 1;
                }

                if (max <= 30) {
                    return 5;
                }

                if (max <= 80) {
                    return 10;
                }

                return Math.ceil(max / 60) * 10;
            }

            function drawLineChart(canvas, rows, expanded) {
                if (!ensureChartLibrary()) {
                    return;
                }

                var previous = canvas === expandedHourlyChart ? expandedHourlyChartInstance : hourlyChartInstance;
                var max = chartMax(rows);
                var labels = rows.map(function (row) { return row.hour; });
                var pointRadius = rows.length > (expanded ? 120 : 48) ? 0 : (expanded ? 3 : 2);

                destroyChart(previous);

                var next = new Chart(canvas, {
                    type: "line",
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: "Unique visits",
                                data: rows.map(function (row) { return row.unique_visits; }),
                                borderColor: "#0f766e",
                                backgroundColor: "rgba(15, 118, 110, .14)",
                                borderWidth: expanded ? 3 : 2.5,
                                fill: true,
                                tension: .34,
                                pointRadius: pointRadius,
                                pointHoverRadius: 5
                            },
                            {
                                label: "Page views",
                                data: rows.map(function (row) { return row.page_views; }),
                                borderColor: "#2f6fb2",
                                backgroundColor: "rgba(47, 111, 178, .12)",
                                borderDash: [6, 5],
                                borderWidth: expanded ? 2.6 : 2,
                                fill: false,
                                tension: .28,
                                pointRadius: 0,
                                pointHoverRadius: 5
                            }
                        ]
                    },
                    options: {
                        animation: { duration: 220 },
                        maintainAspectRatio: false,
                        normalized: true,
                        responsive: true,
                        interaction: {
                            intersect: false,
                            mode: "index"
                        },
                        plugins: {
                            legend: {
                                display: true,
                                labels: {
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    padding: 14,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    title: function (items) {
                                        var index = items[0] ? items[0].dataIndex : 0;
                                        return rows[index] ? formatDateTime(rows[index].hour) : "";
                                    },
                                    label: function (context) {
                                        return context.dataset.label + ": " + number(context.parsed.y);
                                    }
                                },
                                displayColors: true,
                                padding: 10
                            }
                        },
                        scales: {
                            x: {
                                border: { display: false },
                                grid: { display: false },
                                ticks: {
                                    autoSkip: true,
                                    maxRotation: 0,
                                    maxTicksLimit: hourlyTickLimit(rows, expanded, canvas),
                                    minRotation: 0,
                                    padding: 10,
                                    callback: function (value) {
                                        return hourlyTickLabel(rows, Number(value), expanded);
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grace: "22%",
                                suggestedMax: Math.max(2, Math.ceil(max * 1.18)),
                                grid: {
                                    color: "#d9dee5"
                                },
                                ticks: {
                                    maxTicksLimit: expanded ? 8 : 6,
                                    precision: 0,
                                    stepSize: yStep(max),
                                    callback: function (value) {
                                        return number(value);
                                    }
                                }
                            }
                        }
                    }
                });

                if (canvas === expandedHourlyChart) {
                    expandedHourlyChartInstance = next;
                } else {
                    hourlyChartInstance = next;
                }
            }

            function centerTextPlugin(total) {
                return {
                    id: "centerText-" + total,
                    afterDraw: function (chart) {
                        var area = chart.chartArea;

                        if (!area) {
                            return;
                        }

                        var ctx = chart.ctx;
                        var x = (area.left + area.right) / 2;
                        var y = (area.top + area.bottom) / 2;

                        ctx.save();
                        ctx.textAlign = "center";
                        ctx.textBaseline = "middle";
                        ctx.fillStyle = "#182029";
                        ctx.font = "700 24px Inter, sans-serif";
                        ctx.fillText(number(total), x, y - 6);
                        ctx.fillStyle = "#667085";
                        ctx.font = "12px Inter, sans-serif";
                        ctx.fillText("page views", x, y + 18);
                        ctx.restore();
                    }
                };
            }

            function drawPieChart(canvas, cities, expanded) {
                if (!ensureChartLibrary()) {
                    return;
                }

                var previous = cityChartInstance;
                var total = cities.reduce(function (sum, city) { return sum + city.page_views; }, 0);
                var chartCities = cities.length ? cities : [{ city: "No data", page_views: 1, percentage: 100 }];

                destroyChart(previous);

                var next = new Chart(canvas, {
                    type: "doughnut",
                    data: {
                        labels: chartCities.map(function (city) { return city.city; }),
                        datasets: [
                            {
                                data: chartCities.map(function (city) { return city.page_views; }),
                                backgroundColor: chartCities.map(function (city, index) {
                                    return cities.length ? colors[index % colors.length] : "#d9dee5";
                                }),
                                borderColor: "#fff",
                                borderWidth: 3,
                                hoverOffset: 8
                            }
                        ]
                    },
                    options: {
                        animation: { duration: 220 },
                        cutout: expanded ? "64%" : "62%",
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: {
                                display: expanded,
                                position: "bottom",
                                labels: {
                                    boxWidth: 10,
                                    boxHeight: 10,
                                    padding: 14,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        var city = cities[context.dataIndex];

                                        if (!city) {
                                            return "No data";
                                        }

                                        return city.city + ": " + number(city.page_views) + " views, " + city.percentage + "%";
                                    }
                                },
                                padding: 10
                            }
                        }
                    },
                    plugins: [centerTextPlugin(total)]
                });

                cityChartInstance = next;
            }

            function renderCityList(cities, target, limit) {
                var list = target || cityList;
                list.replaceChildren();
                cities.slice(0, limit || 7).forEach(function (city, index) {
                    var row = document.createElement("div");
                    row.className = "city-row";

                    var swatch = document.createElement("span");
                    swatch.className = "swatch";
                    swatch.style.background = colors[index % colors.length];

                    var cityName = document.createElement("span");
                    cityName.textContent = city.city;

                    var percentage = document.createElement("strong");
                    percentage.textContent = city.percentage + "%";

                    row.append(swatch, cityName, percentage);
                    list.appendChild(row);
                });
            }

            function renderDetailRows(rows) {
                expandedStats.replaceChildren();

                rows.forEach(function (item) {
                    var row = document.createElement("div");
                    row.className = "detail-row";

                    var label = document.createElement("span");
                    label.textContent = item.label;

                    var value = document.createElement("strong");
                    value.textContent = item.value;

                    row.append(label, value);
                    expandedStats.appendChild(row);
                });
            }

            function renderExpandedWidget(widget) {
                if (!lastStats || !widget) {
                    return;
                }

                expandedTitle.textContent = "Visits by hour";
                expandedSubtitle.textContent = formatPeriod(lastStats.period.from, lastStats.period.to);

                renderDetailRows([
                    { label: "Page views", value: number(lastStats.totals.page_views) },
                    { label: "Unique visits", value: number(lastStats.totals.unique_visitors) },
                    { label: "Cities", value: number(lastStats.totals.cities) }
                ]);

                expandedChartWrap.style.height = chartHeight(lastStats.hourly, true) + "px";
                drawLineChart(expandedHourlyChart, lastStats.hourly, true);
            }

            function showExpandedWidget(widget, updateHistory) {
                if (!lastStats || widget !== "hourly") {
                    return;
                }

                activeWidget = widget;
                expandedView.classList.add("is-open");
                expandedView.setAttribute("aria-hidden", "false");
                document.body.classList.add("widget-open");
                renderExpandedWidget(widget);

                if (updateHistory) {
                    var url = new URL(window.location.href);
                    if (url.searchParams.get("widget") !== widget) {
                        url.searchParams.set("widget", widget);
                        window.history.pushState({ widget: widget }, "", url);
                    }
                }
            }

            function hideExpandedWidget() {
                activeWidget = null;
                expandedView.classList.remove("is-open");
                expandedView.setAttribute("aria-hidden", "true");
                document.body.classList.remove("widget-open");
            }

            function syncWidgetFromLocation() {
                var widget = new URL(window.location.href).searchParams.get("widget");

                if (widget === "hourly") {
                    showExpandedWidget(widget, false);
                    return;
                }

                hideExpandedWidget();
            }

            loginForm.addEventListener("submit", function (event) {
                event.preventDefault();
                authError.style.display = "none";

                var payload = {
                    email: new FormData(loginForm).get("email"),
                    password: new FormData(loginForm).get("password"),
                    device_name: "dashboard"
                };

                api("/api/auth/login", {
                    method: "POST",
                    body: JSON.stringify(payload)
                }).then(function (body) {
                    token = body.access_token;
                    sessionStorage.setItem(tokenKey, token);
                    showDashboard();
                    return loadUser().then(loadStatistics);
                }).catch(function (error) {
                    showAuth(error.message);
                });
            });

            refreshButton.addEventListener("click", safeLoadStatistics);
            daysSelect.addEventListener("change", safeLoadStatistics);
            deviceSelect.addEventListener("change", safeLoadStatistics);
            widgetBackButton.addEventListener("click", function () {
                var url = new URL(window.location.href);

                if (url.searchParams.has("widget") && window.history.state && window.history.state.widget === activeWidget) {
                    window.history.back();
                    return;
                }

                url.searchParams.delete("widget");
                window.history.replaceState({}, "", url);
                hideExpandedWidget();
            });

            expandButtons.forEach(function (button) {
                button.addEventListener("click", function () {
                    showExpandedWidget(button.getAttribute("data-expand-widget"), true);
                });
            });

            logoutButton.addEventListener("click", function () {
                api("/api/auth/logout", { method: "POST", body: "{}" }).finally(function () {
                    sessionStorage.removeItem(tokenKey);
                    token = null;
                    loginForm.reset();
                    showAuth();
                });
            });

            window.addEventListener("resize", function () {
                if (dashboardView.style.display === "block" && lastStats) {
                    updateChartHeights(lastStats);
                    drawLineChart(document.getElementById("hourlyChart"), lastStats.hourly, false);
                    drawPieChart(document.getElementById("cityChart"), lastStats.cities, false);
                    renderExpandedWidget(activeWidget);
                }
            });

            window.addEventListener("popstate", syncWidgetFromLocation);

            if (token) {
                showDashboard();
                loadUser().then(loadStatistics).catch(function () {
                    sessionStorage.removeItem(tokenKey);
                    token = null;
                    showAuth();
                });
            } else {
                showAuth();
            }
        }());
    </script>
</body>
</html>
