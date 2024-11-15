<?php
// Memanggil fpdf.php
require('../libraries/fpdf.php'); // Sesuaikan dengan path jika diperlukan

// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "pkl");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Mengambil NISN dari session
session_start(); // Pastikan session telah dimulai
$nisn = $_SESSION['nisn']; // Mengambil NISN dari session

// Menginisialisasi PDF
$pdf = new FPDF(); // Menggunakan FPDF sebagai objek
$pdf->SetMargins(4, 4, 3);
$pdf->AliasNbPages();
$pdf->AddPage();

// Mengatur font untuk judul
$pdf->SetFont('Times', 'B', 16);

// Judul di tengah
$pdf->Cell(0, 10, 'LAPORAN AKTIVITAS MAGANG / PKL', 0, 0, 'C');

$pdf->Ln();
// Mengatur font untuk label
$pdf->SetFont('times', '', 12);

// Menyiapkan query untuk mengambil data siswa
$query = $koneksi->prepare("SELECT * FROM data_siswa WHERE nisn = ?");
$query->bind_param("s", $nisn);
$query->execute();
$result = $query->get_result();

// Cek apakah ada hasil
if ($row = $result->fetch_assoc()) {
    $nama = $row['nama_siswa']; // Ganti dengan nama kolom yang sesuai
    $kelas = $row['kelas'];
    $jurusan = $row['jurusan']; // Ganti dengan nama kolom yang sesuai
    $nama_perusahaan = $row['nama_industri']; // Ganti dengan nama kolom yang sesuai
    $nama_pembimbing = $row['nama_pembimbing']; // Ganti dengan nama kolom yang sesuai
    $gambar = '../src/img/smk5.png'; // Ganti dengan path gambar yang sesuai
} else {
    $nama = "Tidak ada data";
    $kelas = "Tidak ada data";
    $nama_perusahaan = "Tidak ada data";
    $nama_pembimbing = "Tidak ada data";
    $gambar = ''; // Atur default jika tidak ada data
}

// Mengatur lebar kolom
$labelWidth = 40; // Lebar untuk label
$valueWidth = 100; // Lebar untuk nilai

// Menampilkan NAMA
$pdf->Cell($labelWidth, 7, 'Nama', 0, 0, 'L');
$pdf->Cell($valueWidth, 7, ": $nama", 0, 1, 'L');

// Menampilkan KELAS/JURUSAN
$pdf->Cell($labelWidth, 7, 'Kelas/Jurusan', 0, 0, 'L');
$pdf->Cell($valueWidth, 7, ": $kelas / $jurusan", 0, 1, 'L');

// Menampilkan ASAL SEKOLAH
$pdf->Cell($labelWidth, 7, 'Asal Sekolah', 0, 0, 'L');
$pdf->Cell($valueWidth, 7, ': SMK Negeri 5 Kota Bekasi', 0, 1, 'L');

// Menampilkan NAMA PERUSAHAAN
$pdf->Cell($labelWidth, 7, 'Nama Perusahaan', 0, 0, 'L');
$pdf->Cell($valueWidth, 7, ": $nama_perusahaan", 0, 1, 'L');

// Menampilkan NAMA PEMBIMBING
$pdf->Cell($labelWidth, 7, 'Nama Pembimbing', 0, 0, 'L');
$pdf->Cell($valueWidth, 7, ": $nama_pembimbing", 0, 1, 'L');
$pdf->Ln(5);

// Menampilkan gambar di paling kanan dan sejajar dengan $labelWidth
$imgWidth = 35; // Lebar gambar
$imgHeight = 35; // Tinggi gambar

// Menghitung posisi X untuk gambar
$pageWidth = $pdf->getPageWidth(); // Total lebar halaman
$rightX = $pageWidth - $imgWidth - 10; // 10 mm dari kanan untuk memberi jarak

$currentY = $pdf->GetY();
$pdf->SetXY($rightX, $currentY - 10); // Naikkan Y sebanyak 10 mm
$pdf->Image($gambar, $pdf->GetX(), $currentY - 40, $imgWidth, $imgHeight, '', '', '', false, 300, '', false, false, 0, false, false, false);
$pdf->Ln(); // Move to next line after image

// Menambahkan tabel header
$pdf->SetFont('times', 'B', 12);
$pdf->Cell(10, 10, 'No.', 1, 0, 'C');
$pdf->Cell(30, 10, 'Tanggal', 1, 0, 'C');
$pdf->Cell(30, 10, 'Clock In', 1, 0, 'C');
$pdf->Cell(30, 10, 'Clock Out', 1, 0, 'C');
$pdf->Cell(80, 10, 'Kegiatan', 1, 0, 'C');
$pdf->Cell(20, 10, 'Paraf', 1, 0, 'C');
$pdf->Ln();

// Query untuk mengambil data jurnal
$sql = "SELECT * FROM jurnal WHERE nisn = ?";
$query = $koneksi->prepare($sql);
$query->bind_param("s", $nisn);
$query->execute();
$result = $query->get_result();

$pdf->SetFont('times', '', 12);
if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        // Check if the expected keys exist before accessing them
        $tanggal = isset($row['tanggal']) ? $row['tanggal'] : 'N/A';
        $clock_in = isset($row['clock_in']) ? $row['clock_in'] : 'N/A';
        $clock_out = isset($row['clock_out']) ? $row['clock_out'] : 'N/A';
        $kegiatan = isset($row['kegiatan']) ? $row['kegiatan'] : 'N/A';

        $pdf->Cell(10, 10, $no++, 1, 0, 'C');
        $pdf->Cell(30, 10, $tanggal, 1, 0, 'C');
        $pdf->Cell(30, 10, $clock_in, 1, 0, 'C');
        $pdf->Cell(30, 10, $clock_out, 1, 0, 'C');
        $pdf->Cell(80, 10, $kegiatan, 1, 0, 'C');
        $pdf->Cell(20, 10, '', 1, 0, 'C'); // Kolom Paraf, bisa diisi nanti
        $pdf->Ln();
    }
} else {
    // Jika tidak ada data
    $pdf->Cell(0, 10, 'Tidak ada data ditemukan.', 1, 1, 'C');
}

// Menutup koneksi
$koneksi->close();
// Output the PDF to the browser
$pdf->Output();
