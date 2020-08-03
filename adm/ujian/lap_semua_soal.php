<?php
session_start();

require("../../inc/config.php");
require("../../inc/fungsi.php");
require("../../inc/koneksi.php");
require("../../inc/cek/adm.php");
require("../../inc/class/paging.php");
$tpl = LoadTpl("../../template/admin.html");

nocache;

//nilai
$filenya = "lap_semua_soal.php";
$judul = "[LAPORAN] Siswa Mengerjakan Semua Soal";
$judulku = "$judul";
$judulx = $judul;
$jkd = nosql($_REQUEST['jkd']);
$kd = nosql($_REQUEST['kd']);
$s = nosql($_REQUEST['s']);
$kunci = cegah($_REQUEST['kunci']);
$kunci2 = balikin($_REQUEST['kunci']);
$page = nosql($_REQUEST['page']);
if ((empty($page)) OR ($page == "0"))
	{
	$page = "1";
	}


$limit = 1000;





require '../../inc/class/phpofficeexcel/vendor/autoload.php';



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;






//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//jika ke daftar
if ($_POST['btnDF'])
	{
	//re-direct
	$ke = "lap.php";
	xloc($ke);
	exit();
	}






//nek batal
if ($_POST['btnBTL'])
	{
	//nilai
	$jkd = nosql($_POST['jkd']);

	//re-direct
	$ke = "$filenya?jkd=$jkd";
	xloc($ke);
	exit();
	}





//jika cari
if ($_POST['btnCARI'])
	{
	//nilai
	$jkd = nosql($_POST['jkd']);	
	$kunci = cegah($_POST['kunci']);


	//re-direct
	$ke = "$filenya?jkd=$jkd&kunci=$kunci";
	xloc($ke);
	exit();
	}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





//jika excel
if ($s == "excel")
	{
	//detail jkd jadwal
	$qku = mysqli_query($koneksi, "SELECT * FROM m_jadwal ".
							"WHERE kd = '$jkd'");
	$rku = mysqli_fetch_assoc($qku);
	$u_waktu = balikin($rku['waktu']);
	$u_pukul = balikin($rku['pukul']);
	$u_durasi = balikin($rku['durasi']);
	$u_mapel = balikin($rku['mapel']);
	$u_tingkat = balikin($rku['tingkat']);
	$u_soal_jml = balikin($rku['soal_jml']);
	



	

	//nama file e...
	$i_pecahku = seo_friendly_url("jawabsemua-$u_waktu-$u_mapel-$u_tingkat");
	$i_filename = "$i_pecahku.xls";
	$i_judul = "JawabSemua";
	



	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$sheet->setCellValue('A1', 'NO');
	$sheet->setCellValue('B1', 'KELAS');
	$sheet->setCellValue('C1', 'NIS');
	$sheet->setCellValue('D1', 'NAMA');
	$sheet->setCellValue('E1', 'BENAR');
	$sheet->setCellValue('F1', 'SALAH');
	$sheet->setCellValue('G1', 'NILAI');
	$sheet->setCellValue('H1', 'POSTDATE');

	$i = 2;		
	$no = 1;




	//deteksi kelas
	$ikelas = explode(" ", $u_tingkat);
	$kelasa = trim($ikelas[0]);
	$kelasb = trim($ikelas[1]);
	$tingkat1 = $kelasa;
	$tingkat2 = "$kelasa $kelasb";
	


	//query	
	$qyukx = mysqli_query($koneksi, "SELECT * FROM siswa ".
							"WHERE kelas LIKE '$tingkat2%' ".
							"ORDER BY kelas ASC, ".
							"round(nis) ASC");
	$ryukx = mysqli_fetch_assoc($qyukx);

	do 
		{
		$i_kd = nosql($ryukx['kd']);
		$i_nis = balikin($ryukx['nis']);
		$i_nama = balikin($ryukx['nama']);
		$i_kelas = balikin($ryukx['kelas']);
		
				 
		
		
		//ambil dari table siswa masing - masing
		$tablenilai = "siswa_soal_nilai";
		$qmboh = mysqli_query($koneksi, "SELECT * FROM $tablenilai ".
								"WHERE siswa_kd = '$i_kd' ".
								"AND jadwal_kd = '$jkd'");
		$rmboh = mysqli_fetch_assoc($qmboh);
		$mboh_kd = nosql($rmboh['kd']);
		$mboh_benar = balikin($rmboh['jml_benar']);
		$mboh_salah = balikin($rmboh['jml_salah']);
		$mboh_dikerjakan = balikin($rmboh['jml_soal_dikerjakaan']);
		$mboh_postdate = balikin($rmboh['postdate']);
			
		
		
		//update...
		if (empty($mboh_dikerjakan))
			{
			$mboh_dikerjakan = $mboh_benar + $mboh_salah;
			
			//update
			mysqli_query($koneksi, "UPDATE $tablenilai SET jml_soal_dikerjakan ='$mboh_dikerjakan' ".
							"WHERE kd = '$mboh_kd'");
			}
			
			
			
		//nilainya..
		$nilaiku = ($mboh_benar / $mboh_dikerjakan) * 100;
		
		
		
		//update
		mysqli_query($koneksi, "UPDATE siswa_soal_nilai SET skor = '$nilaiku' ".
						"WHERE jadwal_kd = '$jkd' ".
						"AND siswa_kd = '$i_kd'");
		


			
		//jika semua
		if ($mboh_dikerjakan == $u_soal_jml)
			{
			$dt_nox = $dt_nox + 1;
			  
			
			//ciptakan
			$sheet->setCellValue('A'.$i, $no++);
			$sheet->setCellValue('B'.$i, $i_kelas);
			$sheet->setCellValue('C'.$i, $i_nis);
			$sheet->setCellValue('D'.$i, $i_nama);
			$sheet->setCellValue('E'.$i, $mboh_benar);
			$sheet->setCellValue('F'.$i, $mboh_salah);
			$sheet->setCellValue('G'.$i, $nilaiku);
			$sheet->setCellValue('H'.$i, $mboh_postdate);
			$i++;				
			}
			
		}
	while ($ryukx = mysqli_fetch_assoc($qyukx));
		
	


	//tulis
	$targetfileku = "../../filebox/excel/$i_filename";
	$writer = new Xlsx($spreadsheet);
	$writer->save($targetfileku);
		
	


		
	//download
	header('Content-Type: Application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename="'.$i_filename.'"');
	$writer->save('php://output');
		

	//hapus file, jika telah import
	$path1 = "../../filebox/excel/$i_filename";
	chmod($path1,0777);
	unlink ($path1);


	
	//re-direct
	//exit
	xclose($koneksi);
	xloc($filenya);
	exit();
	}



else
	{		
	//isi *START
	ob_start();
	
	
	//require
	require("../../template/js/jumpmenu.js");
	require("../../template/js/checkall.js");
	require("../../template/js/swap.js");
	?>
	
	  
	  <script>
	$(document).ready(function() {
	$('#table-responsive').dataTable( {
	"scrollX": true
	    } );
	} );
	  </script>
	  
	<?php
	//view //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//detail jkd jadwal
	$qku = mysqli_query($koneksi, "SELECT * FROM m_jadwal ".
					"WHERE kd = '$jkd'");
	$rku = mysqli_fetch_assoc($qku);
	$u_waktu = balikin($rku['waktu']);
	$u_pukul = balikin($rku['pukul']);
	$u_durasi = balikin($rku['durasi']);
	$u_mapel = balikin($rku['mapel']);
	$u_tingkat = balikin($rku['tingkat']);
	$u_soal_jml = balikin($rku['soal_jml']);
	$u_postdate_mulai = balikin($rku['postdate_mulai']);
	$u_postdate_selesai = balikin($rku['postdate_selesai']);
	
	
	
	
	
	//ketahui jumlah siswa yg mengerjakan
	$qjos = mysqli_query($koneksi, "SELECT DISTINCT(siswa_kd) AS skd ".
					"FROM siswa_soal_nilai ".
					"WHERE jadwal_kd = '$jkd'");
	$tjos = mysqli_num_rows($qjos);
	
	echo '<form action="'.$filenya.'" method="post" name="formxx">
	
	<p>
	[<b>'.$u_waktu.'</b>]. [<b>'.$u_pukul.'</b>]. [<b>'.$u_durasi.' Menit</b>].
	</p>
	
	<p>
	Mapel : <b>'.$u_mapel.'</b>, Kelas : <b>'.$u_tingkat.'</b>
	</p>
	
	
	<p>
	Mulai : <b>'.$u_postdate_mulai.'</b>, Selesai : <b>'.$u_postdate_selesai.'</b>
	</p>
	
	
	
	<p>
	<input name="jkd" type="hidden" value="'.$jkd.'">
	<input name="btnDF" type="submit" value="LIHAT JADWAL LAIN >" class="btn btn-danger">
	</p>
	<br>
	
	</form>
	
	
	
	
	
	
	
	
	<form action="'.$filenya.'" method="post" name="formxx">';
	
	
	//deteksi kelas
	$ikelas = explode(" ", $u_tingkat);
	$kelasa = trim($ikelas[0]);
	$kelasb = trim($ikelas[1]);
	$tingkat1 = $kelasa;
	$tingkat2 = "$kelasa $kelasb";
	
	
	
	//jika null
	if (empty($kunci))
		{
		$sqlcount = "SELECT * FROM siswa ".
						"WHERE kelas LIKE '$tingkat2%' ".
						"ORDER BY kelas ASC, ".
						"round(nis) ASC";
		}
		
	else
		{
		$sqlcount = "SELECT * FROM siswa ".
						"WHERE kelas LIKE '$tingkat2%' ".
						"AND (nis LIKE '%$kunci%' ".
						"OR nama LIKE '%$kunci%') ".
						"ORDER BY kelas ASC, ".
						"round(nis) ASC";
	}
		
	
	//query
	$p = new Pager();
	$start = $p->findStart($limit);
	
	$sqlresult = $sqlcount;
	
	$count = mysqli_num_rows(mysqli_query($koneksi, $sqlcount));
	$pages = $p->findPages($count, $limit);
	$result = mysqli_query($koneksi, "$sqlresult LIMIT ".$start.", ".$limit);
	$target = "$filenya?jkd=$jkd";
	$pagelist = $p->pageList($_GET['page'], $pages, $target);
	$data = mysqli_fetch_array($result);
		
	
	echo '<hr>
	<p>
	<input name="kunci" type="text" value="'.$kunci2.'" size="20" class="btn btn-warning">
	<input name="btnCARI" type="submit" value="CARI" class="btn btn-danger">
	<input name="btnBTL" type="submit" value="RESET" class="btn btn-info">
	<input name="s" type="hidden" value="'.$s.'">
	<input name="jkd" type="hidden" value="'.$jkd.'">
	
	</p>
		
		
	
	
	 
	<a href="'.$filenya.'?s=excel&jkd='.$jkd.'" class="btn btn-success">EXPORT EXCEL >></a>
		
	<div class="table-responsive">          
	<table class="table" border="1">
	<thead>
	
	<tr valign="top" bgcolor="'.$warnaheader.'">
	<td width="50"><strong><font color="'.$warnatext.'">NO</font></strong></td>
	<td width="150"><strong><font color="'.$warnatext.'">KELAS</font></strong></td>
	<td width="50"><strong><font color="'.$warnatext.'">NIS</font></strong></td>
	<td><strong><font color="'.$warnatext.'">NAMA</font></strong></td>
	<td width="50"><strong><font color="'.$warnatext.'">BENAR</font></strong></td>
	<td width="50"><strong><font color="'.$warnatext.'">SALAH</font></strong></td>
	<td width="50"><strong><font color="'.$warnatext.'">NILAI</font></strong></td>
	<td width="50"><strong><font color="'.$warnatext.'">POSTDATE</font></strong></td>
	</tr>
	</thead>
	<tbody>';
	
	if ($count != 0)
		{
		do 
			{
			if ($warna_set ==0)
				{
				$warna = $warna01;
				$warna_set = 1;
				}
			else
				{
				$warna = $warna02;
				$warna_set = 0;
				}
	
			$i_kd = nosql($data['kd']);
			$i_nis = balikin($data['nis']);
			$i_nama = balikin($data['nama']);
			$i_kelas = balikin($data['kelas']);
			
				 
			
			
			//baca table sumbernya......................................................................
			$tableujian = "siswa_soal";
			$qyuk = mysqli_query($koneksi, "SELECT * FROM $tableujian ".
									"WHERE siswa_kd = '$i_kd' ".
									"AND jadwal_kd = '$jkd' ".
									"ORDER BY postdate DESC");
			$ryuk = mysqli_fetch_assoc($qyuk);
			$tyuk = mysqli_num_rows($qyuk);
			
			
			//jika ada
			if (!empty($tyuk))
				{
				do
					{
					//nilai
					$yuk_kd = nosql($ryuk['kd']);
					$yuk_soalkd = nosql($ryuk['soal_kd']);
					$yuk_jawab = nosql($ryuk['jawab']);
					$yuk_kunci = nosql($ryuk['kunci']);
					$yuk_benar = balikin($ryuk['benar']);
					$yuk_postdate1 = balikin($ryuk['postdate']);
					
					
					$yuk_postdatex = explode("-", $yuk_postdate1);
					$p_tahun = trim($yuk_postdatex[0]);
					$p_bulan = trim($yuk_postdatex[1]);
					$p_tanggalx = trim($yuk_postdatex[2]);
					
					$yuk_postdatex2 = explode(" ", $p_tanggalx);
					$p_tanggal = trim($yuk_postdatex2[0]);
					
					
					
					$yuk_postdatex = explode(" ", $yuk_postdate1);
					$p_waktu = trim($yuk_postdatex[1]);
					
					
					$p_postdate_baru = "$p_tahun:$p_bulan:$p_tanggal $p_waktu";	
						
					//insert
					mysqli_query($koneksi, "INSERT INTO siswa_soal(kd, jadwal_kd, siswa_kd, soal_kd, ".
									"jawab, postdate, kunci, benar) VALUES ".
									"('$yuk_kd', '$jkd', '$i_kd', '$yuk_soalkd', ".
									"'$yuk_kunci', '$p_postdate_baru', '$yuk_kunci', '$yuk_benar')");
					}
				while ($ryuk = mysqli_fetch_assoc($qyuk));
				}
			//baca table sumbernya......................................................................
			
			
			
			
			
			
			
			
			//ambil dari table siswa masing - masing
			$tablenilai = "siswa_soal_nilai";
			$qmboh = mysqli_query($koneksi, "SELECT * FROM $tablenilai ".
									"WHERE siswa_kd = '$i_kd' ".
									"AND jadwal_kd = '$jkd'");
			$rmboh = mysqli_fetch_assoc($qmboh);
			$mboh_kd = nosql($rmboh['kd']);
			$mboh_benar = nosql($rmboh['jml_benar']);
			$mboh_salah = nosql($rmboh['jml_salah']);
			$mboh_dikerjakan = nosql($rmboh['jml_soal_dikerjakaan']);
			$mboh_postdate = balikin($rmboh['postdate']);
			
			
			
			//update...
			if (empty($mboh_dikerjakan))
				{
				$mboh_dikerjakan = $mboh_benar + $mboh_salah;
				
				//update
				mysqli_query($koneksi, "UPDATE $tablenilai SET jml_soal_dikerjakan ='$mboh_dikerjakan' ".
								"WHERE siswa_kd = '$i_kd' ".
								"AND kd = '$mboh_kd'");
				}
				
				
				
			//nilainya..
			$nilaiku = ($mboh_benar / $mboh_dikerjakan) * 100;
			
			
			
			//update
			mysqli_query("UPDATE siswa_soal_nilai SET skor = '$nilaiku' ".
							"WHERE jadwal_kd = '$jkd' ".
							"AND siswa_kd = '$i_kd'");
			
			
			
			//jika semua
			if ($mboh_dikerjakan == $u_soal_jml)
				{
				$nomer = $nomer + 1;
					  
				echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
				echo '<td>'.$nomer.'</td>
				<td>'.$i_kelas.'</td>
				<td>'.$i_nis.'</td>
				<td>'.$i_nama.'</td>
				<td>'.$mboh_benar.'</td>
				<td>'.$mboh_salah.'</td>
				<td>'.$nilaiku.'</td>
				<td>'.$mboh_postdate.'</td>
				</tr>';
				}
			}
		while ($data = mysqli_fetch_assoc($result));
		}
	
	
	echo '</tbody>
	  </table>
	  </div>
	
	
	<table width="500" border="0" cellspacing="0" cellpadding="3">
	<tr>
	<td>
	
	<input name="jml" type="hidden" value="'.$count.'">
	<input name="s" type="hidden" value="'.$s.'">
	<input name="kd" type="hidden" value="'.$kdx.'">
	<input name="page" type="hidden" value="'.$page.'">
	</td>
	</tr>
	</table>
	</form>';
	
	
	
	
	
	//isi
	$isi = ob_get_contents();
	ob_end_clean();
	
	require("../../inc/niltpl.php");
	}





//null-kan
xclose($koneksi);
exit();
?>