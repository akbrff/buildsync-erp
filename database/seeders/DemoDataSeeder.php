<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\CashFlow;
use App\Models\Material;
use App\Models\ProgressUpdate;
use App\Models\Project;
use App\Models\ProjectSchedule;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerAssignment;
use App\Models\WorkerAttendance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedWorkers();
            $this->seedUsers();
            $this->seedProjects();
            $this->seedProjectSchedulesAndProgress();
            $this->seedMaterials();
            $this->seedSuppliers();
            $this->seedPurchaseOrdersAndStock();
            $this->seedWorkerAssignmentsAndAttendance();
            $this->seedBudgets();
            $this->seedCashFlows();
        });
    }

    /**
     * 1. Seed 30 Construction Workers
     */
    protected function seedWorkers(): void
    {
        $workerData = [
            ['WKR-001', 'Joko Prasetyo', 'Mandor Konstruksi & Sipil', '081234567801', 220000],
            ['WKR-002', 'Bambang Sutrisno', 'Tukang Besi & Pembesian', '081234567802', 175000],
            ['WKR-003', 'Agus Supriyadi', 'Tukang Cor Beton', '081234567803', 165000],
            ['WKR-004', 'Slamet Riyadi', 'Tukang Batu & Pasang Bata', '081234567804', 160000],
            ['WKR-005', 'Dedi Kurniawan', 'Tukang Plester & Acian', '081234567805', 160000],
            ['WKR-006', 'Hendra Gunawan', 'Tukang Kayu & Bekisting', '081234567806', 170000],
            ['WKR-007', 'Eko Prasetya', 'Tukang Rangka Baja Ringan', '081234567807', 175000],
            ['WKR-008', 'Wahyu Hidayat', 'Tukang Pasang Keramik & Granit', '081234567808', 180000],
            ['WKR-009', 'Sugeng Wibowo', 'Tukang Plafon Gypsum', '081234567809', 165000],
            ['WKR-010', 'Rudi Hermawan', 'Tukang Cat & Finishing', '081234567810', 155000],
            ['WKR-011', 'Mulyono', 'Operator Excavator & Backhoe', '081234567811', 240000],
            ['WKR-012', 'Suparno', 'Operator Tower Crane', '081234567812', 250000],
            ['WKR-013', 'Anto Wijaya', 'Surveyor Proyek & Pemetaan', '081234567813', 230000],
            ['WKR-014', 'Arif Budiman', 'Tukang Pipa & Plumbing ME', '081234567814', 175000],
            ['WKR-015', 'Tri Santoso', 'Tukang Listrik Arus Kuat', '081234567815', 185000],
            ['WKR-016', 'Sunardi', 'Pekerja Pembongkaran / Demolisi', '081234567816', 140000],
            ['WKR-017', 'Yanto Sukamto', 'Tukang Las Karbit & Listrik', '081234567817', 190000],
            ['WKR-018', 'Budi Santoso', 'Tukang Pasang Paving Block', '081234567818', 150000],
            ['WKR-019', 'Darmawan', 'Helper / Tenaga Kasar Angkut', '081234567819', 130000],
            ['WKR-020', 'Sutrisno', 'Tukang Gali Pondasi & Tanah', '081234567820', 135000],
            ['WKR-021', 'Herman Syah', 'Teknisi Mesin Molen Beton', '081234567821', 170000],
            ['WKR-022', 'Purwanto', 'Tukang Pasang Waterproofing', '081234567822', 175000],
            ['WKR-023', 'Rohmat Hidayat', 'Tukang Pasang Kusen Aluminium', '081234567823', 180000],
            ['WKR-024', 'Danang Setiawan', 'Teknisi Scaffolding / Perancah', '081234567824', 165000],
            ['WKR-025', 'Heri Kiswanto', 'Tukang Pasang Kaca Gedung', '081234567825', 185000],
            ['WKR-026', 'Aris Munandar', 'Helper Bongkar Muat Material', '081234567826', 130000],
            ['WKR-027', 'Gunawan Wibisono', 'Mandor Finishing Arsitektur', '081234567827', 210000],
            ['WKR-028', 'Maryanto', 'Tukang Cor Jalan Rigid Pavement', '081234567828', 165000],
            ['WKR-029', 'Nur Kholis', 'Tukang Pasang Saluran U-Ditch', '081234567829', 170000],
            ['WKR-030', 'Edi Subagyo', 'Operator Vibrator & Stamper', '081234567830', 155000],
        ];

        foreach ($workerData as $item) {
            Worker::updateOrCreate(
                ['worker_code' => $item[0]],
                [
                    'name' => $item[1],
                    'position' => $item[2],
                    'phone' => $item[3],
                    'daily_rate' => $item[4],
                    'status' => 'active',
                    'notes' => 'Tenaga kerja lapangan terverifikasi sertifikasi kompetensi.',
                ]
            );
        }
    }

    /**
     * 2. Seed Exactly One Demo User per Application Role
     * The first three users must have these exact names:
     * 1. AHMAD SAUQI ZAMZAMI -> superadmin
     * 2. MUHAMAD AKBAR FEBIANSYAH -> project_manager
     * 3. MUHAMMAD FADHILLAH AKBAR -> logistics
     * 4. SITI NURHALIZA -> finance
     * 5. JOKO PRASETYO -> field_staff (linked to worker WKR-001)
     */
    protected function seedUsers(): void
    {
        $fieldWorker = Worker::where('worker_code', 'WKR-001')->first();

        $demoUsers = [
            [
                'email' => 'admin@buildsync.com',
                'name' => 'AHMAD SAUQI ZAMZAMI',
                'role' => 'superadmin',
                'worker_id' => null,
            ],
            [
                'email' => 'akbar.febiansyah@buildsync.com',
                'name' => 'MUHAMAD AKBAR FEBIANSYAH',
                'role' => 'project_manager',
                'worker_id' => null,
            ],
            [
                'email' => 'fadhillah.akbar@buildsync.com',
                'name' => 'MUHAMMAD FADHILLAH AKBAR',
                'role' => 'logistics',
                'worker_id' => null,
            ],
            [
                'email' => 'siti.nurhaliza@buildsync.com',
                'name' => 'SITI NURHALIZA',
                'role' => 'finance',
                'worker_id' => null,
            ],
            [
                'email' => 'joko.prasetyo@buildsync.com',
                'name' => 'JOKO PRASETYO',
                'role' => 'field_staff',
                'worker_id' => $fieldWorker?->id,
            ],
        ];

        foreach ($demoUsers as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'role' => $u['role'],
                    'worker_id' => $u['worker_id'],
                    'status' => 'active',
                    'locale' => 'en',
                ]
            );
        }
    }

    /**
     * 3. Seed 30 Realistic Indonesian Construction Projects
     */
    protected function seedProjects(): void
    {
        $projects = [
            ['PRJ-2026-001', 'Pembangunan Gedung Rawat Inap RSUD Tarakan', 'Jakarta Pusat', '2026-01-10', '2026-12-20', 32500000000, 'ongoing', 'Pembangunan gedung 8 lantai dengan standar fasilitas kesehatan terpadu.'],
            ['PRJ-2026-002', 'Pembangunan Flyover Simpang Antapani', 'Bandung, Jawa Barat', '2026-01-15', '2026-11-30', 24800000000, 'ongoing', 'Konstruksi jembatan layang rangka baja komposit untuk mengurai kemacetan antarkota.'],
            ['PRJ-2026-003', 'Konstruksi Jembatan Penyeberangan Cisadane', 'Tangerang, Banten', '2026-02-01', '2026-10-15', 14200000000, 'ongoing', 'Jembatan pedestrian bentang 120 meter dengan struktur kabel baja berkekuatan tinggi.'],
            ['PRJ-2026-004', 'Revitalisasi Stasiun Kereta Api Pasar Turi', 'Surabaya, Jawa Timur', '2026-01-20', '2026-09-30', 18500000000, 'ongoing', 'Pekerjaan perpanjangan peron, kanopi baja modern, dan fasilitas akses difabel.'],
            ['PRJ-2026-005', 'Kompleks Pergudangan Logistik Cikarang Dry Port', 'Bekasi, Jawa Barat', '2026-02-10', '2026-12-31', 28000000000, 'ongoing', 'Pembangunan 4 unit gudang rangka baja bentang lebar dengan fasilitas cross-docking.'],
            ['PRJ-2026-006', 'Gedung Laboratorium Terpadu Universitas Diponegoro', 'Semarang, Jawa Tengah', '2026-02-15', '2026-11-15', 16750000000, 'ongoing', 'Pembangunan gedung lab 5 lantai lengkap dengan instalasi gas medis dan clean room.'],
            ['PRJ-2026-007', 'Peningkatan Kapasitas Drainase Utama Tol Tomang', 'Jakarta Barat', '2026-01-05', '2026-07-25', 9500000000, 'ongoing', 'Pekerjaan gorong-gorong box culvert precast 3x3 meter sepanjang 2,5 kilometer.'],
            ['PRJ-2026-008', 'Pembangunan Kantor Pusat Cabang Bank Jatim', 'Malang, Jawa Timur', '2026-02-20', '2026-11-20', 12300000000, 'ongoing', 'Gedung perbankan 4 lantai dengan fasad curtain wall kaca double-glazing.'],
            ['PRJ-2026-009', 'Pusat Distribusi Farmasi Nasional Cengkareng', 'Jakarta Barat', '2026-03-01', '2026-12-15', 21000000000, 'ongoing', 'Fasilitas cold chain storage suhu terkendali untuk penyimpanan vaksin nasional.'],
            ['PRJ-2026-010', 'Tanggul Pengendali Banjir Kali Lamong', 'Gresik, Jawa Timur', '2026-01-12', '2026-10-30', 35000000000, 'ongoing', 'Konstruksi tanggul pasangan batu kali dan sheet pile baja sepanjang 4,2 km.'],
            ['PRJ-2026-011', 'Pekerjaan Struktur Tower Apartemen Sudirman Hill', 'Jakarta Selatan', '2026-01-08', '2027-04-30', 45000000000, 'ongoing', 'Pekerjaan substruktur dan struktur atas beton bertulang tower hunian 25 lantai.'],
            ['PRJ-2026-012', 'Instalasi Pengolahan Air Bersih PDAM Tirta Kencana', 'Samarinda, Kalimantan Timur', '2026-02-25', '2026-12-10', 19800000000, 'ongoing', 'IPA kapasitas 200 liter/detik lengkap dengan reservoir beton 3.000 m3.'],
            ['PRJ-2026-013', 'Renovasi Terminal Domestik Bandara Sepinggan', 'Balikpapan, Kalimantan Timur', '2026-01-18', '2026-08-31', 15600000000, 'ongoing', 'Pekerjaan interior keberangkatan, sistem tata udara HVAC, dan peremajaan boarding lounge.'],
            ['PRJ-2026-014', 'Puskesmas Rawat Inap Modern Kec. Sukasari', 'Bogor, Jawa Barat', '2025-05-10', '2025-12-20', 6800000000, 'completed', 'Puskesmas 2 lantai dengan kapasitas 20 tempat tidur rawat inap, rampung 100%.'],
            ['PRJ-2026-015', 'Penataan Kawasan Wisata Bahari Pantai Melasti', 'Badung, Bali', '2025-04-01', '2025-11-25', 11200000000, 'completed', 'Pembangunan plaza pedestrian, anjungan pandang, dan seawall penahan abrasi.'],
            ['PRJ-2026-016', 'Gedung Sekolah Vokasi Politeknik Sriwijaya', 'Palembang, Sumatera Selatan', '2025-06-01', '2025-12-28', 17400000000, 'completed', 'Pembangunan gedung workshop teknik mesin dan sipil 4 lantai, telah diserahterimakan.'],
            ['PRJ-2026-017', 'Dermaga Sandar Kapal Cepat Pelabuhan Sanur', 'Denpasar, Bali', '2025-03-15', '2025-10-30', 22900000000, 'completed', 'Dermaga ponton apung baja dan trestle beton precast menuju kapal cepat Nusa Penida.'],
            ['PRJ-2026-018', 'Silo Penyimpanan Semen Curah Ciwandan', 'Cilegon, Banten', '2025-02-10', '2025-11-15', 26500000000, 'completed', 'Konstruksi 2 unit silo beton slipform kapasitas masing-masing 10.000 ton semen.'],
            ['PRJ-2026-019', 'Revitalisasi Pasar Tradisional Modern BSD City', 'Tangerang Selatan, Banten', '2025-05-20', '2025-12-31', 13800000000, 'completed', 'Pembangunan 200 unit kios semi-modern, area food court higienis, dan IPAL limbah.'],
            ['PRJ-2026-020', 'Pemasangan Jaringan Pipa Gas Bumi KIIC', 'Karawang, Jawa Barat', '2025-06-15', '2025-12-15', 8700000000, 'completed', 'Jalur pipa baja API 5L diameter 8 inch sepanjang 6 km bawah tanah sistem HDD.'],
            ['PRJ-2026-021', 'Gedung Data Center Tier-3 Jababeka Phase II', 'Cikarang, Jawa Barat', '2026-05-01', '2027-02-28', 38000000000, 'planning', 'Fasilitas data center berdaya ganda 15 MW dengan sistem proteksi gempa seismik.'],
            ['PRJ-2026-022', 'Underpass Simpang Kentungan Tahap 2', 'Sleman, D.I. Yogyakarta', '2026-06-01', '2027-03-31', 27200000000, 'planning', 'Konstruksi terowongan bawah tanah jalur lingkar utara Yogyakarta sepanjang 850 meter.'],
            ['PRJ-2026-023', 'Hanggar Perawatan Pesawat MRO Bandara Kertajati', 'Majalengka, Jawa Barat', '2026-05-15', '2027-06-30', 31500000000, 'planning', 'Hanggar bentang lebar 90 meter sanggup menampung 2 unit pesawat berbadan sedang.'],
            ['PRJ-2026-024', 'Fasilitas Pengendali Erosi Tebing Tol Cipularang', 'Purwakarta, Jawa Barat', '2026-04-10', '2026-11-30', 10400000000, 'planning', 'Pemasangan soil nailing dan shotcrete tebing rawan longsor KM 92-96.'],
            ['PRJ-2026-025', 'Instalasi Pengolahan Limbah B3 Kawasan Kendal', 'Kendal, Jawa Tengah', '2026-06-15', '2027-04-15', 23600000000, 'planning', 'Pabrik insinerator limbah industri dan stabilisasi kimiawi berizin lingkungan.'],
            ['PRJ-2026-026', 'Asrama Mahasiswa Terpadu UIN Sunan Kalijaga', 'Sleman, D.I. Yogyakarta', '2026-05-20', '2027-01-20', 15000000000, 'planning', 'Gedung asrama 6 lantai kapasitas 500 mahasiswa bersubsidi kementerian agama.'],
            ['PRJ-2026-027', 'Jembatan Gantung Wisata Curug Cilember', 'Megamendung, Bogor', '2026-02-01', '2026-08-30', 5200000000, 'on_hold', 'Pekerjaan ditunda sementara menunggu hasil uji kelayakan geoteknik lereng tebing.'],
            ['PRJ-2026-028', 'Sentra UMKM Kuliner Pesisir Kenjeran', 'Surabaya, Jawa Timur', '2026-01-25', '2026-07-31', 7800000000, 'on_hold', 'Penyesuaian perizinan batas sempadan pantai dan koordinasi warga lokal.'],
            ['PRJ-2026-029', 'Tempat Pengolahan Sampah Terpadu TPST Bantargebang', 'Bekasi, Jawa Barat', '2026-07-01', '2027-05-31', 29400000000, 'planning', 'Konstruksi hanggar RDF (Refuse Derived Fuel) kapasitas olah 1.000 ton sampah/hari.'],
            ['PRJ-2026-030', 'Gardu Induk Distribusi Listrik 150kV Tanjung Emas', 'Semarang, Jawa Tengah', '2026-03-01', '2026-12-20', 20100000000, 'ongoing', 'Pekerjaan sipil gedung kontrol switchgear GIS dan pondasi trafo 60 MVA.'],
        ];

        foreach ($projects as $p) {
            Project::updateOrCreate(
                ['project_code' => $p[0]],
                [
                    'name' => $p[1],
                    'location' => $p[2],
                    'start_date' => $p[3],
                    'end_date' => $p[4],
                    'budget' => $p[5],
                    'status' => $p[6],
                    'description' => $p[7],
                ]
            );
        }
    }

    /**
     * 4. Seed 30 Project Schedules & 30 Progress Updates
     * Ensure total cumulative weight of stages for each project is strictly <= 100.00%.
     */
    protected function seedProjectSchedulesAndProgress(): void
    {
        $projectModels = Project::orderBy('id')->get()->keyBy('project_code');

        // We define 30 schedules across projects. Each project's stages sum to <= 100% weight.
        $scheduleData = [
            // PRJ-001 (ongoing, total weight: 45 + 55 = 100)
            ['PRJ-2026-001', 'Pekerjaan Pondasi Bored Pile & Pile Cap', '2026-01-10', '2026-04-30', 45.00, 100.0, 'Pondasi 48 titik bored pile diameter 80cm selesai sempurna.'],
            ['PRJ-2026-001', 'Struktur Kolom, Balok & Pelat Lantai 1-4', '2026-05-01', '2026-08-31', 55.00, 68.5, 'Pengecoran plat lantai 3 sedang berlangsung, deviasi aman.'],

            // PRJ-002 (ongoing, total weight: 40 + 60 = 100)
            ['PRJ-2026-002', 'Pondasi Pier Abutment & Tiang Pancang', '2026-01-15', '2026-05-15', 40.00, 100.0, 'Pemancangan spun pile kedalaman 24 meter rampung.'],
            ['PRJ-2026-002', 'Ereksi Girder Baja & Deck Slab Jembatan', '2026-05-16', '2026-11-30', 60.00, 42.0, 'Pemasangan 6 girder bentang 40m dari total 12 girder.'],

            // PRJ-003 (ongoing, total weight: 50 + 50 = 100)
            ['PRJ-2026-003', 'Fabrikasi Struktur Baja & Pylon Jembatan', '2026-02-01', '2026-06-15', 50.00, 85.0, 'Fabrikasi pylon baja di workshop telah mencapai 85%.'],
            ['PRJ-2026-003', 'Pemasangan Kabel Stay & Lantai Komposit', '2026-06-16', '2026-10-15', 50.00, 15.0, 'Persiapan angkur kabel stay dan uji tarik laboratorium.'],

            // PRJ-004 (ongoing, weight 100)
            ['PRJ-2026-004', 'Perpanjangan Peron & Struktur Kanopi Stasiun', '2026-01-20', '2026-09-30', 100.00, 58.0, 'Pemasangan kanopi baja bentang 30 meter di peron utara.'],

            // PRJ-005 (ongoing, weight 100)
            ['PRJ-2026-005', 'Struktur Portal Rangka Baja Pergudangan', '2026-02-10', '2026-12-31', 100.00, 48.0, 'Ereksi portal baja gudang blok A dan B sedang dikerjakan.'],

            // PRJ-006 (ongoing, weight 100)
            ['PRJ-2026-006', 'Pekerjaan Struktur Utama & Fasad Cleanroom', '2026-02-15', '2026-11-15', 100.00, 52.0, 'Struktur lt. 4 selesai cor, pemasangan partisi sandwich panel.'],

            // PRJ-007 (ongoing, weight 100)
            ['PRJ-2026-007', 'Galian Saluran & Instalasi Box Culvert 3x3m', '2026-01-05', '2026-07-25', 100.00, 75.0, 'Pemasangan 1.800 unit box culvert precast sepanjang tol.'],

            // PRJ-008 (ongoing, weight 100)
            ['PRJ-2026-008', 'Struktur Fasad Kaca Curtain Wall & ME Perbankan', '2026-02-20', '2026-11-20', 100.00, 38.0, 'Pemasangan bracket aluminium dan instalasi tray kabel listrik.'],

            // PRJ-009 (ongoing, weight 100)
            ['PRJ-2026-009', 'Insulasi Dinding Cold Storage & Chiller Plant', '2026-03-01', '2026-12-15', 100.00, 30.0, 'Pemasangan panel insulasi PIR 150mm dan unit outdoor chiller.'],

            // PRJ-010 (ongoing, weight 100)
            ['PRJ-2026-010', 'Pemancangan Sheet Pile Baja & Pasangan Batu Kali', '2026-01-12', '2026-10-30', 100.00, 62.0, 'Pemancangan tanggul bantaran hilir mencapai 2,6 km.'],

            // PRJ-011 (ongoing, total weight: 35 + 65 = 100)
            ['PRJ-2026-011', 'Pekerjaan Basement 3 Lapis & Raft Foundation', '2026-01-08', '2026-06-30', 35.00, 95.0, 'Pengecoran raft foundation tebal 2 meter hampir selesai.'],
            ['PRJ-2026-011', 'Struktur Atas Podium & Hunian Lt. 1-12', '2026-07-01', '2027-04-30', 65.00, 10.0, 'Persiapan pembesian kolom podium lantai 1.'],

            // PRJ-012 (ongoing, weight 100)
            ['PRJ-2026-012', 'Konstruksi Bak Sedimentasi & Reservoir Beton', '2026-02-25', '2026-12-10', 100.00, 44.0, 'Pengecoran dinding kedap air reservoir kapasitas 3.000 m3.'],

            // PRJ-013 (ongoing, weight 100)
            ['PRJ-2026-013', 'Interior Boarding Gate & Peremajaan HVAC', '2026-01-18', '2026-08-31', 100.00, 65.0, 'Penggantian ducting AC sentral dan plafon drop ceiling.'],

            // PRJ-014 (completed, weight 100)
            ['PRJ-2026-014', 'Pembangunan Total Gedung Puskesmas 2 Lantai', '2025-05-10', '2025-12-20', 100.00, 100.0, 'Proyek selesai 100%, PHO dan FHO tuntas tanpa catatan cacat mutu.'],

            // PRJ-015 (completed, weight 100)
            ['PRJ-2026-015', 'Pekerjaan Plaza Pedestrian & Seawall Pantai', '2025-04-01', '2025-11-25', 100.00, 100.0, 'Pekerjaan lanskap dan penahan ombak telah beroperasi penuh.'],

            // PRJ-016 (completed, weight 100)
            ['PRJ-2026-016', 'Struktur & Arsitektur Gedung Workshop Vokasi', '2025-06-01', '2025-12-28', 100.00, 100.0, 'Pekerjaan arsitektur dan kelistrikan selesai, telah diresmikan.'],

            // PRJ-017 (completed, weight 100)
            ['PRJ-2026-017', 'Fabrikasi Ponton Apung & Trestle Beton Dermaga', '2025-03-15', '2025-10-30', 100.00, 100.0, 'Dermaga telah melayani rute kapal cepat Denpasar-Nusa Penida.'],

            // PRJ-018 (completed, weight 100)
            ['PRJ-2025-018', 'Silo Beton Slipform & Sistem Konveyor Semen', '2025-02-10', '2025-11-15', 100.00, 100.0, 'Uji coba komisioning conveyor dan pengisian semen curah sukses.'],

            // PRJ-019 (completed, weight 100)
            ['PRJ-2026-019', 'Pembangunan Kios Pasar & Instalasi IPAL Modern', '2025-05-20', '2025-12-31', 100.00, 100.0, 'Seluruh unit kios pasar telah diserahkan kepada pengelola PD Pasar.'],

            // PRJ-020 (completed, weight 100)
            ['PRJ-2026-020', 'Pemasangan Pipa Gas Bawah Tanah Sistem HDD', '2025-06-15', '2025-12-15', 100.00, 100.0, 'Uji hidrostatik tekanan 60 bar lolos tanpa kebocoran.'],

            // PRJ-021 (planning, weight 100)
            ['PRJ-2026-021', 'Penyelidikan Tanah Deep Boring & Desain Struktur', '2026-05-01', '2026-07-31', 100.00, 0.0, 'Menunggu finalisasi kajian daya dukung tanah dan perizinan PBG.'],

            // PRJ-022 (planning, weight 100)
            ['PRJ-2026-022', 'Pembersihan Lahan & Relokasi Utilitas Pipa/Kabel', '2026-06-01', '2026-08-31', 100.00, 0.0, 'Koordinasi relokasi pipa PDAM dan kabel fiber optik lintasan.'],

            // PRJ-023 (planning, weight 100)
            ['PRJ-2026-023', 'Desain Rinci Space Frame Baja Hanggar MRO', '2026-05-15', '2026-08-15', 100.00, 0.0, 'Tahap review shop drawing kuda-kuda space frame bentang 90m.'],

            // PRJ-024 (planning, weight 100)
            ['PRJ-2026-024', 'Persiapan Material Geotekstil & Soil Nailing', '2026-04-10', '2026-06-30', 100.00, 0.0, 'Pengadaan angkur baja dan mesin compressor shotcrete tebing.'],

            // PRJ-025 (planning, weight 100)
            ['PRJ-2026-025', 'Pondasi Mesin Insinerator & Cerobong Emisi', '2026-06-15', '2026-10-31', 100.00, 0.0, 'Kajian AMDAL telah disetujui, persiapan tender vendor insinerator.'],

            // PRJ-026 (planning, weight 100)
            ['PRJ-2026-026', 'Pekerjaan Persiapan Lahan & Direksi Keet', '2026-05-20', '2026-07-20', 100.00, 0.0, 'Pemasangan pagar proyek dan penataan akses jalan truk material.'],

            // PRJ-027 (on_hold, weight 100)
            ['PRJ-2026-027', 'Pondasi Blok Angkur Kabel Jembatan Gantung', '2026-02-01', '2026-05-31', 100.00, 20.0, 'Pekerjaan dihentikan sementara menunggu mitigasi risiko pergeseran lereng.'],

            // PRJ-028 (on_hold, weight 100)
            ['PRJ-2026-028', 'Pemasangan Tiang Pancang Mini Pile Pesisir', '2026-01-25', '2026-04-30', 100.00, 15.0, 'Penghentian sementara terkait penyesuaian tata ruang zonasi pesisir.'],

            // PRJ-029 (planning, weight 100)
            ['PRJ-2026-029', 'Pekerjaan Cut and Fill & Land Levelling Zona 4', '2026-07-01', '2026-09-30', 100.00, 0.0, 'Persiapan alat berat dozer dan excavator long-arm pematangan lahan.'],

            // PRJ-030 (ongoing, weight 100)
            ['PRJ-2026-030', 'Pondasi Transformator 60 MVA & Gedung Kontrol GIS', '2026-03-01', '2026-08-31', 100.00, 35.0, 'Pengecoran plat landasan trafo daya dan struktur bawah gedung GIS.'],
        ];

        // Notice: PRJ-018 in array had typo 'PRJ-2025-018' -> correct to 'PRJ-2026-018'
        foreach ($scheduleData as $idx => $s) {
            $prjCode = $s[0] === 'PRJ-2025-018' ? 'PRJ-2026-018' : $s[0];
            $project = $projectModels->get($prjCode);

            if (! $project) {
                continue;
            }

            $schedule = ProjectSchedule::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'name' => $s[1],
                ],
                [
                    'start_date' => $s[2],
                    'end_date' => $s[3],
                    'weight' => $s[4],
                    'description' => 'Tahapan pekerjaan konstruksi terjadwal dalam Master Work Breakdown Structure.',
                ]
            );

            // Record progress update
            $recDate = $s[5] > 0 ? Carbon::parse($s[2])->addDays(20)->toDateString() : $s[2];
            ProgressUpdate::updateOrCreate(
                [
                    'project_schedule_id' => $schedule->id,
                    'recorded_at' => $recDate,
                ],
                [
                    'progress_percentage' => $s[5],
                    'notes' => $s[6],
                    'is_correction' => false,
                ]
            );
        }
    }

    /**
     * 5. Seed 30 Standard Construction Materials
     */
    protected function seedMaterials(): void
    {
        $materials = [
            ['MAT-001', 'Semen Portland Komposit (PCC) 50kg', 'sak', 68500, 1200, 200],
            ['MAT-002', 'Besi Beton Ulir D16 SNI TS280', 'batang', 175000, 850, 150],
            ['MAT-003', 'Besi Beton Ulir D13 SNI TS280', 'batang', 115000, 920, 150],
            ['MAT-004', 'Besi Beton Polos D10 SNI TP280', 'batang', 72000, 1100, 200],
            ['MAT-005', 'Pasir Pasang Kali Muntilan Pilihan', 'm3', 285000, 450, 80],
            ['MAT-006', 'Pasir Cor Extra Kasar Bebas Lumpur', 'm3', 320000, 380, 70],
            ['MAT-007', 'Batu Pecah Split 1-2 Standar Cor', 'm3', 295000, 520, 90],
            ['MAT-008', 'Ready Mix Beton Cor K-300 Slump 12', 'm3', 860000, 150, 30],
            ['MAT-009', 'Ready Mix Beton Cor K-350 Slump 12', 'm3', 920000, 120, 25],
            ['MAT-010', 'Bata Ringan AAC 10x20x60 cm Presisi', 'm3', 650000, 240, 50],
            ['MAT-011', 'Mortar Perekat Bata Ringan ThinBed 40kg', 'sak', 85000, 600, 100],
            ['MAT-012', 'Pipa PVC AW Diameter 3 Inch Wavin', 'batang', 145000, 320, 60],
            ['MAT-013', 'Pipa PVC AW Diameter 4 Inch Wavin', 'batang', 230000, 280, 50],
            ['MAT-014', 'Baja Ringan Kuda-Kuda Truss C75.075', 'batang', 88000, 750, 120],
            ['MAT-015', 'Reng Baja Ringan Profil U R32.045', 'batang', 42000, 880, 150],
            ['MAT-016', 'Genteng Metal Berpasir Ukuran 2x4', 'lembar', 48000, 1400, 250],
            ['MAT-017', 'Papan Gypsum Board 9mm Elephant', 'lembar', 74000, 540, 100],
            ['MAT-018', 'Rangka Plafon Hollow Galvalum 4x4', 'batang', 36000, 680, 120],
            ['MAT-019', 'Cat Dinding Eksterior Weathershield 20L', 'pail', 1450000, 65, 15],
            ['MAT-020', 'Cat Dinding Interior Dulux Catylac 20L', 'pail', 980000, 95, 20],
            ['MAT-021', 'Keramik Lantai Granit Polish 60x60', 'dus', 185000, 720, 120],
            ['MAT-022', 'Keramik Dinding Kamar Mandi 30x60', 'dus', 95000, 430, 80],
            ['MAT-023', 'Wiremesh M8 Ulir Lembaran SNI 2.1x5.4m', 'lembar', 485000, 180, 35],
            ['MAT-024', 'Kawat Bendrat Pengikat Beton Galvanis', 'rol', 340000, 85, 20],
            ['MAT-025', 'Multiplek Phenolic Film 18mm Bekisting', 'lembar', 245000, 310, 60],
            ['MAT-026', 'Kayu Kaso 5x7 cm Kayu Meranti Ikat', 'm3', 3200000, 45, 10],
            ['MAT-027', 'Waterproofing Coating Elastomeric 20kg', 'pail', 720000, 110, 25],
            ['MAT-028', 'Kabel Tembaga NYM 3x2.5mm Supreme 100m', 'rol', 850000, 90, 20],
            ['MAT-029', 'Lampu LED Downlight Panel 12W Philips', 'pcs', 58000, 450, 80],
            ['MAT-030', 'Bahan Bakar Solar Industri Non-Subsidi', 'liter', 18500, 3500, 800],
        ];

        foreach ($materials as $m) {
            Material::updateOrCreate(
                ['code' => $m[0]],
                [
                    'name' => $m[1],
                    'unit' => $m[2],
                    'price' => $m[3],
                    'stock' => $m[4],
                    'min_stock' => $m[5],
                ]
            );
        }
    }

    /**
     * 6. Seed 30 Indonesian Construction Suppliers
     */
    protected function seedSuppliers(): void
    {
        $suppliers = [
            ['SUP-001', 'PT Semen Indonesia Distributor', 'Bambang Irawan', '021-5256781', 'distribusi@semenindonesia.co.id', 'Jl. Gatot Subroto Kav. 18, Jakarta Selatan'],
            ['SUP-002', 'PT Krakatau Steel Perkasa', 'Haryanto Tanuwijaya', '0254-391201', 'penjualan@krakatausteel.com', 'Kawasan Industri Cilegon Kav. C4, Cilegon, Banten'],
            ['SUP-003', 'PT Adhi Persada Precast', 'Dian Anggraini', '021-8240991', 'marketing@adhiprecast.co.id', 'Jl. Raya Narogong KM 12, Pangkalan 2, Bekasi'],
            ['SUP-004', 'CV Pasir Berkah Muntilan', 'Haji Syafei', '0293-587211', 'pasir.muntilan@gmail.com', 'Jl. Magelang-Yogya KM 24, Muntilan, Jawa Tengah'],
            ['SUP-005', 'PT Holcim Solusi Beton', 'Rahmat Santoso', '021-2988100', 'readymix@holcim-indonesia.com', 'Talavera Office Park Lt. 15, Jl. TB Simatupang, Jakarta'],
            ['SUP-006', 'PT Wijaya Metalindo Utama', 'Andreas Lukman', '021-4608822', 'sales@wijayametalindo.com', 'Kawasan Industri Pulogadung Blok F-8, Jakarta Timur'],
            ['SUP-007', 'CV Jaya Rangka Baja Ringan', 'Kurniawan Pratama', '022-7561901', 'order@jayarangkaprofil.com', 'Jl. Soekarno Hatta No. 428, Bandung, Jawa Barat'],
            ['SUP-008', 'PT Caturkencana Mulya (Pipa)', 'Surya Dharma', '031-7482001', 'surabaya@caturkencana.co.id', 'Kompleks Pergudangan Margomulyo Indah Blok H-7, Surabaya'],
            ['SUP-009', 'PT Graha Keramik Sentosa', 'Melissa Sutanto', '021-5698300', 'proyek@grahakeramik.co.id', 'Jl. Daan Mogot KM 11 No. 58, Jakarta Barat'],
            ['SUP-010', 'CV Aneka Cat & Kimia Mandiri', 'Faisal Akbar', '021-8832044', 'sales@anekacat.co.id', 'Jl. Raya Bekasi KM 28, Kranji, Bekasi Barat'],
            ['SUP-011', 'PT Bintang Semesta Gypsum', 'Yudi Purnomo', '021-8790112', 'gypsum.bintang@gmail.com', 'Jl. Raya Jakarta-Bogor KM 48, Cibinong, Bogor'],
            ['SUP-012', 'PT Multi Sarana Wiremesh', 'Gunawan Susanto', '021-5551980', 'info@multisaranasteel.com', 'Kawasan Industri Manis Blok D-14, Tangerang'],
            ['SUP-013', 'CV Sumber Kayu Meranti Jaya', 'Heri Cahyono', '021-5401122', 'sumberkayu.meranti@gmail.com', 'Jl. Raya Kapuk Kamal No. 17, Jakarta Utara'],
            ['SUP-014', 'PT Sinar Elektrik Pratama', 'Handoko Wijaya', '021-6268900', 'proyek@sinarelektrik.com', 'Pusat Niaga Glodok Plaza Blok E-12, Jakarta Barat'],
            ['SUP-015', 'PT Pertamina Patra Niaga (Solar HSD)', 'Dewi Kusuma', '021-1350000', 'corporate.solar@pertamina.com', 'Gedung Wisma Tugu Pratama, Jl. Rasuna Said, Jakarta'],
            ['SUP-016', 'PT Jayamix Precast Utama', 'Iman Nugroho', '021-2997890', 'tender@jayamixprecast.co.id', 'Pondok Indah Office Tower 3 Lt. 9, Jakarta Selatan'],
            ['SUP-017', 'CV Citra Pasir Mandiri', 'Ahmad Zaelani', '0265-334100', 'citrapasir.galunggung@yahoo.com', 'Jl. Raya Singaparna KM 8, Tasikmalaya, Jawa Barat'],
            ['SUP-018', 'PT Tiga Roda Logistik Jawa', 'Irfan Hakim', '0231-341200', 'logistik@indocement.co.id', 'Jl. Raya Palimanan KM 20, Cirebon, Jawa Barat'],
            ['SUP-019', 'PT Master Steel Sukses', 'Toni Hartono', '021-4603333', 'marketing@mastersteel.co.id', 'Jl. Pangeran Jayakarta No. 107, Jakarta Pusat'],
            ['SUP-020', 'CV Berkah Split Andesit', 'Budi Handoyo', '0282-540900', 'split.cilacap@gmail.com', 'Jl. Tentara Pelajar No. 88, Cilacap, Jawa Tengah'],
            ['SUP-021', 'PT Knauf Gypsum Nusantara', 'Priscilla Tan', '021-2525111', 'knauf.indonesia@knauf.com', 'Menara Karya Lt. 22, Jl. HR Rasuna Said, Jakarta'],
            ['SUP-022', 'PT Rucika Polychem Utama', 'Wawan Setiawan', '021-5694200', 'proyek@rucika.co.id', 'Alia Building Lt. 7, Jl. Ridwan Rais No. 10, Jakarta'],
            ['SUP-023', 'CV Pelita Lampu Nusantara', 'Doni Kurnia', '021-6385100', 'pelitalampu@cbn.net.id', 'Pertokoan Lindeteves Trade Center (LTC) Glodok, Jakarta'],
            ['SUP-024', 'PT Fajar Surya Wisesa', 'Sony Kurniawan', '021-8900330', 'sales@fajarpaper.com', 'Jl. Kalibaru KM 24, Cikarang Barat, Bekasi'],
            ['SUP-025', 'CV Granit Indah Perkasa', 'Tommy Chandra', '031-5034100', 'granit.surabaya@gmail.com', 'Jl. Raya Manyar Kertoarjo No. 72, Surabaya'],
            ['SUP-026', 'PT Akzo Nobel Cat Dulux', 'Astrid Wardhani', '021-8934200', 'proyek@akzonobel.com', 'Kawasan Industri Jababeka Tahap I Blok C, Cikarang'],
            ['SUP-027', 'CV Dwi Tunggal Multiplek', 'Sutanto Wijaya', '0281-632100', 'dwitunggal.triplek@yahoo.com', 'Jl. Gerilya Barat No. 204, Purwokerto, Jawa Tengah'],
            ['SUP-028', 'PT Tata Metal Lestari Roof', 'Kevin Sanjaya', '021-5633888', 'tatametal@tatametal.com', 'Jl. Arjuna Utara No. 9, Tomang Barat, Jakarta Barat'],
            ['SUP-029', 'CV Makmur Baja Profil', 'Rico Saputra', '024-7608900', 'makmurbaja.smg@gmail.com', 'Kawasan Industri Candi Blok A-15, Semarang'],
            ['SUP-030', 'PT Varia Usaha Logistik Konstruksi', 'Edy Prasetyo', '031-3981800', 'tender@variausaha.co.id', 'Jl. Veteran No. 150, Gresik, Jawa Timur'],
        ];

        foreach ($suppliers as $s) {
            Supplier::updateOrCreate(
                ['supplier_code' => $s[0]],
                [
                    'name' => $s[1],
                    'contact_person' => $s[2],
                    'phone' => $s[3],
                    'email' => $s[4],
                    'address' => $s[5],
                    'notes' => 'Pemasok material resmi rekanan terverifikasi pakta integritas vendor.',
                    'status' => 'active',
                ]
            );
        }
    }

    /**
     * 7. Seed 30 Purchase Orders, Line Items & 30 Stock Transactions
     */
    protected function seedPurchaseOrdersAndStock(): void
    {
        $logisticsUser = User::where('role', 'logistics')->first() ?? User::first();
        $suppliers = Supplier::orderBy('id')->get();
        $projects = Project::orderBy('id')->get();
        $materials = Material::orderBy('id')->get();

        for ($i = 1; $i <= 30; $i++) {
            $poNum = sprintf('PO-2026-%04d', $i);
            $supplier = $suppliers[($i - 1) % $suppliers->count()];
            $project = $projects[($i - 1) % $projects->count()];
            $material = $materials[($i - 1) % $materials->count()];

            $qty = 50 + ($i * 10);
            $unitPrice = $material->price;
            $lineTotal = $qty * $unitPrice;
            $orderDate = Carbon::parse('2026-01-05')->addDays($i * 3)->toDateString();
            $status = $i <= 20 ? 'received' : ($i <= 26 ? 'ordered' : 'partial');

            $po = PurchaseOrder::updateOrCreate(
                ['po_number' => $poNum],
                [
                    'supplier_id' => $supplier->id,
                    'project_id' => $project->id,
                    'user_id' => $logisticsUser->id,
                    'order_date' => $orderDate,
                    'expected_date' => Carbon::parse($orderDate)->addDays(7)->toDateString(),
                    'status' => $status,
                    'total_amount' => $lineTotal,
                    'paid_amount' => $status === 'received' ? $lineTotal : ($status === 'partial' ? ($lineTotal / 2) : 0),
                    'notes' => "Pengadaan rutin material {$material->name} untuk kebutuhan site proyek {$project->project_code}.",
                    'received_at' => $status === 'received' ? Carbon::parse($orderDate)->addDays(5) : null,
                ]
            );

            // Purchase Order Item
            PurchaseOrderItem::updateOrCreate(
                [
                    'purchase_order_id' => $po->id,
                    'material_id' => $material->id,
                ],
                [
                    'quantity' => $qty,
                    'received_quantity' => $status === 'received' ? $qty : ($status === 'partial' ? (int) ($qty / 2) : 0),
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]
            );

            // Stock Transaction (Audit History)
            $stockBefore = $material->stock;
            $stockAfter = $status === 'received' ? ($stockBefore + $qty) : $stockBefore;

            StockTransaction::updateOrCreate(
                [
                    'reference' => 'STX-' . $poNum,
                ],
                [
                    'material_id' => $material->id,
                    'user_id' => $logisticsUser->id,
                    'project_id' => $project->id,
                    'purchase_order_id' => $po->id,
                    'type' => $status === 'received' ? 'in' : 'out',
                    'quantity' => $status === 'received' ? $qty : 10,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'source' => $status === 'received' ? 'purchase_order' : 'site_requisition',
                    'notes' => "Pencatatan mutasi otomatis transaksi logistik referensi {$poNum}.",
                    'occurred_at' => Carbon::parse($orderDate)->addDays(5)->toDateTimeString(),
                ]
            );
        }
    }

    /**
     * 8. Seed 30 Worker Assignments & 30 Worker Attendances
     */
    protected function seedWorkerAssignmentsAndAttendance(): void
    {
        $workers = Worker::orderBy('id')->get();
        $projects = Project::orderBy('id')->get();
        $fieldUser = User::where('role', 'field_staff')->first() ?? User::first();

        for ($i = 1; $i <= 30; $i++) {
            $worker = $workers[($i - 1) % $workers->count()];
            $project = $projects[($i - 1) % $projects->count()];
            $startDate = Carbon::parse('2026-01-10')->addDays($i * 2)->toDateString();

            $assignment = WorkerAssignment::updateOrCreate(
                [
                    'worker_id' => $worker->id,
                    'project_id' => $project->id,
                    'task_name' => "Pekerjaan Spesialisasi {$worker->position} - Zona {$i}",
                ],
                [
                    'start_date' => $startDate,
                    'end_date' => Carbon::parse($startDate)->addMonths(3)->toDateString(),
                    'status' => 'active',
                    'notes' => "Penugasan berkala mandor dan teknisi ke lokasi proyek {$project->name}.",
                ]
            );

            // 1 Daily Attendance per Assignment
            $workDate = Carbon::parse($startDate)->addDays(1)->toDateString();
            $dailyRate = $worker->daily_rate;
            $status = $i % 2 === 0 ? 'paid' : 'pending';

            WorkerAttendance::updateOrCreate(
                [
                    'worker_assignment_id' => $assignment->id,
                    'work_date' => $workDate,
                ],
                [
                    'worker_id' => $worker->id,
                    'project_id' => $project->id,
                    'user_id' => $fieldUser->id,
                    'hours' => 8.0,
                    'days_worked' => 1.0,
                    'daily_rate' => $dailyRate,
                    'amount' => $dailyRate * 1.0,
                    'status' => $status,
                    'paid_at' => $status === 'paid' ? Carbon::parse($workDate)->addDays(2)->toDateTimeString() : null,
                    'notes' => 'Presensi harian diverifikasi oleh mandor dan pengawas K3 lapangan.',
                ]
            );
        }
    }

    /**
     * 9. Seed 30 Project Budgets & Expenses
     */
    protected function seedBudgets(): void
    {
        $projects = Project::orderBy('id')->get();
        $categories = ['material', 'labor', 'equipment', 'subcontractor', 'operational', 'overhead'];

        $budgetDescriptions = [
            'Pembelian ready mix beton cor K-350 batching plant',
            'Upah borongan pembesian dan perakitan kolom lt. 2',
            'Sewa mobile crane 50 ton dan operator 3 hari',
            'Subkontraktor pekerjaan mekanikal elektrikal gedung',
            'Biaya operasional bahan bakar genset lapangan 500 kVA',
            'Pengadaan besi ulir D16 SNI dari distributor utama',
            'Honorarium pengawasan surveyor dan uji sondir tanah',
            'Sewa scaffolding galvanis 400 set durasi 1 bulan',
            'Pekerjaan waterproofing membran bakar dak atap',
            'Biaya konsumsi lembur pekerja pengecoran malam hari',
            'Pembelian semen Portland komposit 500 sak',
            'Upah tukang plester acian dan dinding bata ringan',
            'Mobilisasi dan demobilisasi excavator Hitachi PC200',
            'Subkon pemasangan kusen aluminium dan kaca tempered',
            'Biaya pengujian laboratorium kuat tekan beton 28 hari',
            'Pengadaan keramik granit tile 60x60 polished',
            'Upah mandor dan tukang instalasi pipa air bersih',
            'Sewa dump truck 24 m3 pengangkutan disposal tanah',
            'Pekerjaan kanopi baja profil peron sisi barat',
            'Biaya listrik kerja PLN dan air bersih proyek',
            'Pengadaan pipa PVC AW 4 inch pembuangan air hujan',
            'Upah borongan tukang plafon gypsum board 9mm',
            'Sewa mesin bar bender dan bar cutter pemotong besi',
            'Subkon pemasangan lift penumpang dan eskalator gedung',
            'Biaya audit keselamatan kerja K3 dan APD rompi helm',
            'Pembelian cat eksterior weathershield 30 pail',
            'Upah instalasi titik lampu LED dan panel distribusi',
            'Sewa pompa kodok concrete pump pengecoran lantai 5',
            'Pekerjaan pintu darurat kebakaran emergency exit',
            'Biaya asuransi CAR (Contractor All Risk) tahap lanjutan',
        ];

        for ($i = 1; $i <= 30; $i++) {
            $project = $projects[($i - 1) % $projects->count()];
            $cat = $categories[($i - 1) % count($categories)];
            $voucher = sprintf('EXP-%05d', $i);
            $amount = 15000000 + ($i * 3500000);
            $date = Carbon::parse('2026-01-10')->addDays($i * 3)->toDateString();

            Budget::updateOrCreate(
                ['voucher_code' => $voucher],
                [
                    'project_id' => $project->id,
                    'category' => $cat,
                    'description' => $budgetDescriptions[$i - 1],
                    'amount' => $amount,
                    'date' => $date,
                    'status' => 'approved',
                    'receipt' => null,
                ]
            );
        }
    }

    /**
     * 10. Seed 30 Cash Flow Transactions
     */
    protected function seedCashFlows(): void
    {
        $projects = Project::orderBy('id')->get();
        $suppliers = Supplier::orderBy('id')->get();
        $financeUser = User::where('role', 'finance')->first() ?? User::first();

        $cashFlowData = [
            ['in', 'client_payment', 'Penerimaan Termijn I Proyek RSUD Tarakan', 4875000000, '2026-01-15'],
            ['out', 'vendor_payment', 'Pembayaran Pelunasan Semen Indonesia PO-0001', 342500000, '2026-01-18'],
            ['out', 'payroll', 'Distribusi Upah Mingguan Pekerja Lapangan Periode 1', 65400000, '2026-01-20'],
            ['in', 'client_payment', 'Pencairan Uang Muka DP Proyek Flyover Antapani', 4960000000, '2026-01-22'],
            ['out', 'equipment_rental', 'Pembayaran Sewa Crane & Excavator Tahap 1', 115000000, '2026-01-25'],
            ['out', 'vendor_payment', 'Pembayaran Pengadaan Baja Krakatau Steel PO-0002', 875000000, '2026-01-28'],
            ['in', 'client_payment', 'Penerimaan Uang Muka DP Jembatan Cisadane', 2840000000, '2026-02-02'],
            ['out', 'operational', 'Biaya Operasional Lapangan & Perizinan Dishub', 28500000, '2026-02-05'],
            ['out', 'subcontractor', 'Uang Muka Subkontraktor Tata Udara HVAC', 450000000, '2026-02-08'],
            ['in', 'client_payment', 'Penerimaan Termijn II Revitalisasi Stasiun Ps Turi', 3700000000, '2026-02-12'],
            ['out', 'payroll', 'Distribusi Upah Tukang & Mandor Periode 2', 78900000, '2026-02-15'],
            ['out', 'vendor_payment', 'Pelunasan Pengadaan Pasir Muntilan & Split', 198000000, '2026-02-18'],
            ['in', 'client_payment', 'Penerimaan Termijn I Gudang Cikarang Dry Port', 4200000000, '2026-02-22'],
            ['out', 'operational', 'Biaya Pembelian Solar Industri HSD Genset', 55500000, '2026-02-25'],
            ['out', 'vendor_payment', 'Pembayaran Ready Mix Batching Plant Holcim', 625000000, '2026-02-28'],
            ['in', 'client_payment', 'Penerimaan Termijn I Lab Undip Semarang', 2512500000, '2026-03-02'],
            ['out', 'subcontractor', 'Termijn Subkontraktor Pekerjaan Bored Pile', 720000000, '2026-03-05'],
            ['out', 'payroll', 'Distribusi Gaji dan Upah Periode Maret 2026', 82500000, '2026-03-08'],
            ['in', 'client_payment', 'Pencairan MC Progres Drainase Tol Tomang', 1900000000, '2026-03-12'],
            ['out', 'vendor_payment', 'Pembayaran Pengadaan Pipa Box Culvert Precast', 415000000, '2026-03-15'],
            ['in', 'client_payment', 'Penerimaan Termijn I Kantor Bank Jatim Malang', 1845000000, '2026-03-18'],
            ['out', 'operational', 'Penggantian Petty Cash Lapangan & Keet Proyek', 18500000, '2026-03-20'],
            ['out', 'vendor_payment', 'Pelunasan Pengadaan Keramik Granit 60x60', 133200000, '2026-03-22'],
            ['in', 'client_payment', 'Penerimaan Uang Muka DP Pusat Distribusi Farmasi', 3150000000, '2026-03-25'],
            ['out', 'equipment_rental', 'Sewa Scaffolding dan Alat Uji Lab Tekan Beton', 48000000, '2026-03-28'],
            ['out', 'vendor_payment', 'Pembayaran Bata Ringan AAC & Semen Mortar', 248000000, '2026-04-02'],
            ['in', 'client_payment', 'Pencairan Termijn I Tanggul Kali Lamong Gresik', 5250000000, '2026-04-05'],
            ['out', 'payroll', 'Distribusi Upah Lembur Pengecoran Plat Lantai', 69300000, '2026-04-08'],
            ['out', 'vendor_payment', 'Pelunasan Pengadaan Baja Ringan & Reng Truss', 185000000, '2026-04-12'],
            ['in', 'client_payment', 'Penerimaan Termijn I Struktur Menara Sudirman', 6750000000, '2026-04-15'],
        ];

        foreach ($cashFlowData as $idx => $cf) {
            $ref = sprintf('CF-2026-%03d', $idx + 1);
            $prj = $projects[$idx % $projects->count()];
            $sup = $suppliers[$idx % $suppliers->count()];

            CashFlow::updateOrCreate(
                ['reference' => $ref],
                [
                    'project_id' => $prj->id,
                    'user_id' => $financeUser->id,
                    'supplier_id' => $cf[0] === 'out' ? $sup->id : null,
                    'type' => $cf[0],
                    'category' => $cf[1],
                    'description' => $cf[2],
                    'amount' => $cf[3],
                    'transaction_date' => $cf[4],
                ]
            );
        }
    }
}
