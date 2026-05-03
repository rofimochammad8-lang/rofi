<?php
require_once ROOT . '/app/views/layouts/header.php';
?>

<style>
    #map {
        height: 500px;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        z-index: 1;
    }
    .legend {
        background: white;
        padding: 10px 14px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        font-size: 13px;
        line-height: 2;
    }
    .legend-dot {
        display: inline-block;
        width: 13px; height: 13px;
        border-radius: 50%;
        margin-right: 6px;
        vertical-align: middle;
    }
    .stat-mini {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .stat-mini-card {
        flex: 1;
        min-width: 120px;
        background: #fff;
        border-radius: 10px;
        padding: 12px 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #f0f0f0;
        text-align: center;
    }
    .stat-mini-card .num { font-size: 22px; font-weight: 700; }
    .stat-mini-card .lbl { font-size: 12px; color: #888; margin-top: 2px; }
</style>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-map me-2"></i>Peta Persebaran Stunting</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">

        <!-- Filter Tahun -->
        <div class="card-table mb-4">
            <div class="d-flex align-items-end gap-3 flex-wrap">
                <div>
                    <label class="form-label"><i class="bi bi-funnel me-1"></i>Filter Tahun</label>
                    <select id="filterTahun" class="form-control" style="min-width:160px;">
                        <option value="">Semua Tahun</option>
                        <?php while ($t = mysqli_fetch_assoc($semua_tahun)): ?>
                        <option value="<?= $t['id'] ?>"
                            <?= $id_tahun_aktif == $t['id'] ? 'selected' : '' ?>>
                            <?= $t['tahun'] ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button id="btnFilter" class="btn"
                        style="background:#1a6b3a;color:#fff;border-radius:8px;font-weight:600;padding:9px 20px;">
                    <i class="bi bi-search me-1"></i>Tampilkan
                </button>
                <button id="btnReset" class="btn"
                        style="background:#f0f0f0;color:#555;border-radius:8px;font-weight:600;padding:9px 20px;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
            </div>
        </div>

        <!-- Statistik Mini -->
        <div class="stat-mini">
            <div class="stat-mini-card">
                <div class="num" style="color:#1a73e8;"><?= $total_semua_posyandu ?></div>
                <div class="lbl">Total Posyandu</div>
            </div>
            <div class="stat-mini-card">
                <div class="num" id="statHijau" style="color:#43A047;">0</div>
                <div class="lbl">🟢 Hijau (Normal)</div>
            </div>
            <div class="stat-mini-card">
                <div class="num" id="statKuning" style="color:#FBC02D;">0</div>
                <div class="lbl">🟡 Kuning (Berisiko)</div>
            </div>
            <div class="stat-mini-card">
                <div class="num" id="statMerah" style="color:#e53935;">0</div>
                <div class="lbl">🔴 Merah (Stunting)</div>
            </div>
        </div>

        <!-- Peta -->
        <div class="card-table" style="padding:16px;">
            <div id="map"></div>
        </div>

    </div>
</div>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Init peta - koordinat Desa Sumberwaru
const map = L.map('map').setView([-7.7, 114.0], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Legenda
const legend = L.control({ position: 'bottomright' });
legend.onAdd = function() {
    const div = L.DomUtil.create('div', 'legend');
    div.innerHTML = `
        <strong style="font-size:13px;">Keterangan</strong><br>
        <span class="legend-dot" style="background:#43A047;"></span> Hijau — Normal (0–1)<br>
        <span class="legend-dot" style="background:#FBC02D;"></span> Kuning — Berisiko (2–3)<br>
        <span class="legend-dot" style="background:#e53935;"></span> Merah — Stunting (4+)
    `;
    return div;
};
legend.addTo(map);

let markers = [];

function loadPeta(id_tahun = '') {
    // Hapus marker lama
    markers.forEach(m => map.removeLayer(m));
    markers = [];

    // Reset statistik
    document.getElementById('statHijau').textContent  = 0;
    document.getElementById('statKuning').textContent = 0;
    document.getElementById('statMerah').textContent  = 0;

    fetch(`index.php?page=peta&act=data_peta&id_tahun=${id_tahun}`)
        .then(res => res.json())
        .then(data => {
            let hijau = 0, kuning = 0, merah = 0;

            data.forEach(pos => {
                if (!pos.lat || !pos.lng) return;

                if (pos.kategori === 'hijau')  hijau++;
                if (pos.kategori === 'kuning') kuning++;
                if (pos.kategori === 'merah')  merah++;

                const circle = L.circleMarker([pos.lat, pos.lng], {
                    radius      : 18,
                    fillColor   : pos.warna,
                    color       : '#fff',
                    weight      : 2,
                    opacity     : 1,
                    fillOpacity : 0.85
                });

                circle.bindPopup(`
                    <div style="font-family:Segoe UI,sans-serif;min-width:210px;">
                        <div style="background:${pos.warna};color:#fff;padding:8px 12px;
                                    border-radius:6px 6px 0 0;margin:-10px -10px 10px;font-weight:700;">
                            📍 ${pos.nama_posyandu}
                        </div>
                        <table style="font-size:13px;width:100%;border-collapse:collapse;">
                            <tr><td style="padding:3px 0;">Dusun</td><td>: <b>${pos.nama_dusun}</b></td></tr>
                            <tr><td style="padding:3px 0;">Total Balita</td><td>: <b>${pos.total_balita}</b></td></tr>
                            <tr><td style="padding:3px 0;color:#43A047;">Normal</td><td>: <b>${pos.jml_normal}</b></td></tr>
                            <tr><td style="padding:3px 0;color:#FBC02D;">Berisiko</td><td>: <b>${pos.jml_beresiko}</b></td></tr>
                            <tr><td style="padding:3px 0;color:#e53935;">Stunting</td><td>: <b>${pos.jml_stunting}</b></td></tr>
                        </table>
                        <div style="margin-top:8px;padding:6px 10px;border-radius:6px;
                                    background:${pos.warna}22;color:${pos.warna};
                                    font-weight:700;font-size:13px;text-align:center;">
                            Kategori: ${pos.kategori.toUpperCase()}
                        </div>
                    </div>
                `, { maxWidth: 260 });

                circle.addTo(map);
                markers.push(circle);
            });

            // Update statistik
            document.getElementById('statHijau').textContent  = hijau;
            document.getElementById('statKuning').textContent = kuning;
            document.getElementById('statMerah').textContent  = merah;

            // Auto zoom ke semua marker
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.3));
            }
        })
        .catch(err => console.error('Error load peta:', err));
}

// Load awal
loadPeta('<?= $id_tahun_aktif ?>');

// Tombol filter
document.getElementById('btnFilter').addEventListener('click', function() {
    loadPeta(document.getElementById('filterTahun').value);
});

// Tombol reset
document.getElementById('btnReset').addEventListener('click', function() {
    document.getElementById('filterTahun').value = '';
    loadPeta('');
});
</script>

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>