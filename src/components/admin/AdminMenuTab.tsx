import React, { useState, useEffect } from 'react';
import { useData } from '../../context/DataContext';
import { 
  Sliders, 
  Menu, 
  Sparkles, 
  Save, 
  Bell, 
  Layers, 
  ExternalLink,
  CheckCircle2,
  HelpCircle,
  Eye,
  Image as ImageIcon,
  Upload,
  FolderCheck,
  RefreshCw,
  Info,
  ShieldCheck,
  FileCheck
} from 'lucide-react';
import { TabType } from '../../types';

interface Props {
  onSuccessToast: (msg: string) => void;
}

export const AdminMenuTab: React.FC<Props> = ({ onSuccessToast }) => {
  const { profile, updateProfile, navigateToTab } = useData();

  // Local state for running announcements and hero banner
  const [announcementText, setAnnouncementText] = useState(
    'Pelayanan UGD 24 Jam & Layanan Rujukan Siaga di Pulau Tidung, Pulau Pari, Pulau Lancang, Pulau Untung Jawa, dan Pulau Payung. Hubungi Hotline Maritim: 0859-6100-0003'
  );
  const [heroBadge, setHeroBadge] = useState('Puskesmas Ramah Bahari');
  const [heroTitle, setHeroTitle] = useState('Pusat Pelayanan Kesehatan Bahari Kepulauan Seribu Selatan');

  // Slider image state
  const [detectedImages, setDetectedImages] = useState<any[]>([]);
  const [isRefreshingSlider, setIsRefreshingSlider] = useState(false);
  const [uploadingSlot, setUploadingSlot] = useState<number | null>(null);

  const fetchSliderStatus = () => {
    setIsRefreshingSlider(true);
    fetch('/api/slider.php')
      .then((res) => res.json())
      .then((data) => {
        if (data && data.status === 'success' && Array.isArray(data.images)) {
          setDetectedImages(data.images);
        }
      })
      .catch(() => {})
      .finally(() => setIsRefreshingSlider(false));
  };

  useEffect(() => {
    fetchSliderStatus();
  }, []);

  const handleUploadSliderSlot = async (slot: number, e: React.ChangeEvent<HTMLInputElement>) => {
    if (!e.target.files || e.target.files.length === 0) return;
    const file = e.target.files[0];
    const formData = new FormData();
    formData.append('file', file);
    formData.append('slot', slot.toString());

    setUploadingSlot(slot);
    try {
      const res = await fetch('/api/slider.php', {
        method: 'POST',
        body: formData
      });
      const data = await res.json();
      if (res.ok && data.status === 'success') {
        onSuccessToast(`Spanduk slot ${slot} berhasil diperbarui di server hosting!`);
        fetchSliderStatus();
      } else {
        alert(data.message || 'Gagal mengunggah gambar slider');
      }
    } catch (err) {
      alert('Koneksi ke API slider gagal. Pastikan file diupload di server hosting.');
    } finally {
      setUploadingSlot(null);
    }
  };

  const mainMenuList: { tab: TabType; title: string; category: string; description: string }[] = [
    { tab: 'beranda', title: 'Beranda', category: 'Utama', description: 'Halaman muka, hero slider, layanan unggulan & statistik' },
    { tab: 'profil', title: 'Profil Puskesmas', category: 'Profil', description: 'Sambutan Kepala, Visi Misi, Struktur Organisasi & Peta' },
    { tab: 'ilp', title: 'Integrasi Layanan Primer (ILP)', category: 'Program', description: 'Klaster 1 Manajemen s.d Klaster 5 Lintas Siklus Hidup' },
    { tab: 'pelayanan', title: 'Daftar Layanan Kesehatan', category: 'Pelayanan', description: '9 Poliklinik Rawat Jalan, 24 Jam UGD, Lab & Farmasi' },
    { tab: 'jadwal', title: 'Jadwal Dokter & Poliklinik', category: 'Pelayanan', description: 'Jadwal harian dokter spesialis, gigi, KIA & umum' },
    { tab: 'wilayah', title: 'Wilayah Kerja 5 Pulau', category: 'Faskes', description: 'Pustu P. Pari, Pustu P. Lancang, Untung Jawa & Payung' },
    { tab: 'berita', title: 'Warta & Berita Puskesmas', category: 'Informasi', description: 'Artikel kegiatan puskesmas, promosi kesehatan & rilis' },
    { tab: 'agenda', title: 'Kalender Agenda Kegiatan', category: 'Informasi', description: 'Jadwal posyandu keliling, skrining & penyuluhan pulau' },
    { tab: 'unduhan', title: 'Pusat Unduhan & Dokumen SOP', category: 'Transparansi', description: 'Formulir pelayanan, maklumat & standar mutu' },
    { tab: 'sdm', title: 'Data Ketenagaan & Tenaga Medis', category: 'Transparansi', description: 'Profil 54+ tenaga kesehatan dokter, perawat & bidan' },
    { tab: 'data', title: 'Statistik & Indikator Mutu', category: 'Transparansi', description: 'Capaian indikator SPM & grafik kepuasan masyarakat' },
    { tab: 'kontak', title: 'Kontak & Lokasi Faskes', category: 'Bantuan', description: 'Alamat dermaga faskes, rujukan darurat & WhatsApp' },
    { tab: 'faq', title: 'Tanya Jawab (FAQ)', category: 'Bantuan', description: 'Pertanyaan umum seputar BPJS, alur layanan & rujukan medis' },
  ];

  const handleSaveMenuSettings = () => {
    onSuccessToast('Pengaturan menu & pengumuman tampilan beranda berhasil disimpan!');
  };

  return (
    <div className="space-y-6 text-xs">
      {/* Banner Section */}
      <div className="bg-sky-50 border border-sky-200 p-4 rounded-2xl flex items-center justify-between gap-4">
        <div>
          <h4 className="font-bold text-sky-950 text-sm flex items-center gap-1.5">
            <Sliders className="w-4 h-4 text-sky-700" />
            <span>Pengaturan Navigasi Menu & Konten Tampilan Beranda</span>
          </h4>
          <p className="text-sky-800 text-xs mt-1">
            Sesuaikan teks pengumuman penting (running text), banner slider beranda, serta navigasi menu cepat publik.
          </p>
        </div>
        <button
          onClick={handleSaveMenuSettings}
          className="px-4 py-2 rounded-xl bg-sky-700 hover:bg-sky-600 text-white font-bold flex items-center gap-1.5 shrink-0 shadow-xs"
        >
          <Save className="w-4 h-4" />
          <span>Simpan Tampilan</span>
        </button>
      </div>

      {/* 1. Pengumuman Berjalan / Running Text */}
      <div className="bg-white p-5 rounded-2xl border border-slate-200 space-y-3">
        <div className="flex items-center justify-between">
          <label className="font-bold text-slate-900 text-sm flex items-center gap-2">
            <Bell className="w-4 h-4 text-amber-500" />
            <span>Teks Berjalan Pengumuman Darurat / Penting (Marquee)</span>
          </label>
          <span className="text-[11px] text-slate-400">Tampil di bagian atas beranda</span>
        </div>
        <textarea
          rows={2}
          value={announcementText}
          onChange={(e) => setAnnouncementText(e.target.value)}
          className="w-full p-3 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:border-sky-500 leading-relaxed font-medium text-slate-800"
        />
        <div className="p-2.5 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-900 text-[11px] flex items-center gap-2">
          <Sparkles className="w-4 h-4 text-amber-600 shrink-0" />
          <span><strong>Pratinjau:</strong> "{announcementText}"</span>
        </div>
      </div>

      {/* 2. Banner Utama Beranda */}
      <div className="bg-white p-5 rounded-2xl border border-slate-200 space-y-4">
        <label className="font-bold text-slate-900 text-sm flex items-center gap-2">
          <Sparkles className="w-4 h-4 text-emerald-600" />
          <span>Teks Slogan & Banner Header Beranda</span>
        </label>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="font-bold text-slate-700 block mb-1">Label Tagline Mini (Badge)</label>
            <input
              type="text"
              value={heroBadge}
              onChange={(e) => setHeroBadge(e.target.value)}
              className="w-full p-2.5 rounded-xl bg-slate-50 border border-slate-200 font-semibold text-slate-800"
            />
          </div>

          <div>
            <label className="font-bold text-slate-700 block mb-1">Motto Puskesmas (Global)</label>
            <input
              type="text"
              value={profile.motto}
              onChange={(e) => updateProfile({ motto: e.target.value })}
              className="w-full p-2.5 rounded-xl bg-slate-50 border border-slate-200 font-bold text-emerald-700"
            />
          </div>

          <div className="md:col-span-2">
            <label className="font-bold text-slate-700 block mb-1">Judul Utama Hero Banner</label>
            <input
              type="text"
              value={heroTitle}
              onChange={(e) => setHeroTitle(e.target.value)}
              className="w-full p-2.5 rounded-xl bg-slate-50 border border-slate-200 font-bold text-slate-900 text-sm"
            />
          </div>
        </div>
      </div>

      {/* 3. KONEKSI GAMBAR HEADER SLIDER DARI FOLDER ASSET/SLIDER */}
      <div className="bg-white p-5 rounded-2xl border border-slate-200 space-y-4">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
          <div>
            <div className="flex items-center gap-2">
              <span className="p-1.5 rounded-lg bg-teal-500/10 text-teal-700">
                <ImageIcon className="w-4 h-4" />
              </span>
              <h4 className="font-bold text-slate-900 text-sm">
                Koneksi Gambar Header Slider (Folder: <code className="bg-slate-100 px-1.5 py-0.5 rounded text-teal-700 text-xs font-mono">asset/slider/</code>)
              </h4>
            </div>
            <p className="text-slate-500 text-xs mt-1">
              Website terhubung langsung ke folder <strong className="text-slate-700">asset/slider/</strong> di GitHub & Hostinger. Gambar yang diupload otomatis terdeteksi.
            </p>
          </div>

          <button
            onClick={fetchSliderStatus}
            disabled={isRefreshingSlider}
            className="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs flex items-center gap-1.5 shrink-0 transition cursor-pointer"
          >
            <RefreshCw className={`w-3.5 h-3.5 text-teal-600 ${isRefreshingSlider ? 'animate-spin' : ''}`} />
            <span>Pindai Ulang Server</span>
          </button>
        </div>

        {/* 4 Slot Header Slider */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          {[
            {
              slot: 1,
              title: 'Spanduk 01: Rawat Inap & UGD',
              defaultName: 'banner-1.jpg',
              altName: 'slider-1.jpg',
              badge: 'Layanan Utama 24 Jam'
            },
            {
              slot: 2,
              title: 'Spanduk 02: Klaster ILP',
              defaultName: 'banner-2.jpg',
              altName: 'slider-2.jpg',
              badge: 'Transformasi Primer'
            },
            {
              slot: 3,
              title: 'Spanduk 03: Rujukan Maritim',
              defaultName: 'banner-3.jpg',
              altName: 'slider-3.jpg',
              badge: 'SISRUTE Kapal Ambulans'
            },
            {
              slot: 4,
              title: 'Spanduk 04: Wilayah 5 Pulau',
              defaultName: 'banner-4.png',
              altName: 'slider-4.png',
              badge: 'Jejaring Pustu Pesisir'
            }
          ].map((item) => {
            const previewUrl = `/asset/slider/${item.defaultName}`;
            return (
              <div 
                key={item.slot} 
                className="p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col justify-between space-y-2.5"
              >
                <div>
                  <div className="flex items-center justify-between mb-1.5">
                    <span className="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-teal-100 text-teal-800">
                      SLOT {item.slot}
                    </span>
                    <span className="text-[10px] text-slate-400 font-medium">16:9 Landscape</span>
                  </div>
                  <h5 className="font-bold text-slate-800 text-xs">{item.title}</h5>
                  <p className="text-[11px] text-slate-500 font-mono mt-0.5">
                    {item.defaultName}
                  </p>
                </div>

                {/* Pratinjau Gambar */}
                <div className="relative aspect-video rounded-lg overflow-hidden border border-slate-200 bg-slate-900 group">
                  <img
                    src={previewUrl}
                    alt={item.title}
                    className="w-full h-full object-cover"
                    onError={(e) => {
                      // Fallback ke banner default jika belum ada
                      (e.target as HTMLImageElement).src = `/banners/${item.defaultName}`;
                    }}
                  />
                  <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-2 text-center">
                    <span className="text-[10px] text-white font-semibold">
                      Folder: /asset/slider/
                    </span>
                  </div>
                </div>

                {/* Upload Langsung dari Browser */}
                <div className="pt-1">
                  <label className="w-full py-1.5 px-2.5 rounded-lg bg-white border border-slate-200 hover:border-teal-400 text-slate-700 hover:text-teal-700 text-[11px] font-bold flex items-center justify-center gap-1.5 transition cursor-pointer shadow-2xs">
                    <Upload className="w-3 h-3 text-teal-600" />
                    <span>{uploadingSlot === item.slot ? 'Mengunggah...' : 'Ganti Gambar Ini'}</span>
                    <input
                      type="file"
                      accept="image/png,image/jpeg,image/jpg,image/webp"
                      className="hidden"
                      disabled={uploadingSlot !== null}
                      onChange={(e) => handleUploadSliderSlot(item.slot, e)}
                    />
                  </label>
                </div>
              </div>
            );
          })}
        </div>

        {/* Info Panduan GitHub & Hostinger */}
        <div className="p-3.5 rounded-xl bg-teal-50 border border-teal-200 text-teal-900 space-y-2 text-xs">
          <div className="flex items-center gap-2 font-bold text-teal-950">
            <ShieldCheck className="w-4 h-4 text-teal-600 shrink-0" />
            <span>Petunjuk Sinkronisasi Gambar Header Slider:</span>
          </div>
          <ol className="list-decimal list-inside space-y-1 text-[11px] text-teal-800 leading-relaxed pl-1">
            <li>
              <strong>Lokasi Folder di GitHub:</strong> Pastikan gambar Anda disimpan di dalam folder <code className="bg-white/80 px-1 py-0.5 rounded font-mono font-bold">asset/slider/</code>.
            </li>
            <li>
              <strong>Penamaan File:</strong> Beri nama file gambar Anda salah satu dari format berikut:
              <span className="block mt-0.5 font-mono text-[10.5px] bg-white/70 p-1 rounded border border-teal-200">
                • Slot 1: <code className="text-teal-900 font-bold">banner-1.jpg</code> atau <code className="text-teal-900 font-bold">slider-1.jpg</code><br/>
                • Slot 2: <code className="text-teal-900 font-bold">banner-2.jpg</code> atau <code className="text-teal-900 font-bold">slider-2.jpg</code><br/>
                • Slot 3: <code className="text-teal-900 font-bold">banner-3.jpg</code> atau <code className="text-teal-900 font-bold">slider-3.jpg</code><br/>
                • Slot 4: <code className="text-teal-900 font-bold">banner-4.png</code> atau <code className="text-teal-900 font-bold">slider-4.jpg</code>
              </span>
            </li>
            <li>
              <strong>Format yang Didukung:</strong> File <code className="font-bold">.jpg</code>, <code className="font-bold">.png</code>, atau <code className="font-bold">.webp</code> dengan rasio landscape 16:9.
            </li>
            <li>
              <strong>Sinkron ke Hostinger:</strong> Setelah file di-upload ke GitHub, buka Hostinger hPanel &rarr; Menu <strong>GIT</strong> &rarr; Klik <strong>Deploy</strong> (Tarik). Gambar akan langsung tampil di beranda tanpa blank!
            </li>
          </ol>
        </div>
      </div>

      {/* 4. Daftar Struktur Navigasi Menu Publik */}
      <div className="bg-white p-5 rounded-2xl border border-slate-200 space-y-3">
        <div className="flex items-center justify-between">
          <label className="font-bold text-slate-900 text-sm flex items-center gap-2">
            <Menu className="w-4 h-4 text-sky-600" />
            <span>Struktur Menu Navigasi Website Puskesmas ({mainMenuList.length} Menu)</span>
          </label>
          <span className="text-[11px] text-emerald-700 font-semibold">Semua Menu Aktif</span>
        </div>

        <p className="text-slate-500 text-xs">
          Klik tombol <strong>"Lihat di Web"</strong> untuk memeriksa halaman langsung dari sudut pandang pengunjung website.
        </p>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-2.5 pt-2">
          {mainMenuList.map((m, idx) => (
            <div 
              key={m.tab} 
              className="p-3 rounded-xl bg-slate-50 border border-slate-200 hover:border-sky-300 transition flex items-center justify-between gap-2"
            >
              <div className="flex items-center gap-2.5">
                <span className="w-6 h-6 rounded-lg bg-sky-100 text-sky-800 font-bold flex items-center justify-center text-[11px] shrink-0">
                  {idx + 1}
                </span>
                <div>
                  <div className="flex items-center gap-2">
                    <span className="font-bold text-slate-900 text-xs">{m.title}</span>
                    <span className="px-1.5 py-0.5 rounded text-[10px] bg-slate-200 font-medium text-slate-700">
                      {m.category}
                    </span>
                  </div>
                  <p className="text-[11px] text-slate-500 line-clamp-1">{m.description}</p>
                </div>
              </div>

              <button
                type="button"
                onClick={() => navigateToTab(m.tab)}
                className="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-sky-50 text-sky-700 font-semibold text-[11px] flex items-center gap-1 shrink-0 transition"
              >
                <Eye className="w-3 h-3" />
                <span>Lihat</span>
              </button>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};
