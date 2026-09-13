import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import {
    Chart,
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from 'chart.js';

Chart.register(
    ArcElement,
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Filler,
    Legend,
    LineController,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
);

/* ------------------------------------------------------------------
 | Tema terang / gelap
 * ------------------------------------------------------------------ */
const temaTersimpan = localStorage.getItem('tema');
const gelapDisukai = window.matchMedia('(prefers-color-scheme: dark)').matches;

if (temaTersimpan === 'gelap' || (!temaTersimpan && gelapDisukai)) {
    document.documentElement.classList.add('dark');
}

window.gantiTema = () => {
    const gelap = document.documentElement.classList.toggle('dark');
    localStorage.setItem('tema', gelap ? 'gelap' : 'terang');
    window.dispatchEvent(new CustomEvent('tema-berubah', { detail: { gelap } }));
};

/* ------------------------------------------------------------------
 | Grafik
 * ------------------------------------------------------------------ */
const warna = {
    brand: '#12825b',
    brandTerang: '#42bd8c',
    gold: '#f7b027',
    sky: '#0ea5e9',
    indigo: '#6366f1',
    rose: '#f43f5e',
    slate: '#94a3b8',
    violet: '#8b5cf6',
};

const gridWarna = () => (document.documentElement.classList.contains('dark') ? 'rgba(148,163,184,0.14)' : 'rgba(148,163,184,0.22)');
const tickWarna = () => (document.documentElement.classList.contains('dark') ? '#8192ae' : '#617394');

Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
Chart.defaults.font.size = 12;
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.boxWidth = 8;
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.cornerRadius = 10;
Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15,23,41,0.92)';
Chart.defaults.plugins.tooltip.titleFont = { weight: '700' };

const grafikAktif = new Map();

/**
 * Gambar grafik pada elemen <canvas data-grafik="...">.
 * Konfigurasi diambil dari atribut data-konfig berisi JSON.
 */
function gambarGrafik(kanvas) {
    const jenis = kanvas.dataset.grafik;
    const konfig = JSON.parse(kanvas.dataset.konfig || '{}');
    const sumbuTampil = { color: tickWarna() };

    const dasar = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
    };

    let pengaturan;

    if (jenis === 'garis') {
        const gradasi = kanvas.getContext('2d').createLinearGradient(0, 0, 0, kanvas.height || 260);
        gradasi.addColorStop(0, 'rgba(18,130,91,0.28)');
        gradasi.addColorStop(1, 'rgba(18,130,91,0)');

        pengaturan = {
            type: 'line',
            data: {
                labels: konfig.label ?? [],
                datasets: (konfig.seri ?? []).map((seri, i) => ({
                    label: seri.nama,
                    data: seri.data,
                    borderColor: i === 0 ? warna.brand : warna.gold,
                    backgroundColor: i === 0 ? gradasi : 'rgba(247,176,39,0.12)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2,
                })),
            },
            options: {
                ...dasar,
                plugins: { legend: { display: (konfig.seri ?? []).length > 1, position: 'top', align: 'end' } },
                interaction: { mode: 'index', intersect: false },
                scales: {
                    x: { grid: { display: false }, ticks: sumbuTampil, border: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { ...sumbuTampil, precision: 0 },
                        grid: { color: gridWarna() },
                        border: { display: false },
                    },
                },
            },
        };
    } else if (jenis === 'donat') {
        pengaturan = {
            type: 'doughnut',
            data: {
                labels: konfig.label ?? [],
                datasets: [
                    {
                        data: konfig.data ?? [],
                        backgroundColor: konfig.warna ?? [warna.gold, warna.sky, warna.indigo, warna.brand, warna.rose, warna.slate],
                        borderWidth: 0,
                        hoverOffset: 10,
                    },
                ],
            },
            options: {
                ...dasar,
                cutout: '68%',
                plugins: { legend: { display: true, position: 'bottom', labels: { padding: 16, color: tickWarna() } } },
            },
        };
    } else if (jenis === 'batang') {
        pengaturan = {
            type: 'bar',
            data: {
                labels: konfig.label ?? [],
                datasets: [
                    {
                        data: konfig.data ?? [],
                        backgroundColor: konfig.warna ?? warna.brandTerang,
                        borderRadius: 8,
                        maxBarThickness: 34,
                    },
                ],
            },
            options: {
                ...dasar,
                indexAxis: konfig.horizontal ? 'y' : 'x',
                scales: {
                    x: {
                        grid: { display: !!konfig.horizontal, color: gridWarna() },
                        ticks: { ...sumbuTampil, precision: 0 },
                        border: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { display: !konfig.horizontal, color: gridWarna() },
                        ticks: { ...sumbuTampil, precision: 0 },
                        border: { display: false },
                    },
                },
            },
        };
    } else {
        return;
    }

    grafikAktif.get(kanvas)?.destroy();
    grafikAktif.set(kanvas, new Chart(kanvas, pengaturan));
}

function gambarSemuaGrafik() {
    document.querySelectorAll('canvas[data-grafik]').forEach(gambarGrafik);
}

document.addEventListener('DOMContentLoaded', gambarSemuaGrafik);
window.addEventListener('tema-berubah', gambarSemuaGrafik);

/* ------------------------------------------------------------------
 | Alpine
 * ------------------------------------------------------------------ */
Alpine.plugin(collapse);

/** Penghitung angka yang menghitung naik saat masuk viewport. */
Alpine.data('penghitung', (nilai = 0, durasi = 1400) => ({
    tampil: 0,
    init() {
        const pengamat = new IntersectionObserver((entri) => {
            if (!entri[0].isIntersecting) return;
            pengamat.disconnect();

            const mulai = performance.now();
            const animasi = (waktu) => {
                const maju = Math.min((waktu - mulai) / durasi, 1);
                // easing "ease-out-expo" agar terasa halus di akhir
                const halus = maju === 1 ? 1 : 1 - Math.pow(2, -10 * maju);
                this.tampil = Math.round(nilai * halus);
                if (maju < 1) requestAnimationFrame(animasi);
            };
            requestAnimationFrame(animasi);
        }, { threshold: 0.3 });

        pengamat.observe(this.$el);
    },
}));

/** Baris dinamis (pihak terlapor & lampiran) pada formulir pengaduan. */
Alpine.data('barisDinamis', (awal = [], kosong = {}) => ({
    baris: awal.length ? awal : [{ ...kosong }],
    tambah() {
        this.baris.push({ ...kosong });
    },
    hapus(i) {
        this.baris.splice(i, 1);
        if (!this.baris.length) this.baris.push({ ...kosong });
    },
}));

window.Alpine = Alpine;
Alpine.start();
