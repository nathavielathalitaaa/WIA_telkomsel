import http from 'k6/http';
import { check, sleep } from 'k6';
import { Trend, Rate, Counter } from 'k6/metrics';

// Custom Metrics
const pantauTrend = new Trend('tt_page_pantau_duration', true);
const incidentDetailTrend = new Trend('tt_page_incident_detail_duration', true);
const trackTrend = new Trend('tt_page_track_duration', true);
const replayTrend = new Trend('tt_page_replay_duration', true);
const sitrepTrend = new Trend('tt_page_sitrep_duration', true);
const sourcesTrend = new Trend('tt_page_sources_duration', true);
const guideTrend = new Trend('tt_page_guide_duration', true);
const formReportTrend = new Trend('tt_page_form_report_duration', true);
const apiWilayahTrend = new Trend('tt_api_wilayah_duration', true);
const failureRate = new Rate('tt_failure_rate');
const errorCounter = new Counter('tt_errors');

const BASE_URL = __ENV.BASE_URL || 'http://127.0.0.1:8000';

const INCIDENT_SLUGS = [
    'sim-banjir-luapan-ciliwung-bojonggede-2026',
    'sim-longsor-jalur-puncak-km82-2026',
    'hist-gempa-cugenang-cianjur-2022',
    'hist-banjir-tanggul-wulan-demak-2024',
];

const TICKETS = [
    'RPT-SIM-8K4M9P2X',
    'RPT-SIM-3X7N2Q5W',
    'RSP-SIM-9V5P3W6X',
    'RSP-SIM-7M2K8N4Q',
];

export const options = {
    // Default fallback if not overridden by CLI flags
    vus: Number(__ENV.TARGET_VUS || 1),
    duration: __ENV.TEST_DURATION || '30s',
    summaryTrendStats: ['avg', 'min', 'med', 'max', 'p(90)', 'p(95)', 'p(99)'],
    thresholds: {
        http_req_failed: ['rate<0.01'], // failure rate under 1%
        http_req_duration: ['p(95)<2000', 'p(99)<3000'], // 95% under 2s, 99% under 3s
    },
};

export default function () {
    const defaultHeaders = {
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'User-Agent': 'k6-loadtest-tanggap-terang/1.0',
    };

    // 1. Pantau (Homepage)
    {
        const res = http.get(`${BASE_URL}/`, { headers: defaultHeaders, tags: { endpoint: 'pantau' } });
        pantauTrend.add(res.timings.duration);
        const ok = check(res, { 'pantau status is 200': (r) => r.status === 200 });
        if (!ok) {
            failureRate.add(1);
            errorCounter.add(1);
        } else {
            failureRate.add(0);
        }
    }
    sleep(0.1);

    // 2. Incident Detail (Random Slug)
    {
        const slug = INCIDENT_SLUGS[Math.floor(Math.random() * INCIDENT_SLUGS.length)];
        const res = http.get(`${BASE_URL}/incidents/${slug}`, { headers: defaultHeaders, tags: { endpoint: 'incident_detail' } });
        incidentDetailTrend.add(res.timings.duration);
        const ok = check(res, { 'incident detail status is 200': (r) => r.status === 200 });
        if (!ok) {
            failureRate.add(1);
            errorCounter.add(1);
        } else {
            failureRate.add(0);
        }
    }
    sleep(0.1);

    // 3. Track Ticket (Specific ticket query)
    {
        const ticket = TICKETS[Math.floor(Math.random() * TICKETS.length)];
        const res = http.get(`${BASE_URL}/track?ticket=${ticket}`, { headers: defaultHeaders, tags: { endpoint: 'track' } });
        trackTrend.add(res.timings.duration);
        const ok = check(res, { 'track status is 200': (r) => r.status === 200 });
        if (!ok) {
            failureRate.add(1);
            errorCounter.add(1);
        } else {
            failureRate.add(0);
        }
    }
    sleep(0.1);

    // 4. Linimasa Replay
    {
        const res = http.get(`${BASE_URL}/replay`, { headers: defaultHeaders, tags: { endpoint: 'replay' } });
        replayTrend.add(res.timings.duration);
        const ok = check(res, { 'replay status is 200': (r) => r.status === 200 });
        if (!ok) {
            failureRate.add(1);
            errorCounter.add(1);
        } else {
            failureRate.add(0);
        }
    }
    sleep(0.1);

    // 5. SitRep (Laporan Situasi)
    {
        const res = http.get(`${BASE_URL}/sitrep`, { headers: defaultHeaders, tags: { endpoint: 'sitrep' } });
        sitrepTrend.add(res.timings.duration);
        const ok = check(res, { 'sitrep status is 200': (r) => r.status === 200 });
        if (!ok) {
            failureRate.add(1);
            errorCounter.add(1);
        } else {
            failureRate.add(0);
        }
    }
    sleep(0.1);

    // 6. Sumber Data Terbuka
    {
        const res = http.get(`${BASE_URL}/sumber-data`, { headers: defaultHeaders, tags: { endpoint: 'sources' } });
        sourcesTrend.add(res.timings.duration);
        const ok = check(res, { 'sources status is 200': (r) => r.status === 200 });
        if (!ok) {
            failureRate.add(1);
            errorCounter.add(1);
        } else {
            failureRate.add(0);
        }
    }
    sleep(0.1);

    // 7. Panduan Penggunaan
    {
        const res = http.get(`${BASE_URL}/panduan`, { headers: defaultHeaders, tags: { endpoint: 'guide' } });
        guideTrend.add(res.timings.duration);
        const ok = check(res, { 'guide status is 200': (r) => r.status === 200 });
        if (!ok) {
            failureRate.add(1);
            errorCounter.add(1);
        } else {
            failureRate.add(0);
        }
    }
    sleep(0.1);

    // 8. Form Lapor Warga
    {
        const res = http.get(`${BASE_URL}/reports/create`, { headers: defaultHeaders, tags: { endpoint: 'form_report' } });
        formReportTrend.add(res.timings.duration);
        const ok = check(res, { 'form report status is 200': (r) => r.status === 200 });
        if (!ok) {
            failureRate.add(1);
            errorCounter.add(1);
        } else {
            failureRate.add(0);
        }
    }
    sleep(0.1);

    // 9. API Wilayah (JSON API)
    {
        const res = http.get(`${BASE_URL}/api/wilayah/provinsi`, {
            headers: { 'Accept': 'application/json', 'User-Agent': 'k6-loadtest-tanggap-terang/1.0' },
            tags: { endpoint: 'api_wilayah' },
        });
        apiWilayahTrend.add(res.timings.duration);
        const ok = check(res, { 'api wilayah status is 200': (r) => r.status === 200 });
        if (!ok) {
            failureRate.add(1);
            errorCounter.add(1);
        } else {
            failureRate.add(0);
        }
    }
    sleep(0.2);
}

