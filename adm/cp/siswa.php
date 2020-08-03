<?php
session_start();

require("../../inc/config.php");
require("../../inc/fungsi.php");
require("../../inc/koneksi.php");
require("../../inc/cek/adm.php");
require("../../inc/class/paging.php");
$tpl = LoadTpl("../../template/admin.html");

nocache;





//ketahui tapel terakhir
$qmboh = mysqli_query($koneksi, "SELECT * FROM m_tapel ".
						"WHERE status = 'true' ".
						"ORDER BY tahun1 DESC");
$rmboh = mysqli_fetch_assoc($qmboh);
$tapelkd = nosql($rmboh['kd']);
$tahun1 = nosql($rmboh['tahun1']);
$tahun2 = nosql($rmboh['tahun2']);



$limit = 100;



//nilai
$filenya = "siswa.php";
$kd = nosql($_REQUEST['kd']);
$s = nosql($_REQUEST['s']);
$kunci = cegah($_REQUEST['kunci']);
$kunci2 = balikin($_REQUEST['kunci']);

$judul = "[MASTER] Data Siswa";
$judulku = "$judul";
$judulx = $judul;
$page = nosql($_REQUEST['page']);
if ((empty($page)) OR ($page == "0"))
	{
	$page = "1";
	}






require '../../inc/class/phpofficeexcel/vendor/autoload.php';



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;





//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//nek batal
if ($_POST['btnBTL'])
	{
	//exit
	mysqli_free_result();
	xclose($koneksi);
	
	//re-direct
	xloc($filenya);
	exit();
	}



//jika import
if ($_POST['btnIM'])
	{
	//exit
	mysqli_free_result();
	xclose($koneksi);
	
	//re-direct
	$ke = "$filenya?s=import";
	xloc($ke);
	exit();
	}






//import sekarang
if ($_POST['btnIMX'])
	{
	$filex_namex2 = strip(strtolower($_FILES['filex_xls']['name']));

	//nek null
	if (empty($filex_namex2))
		{
		//re-direct
		$pesan = "Input Tidak Lengkap. Harap Diulangi...!!";
		$ke = "$filenya?s=import";
		pekem($pesan,$ke);
		
		//exit
		mysqli_free_result();
		xclose($koneksi);		
		exit();
		}
	else
		{
		//deteksi .xls
		$ext_filex = substr($filex_namex2, -4);

		if ($ext_filex == ".xls")
			{
			//nilai
			$path1 = "../../filebox";
			$path2 = "../../filebox/excel";
			chmod($path1,0777);
			chmod($path2,0777);

			//nama file import, diubah menjadi baru...
			$filex_namex2 = "siswa.xls";

			//mengkopi file
			copy($_FILES['filex_xls']['tmp_name'],"../../filebox/excel/$filex_namex2");

			//chmod
            $path3 = "../../filebox/excel/$filex_namex2";
			chmod($path1,0755);
			chmod($path2,0777);
			chmod($path3,0777);

			//file-nya...
			$uploadfile = $path3;


			//require
			require('../../inc/class/PHPExcel.php');
			require('../../inc/class/PHPExcel/IOFactory.php');


			  // load excel
			  $load = PHPExcel_IOFactory::load($uploadfile);
			  $sheets = $load->getActiveSheet()->toArray(null,true,true,true);
			
			  $i = 1;
			  foreach ($sheets as $sheet) 
			  	{
			    // karena data yang di excel di mulai dari baris ke 2
			    // maka jika $i lebih dari 1 data akan di masukan ke database
			    if ($i > 1) 
			    	{
				      // nama ada di kolom A
				      // sedangkan alamat ada di kolom B
				      $o_xyz = md5("$x$i");
				      $o_no = cegah($sheet['A']);
				      $o_nis = cegah($sheet['B']);
				      $o_nisn = cegah($sheet['C']);
				      $o_nama = cegah($sheet['D']);
				      $o_kelas = cegah($sheet['E']);
				      $o_lahir_tmp = cegah($sheet['F']);
				      $o_lahir_tgl = cegah($sheet['G']);
				      $o_kelamin = cegah($sheet['H']);
					  
					  
	
	
						//cek
						$qcc = mysqli_query($koneksi, "SELECT * FROM siswa ".
												"WHERE nis = '$i_nis'");
						$rcc = mysqli_fetch_assoc($qcc);
						$tcc = mysqli_num_rows($qcc);
		
						//jika ada, update				
						if (!empty($tcc))
							{
							mysqli_query($koneksi, "UPDATE siswa SET nisn = '$o_nisn', ".
											"nama = '$o_nama', ".
											"kelas = '$o_kelas', ".
											"kelamin = '$o_kelamin', ".
											"lahir_tmp = '$o_lahir_tmp', ".
											"lahir_tgl = '$o_lahir_tgl' ".
											"WHERE nis = '$o_nis'");
							}
		
		
						else
							{
							//insert
							mysqli_query($koneksi, "INSERT INTO siswa(kd, tapel_kd, nis, nisn, nama, kelas, ".
											"kelamin, lahir_tmp, lahir_tgl, postdate) VALUES ".
											"('$o_xyz', '$tapelkd', '$o_nis', '$o_nisn', '$o_nama', '$o_kelas', ".
											"'$o_kelamin', '$o_lahir_tmp', '$o_lahir_tgl', '$today')");
							}
					  
				    }
			
			    $i++;
			  }





			//hapus file, jika telah import
			$path1 = "../../filebox/excel/$filex_namex2";
			chmod($path1,0777);
			unlink ($path1);


			//re-direct
			mysqli_free_result();
			xclose($koneksi);
			xloc($filenya);
			exit();
			}
		else
			{
			//exit
			mysqli_free_result();
			xclose($koneksi);
				
			//salah
			$pesan = "Bukan File .xls . Harap Diperhatikan...!!";
			$ke = "$filenya?s=import";
			pekem($pesan,$ke);
			exit();
			}
		}
	}








//jika export
//export
if ($_POST['btnEX'])
	{
	//nama file e...
	$i_filename = "siswa-$tahun1-$tahun2.xls";
	$i_judul = "SISWA";
	
	


	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$sheet->setCellValue('A1', 'NO');
	$sheet->setCellValue('B1', 'NIS');
	$sheet->setCellValue('C1', 'NISN');
	$sheet->setCellValue('D1', 'NAMA');
	$sheet->setCellValue('E1', 'KELAS');
	$sheet->setCellValue('F1', 'LAHIR_TMP');
	$sheet->setCellValue('G1', 'LAHIR_TGL');
	$sheet->setCellValue('H1', 'KELAMIN');
	$sheet->setCellValue('I1', 'VERIFIKASI');

	$i = 2;		
	$no = 1;


	//data
	$qdt = mysqli_query($koneksi, "SELECT * FROM siswa ".
							"WHERE tapel_kd = '$tapelkd' ".
							"ORDER BY kelas ASC, ".
							"round(nis) ASC");
	$rdt = mysqli_fetch_assoc($qdt);



	do
		{
		//nilai
		$dt_nox = $dt_nox + 1;
		$dt_nis = balikin($rdt['nis']);
		$dt_nisn = balikin($rdt['nisn']);
		$dt_nama = balikin($rdt['nama']);
		$dt_kelas = balikin($rdt['kelas']);
		$dt_kelamin = balikin($rdt['kelamin']);
		$dt_lahir_tmp = balikin($rdt['lahir_tmp']);
		$dt_lahir_tgl = balikin($rdt['lahir_tgl']);
		$dt_aktif = balikin($rdt['aktif']);

		//jika belum aktif, perlu verifikasi
		if ($dt_aktif == "false")
			{
			$aktif_ket = "Belum Verifikasi";
			}
			
		else if ($dt_aktif == "true")
			{
			$aktif_ket = "AKTIF";
			}
	
	
		//ciptakan
		$sheet->setCellValue('A'.$i, $no++);
		$sheet->setCellValue('B'.$i, $dt_nis);
		$sheet->setCellValue('C'.$i, $dt_nisn);
		$sheet->setCellValue('D'.$i, $dt_nama);
		$sheet->setCellValue('E'.$i, $dt_kelas);
		$sheet->setCellValue('F'.$i, $dt_lahir_tmp);
		$sheet->setCellValue('G'.$i, $dt_lahir_tgl);
		$sheet->setCellValue('H'.$i, $dt_kelamin);
		$sheet->setCellValue('I'.$i, $aktif_ket);
		$i++;
		}
	while ($rdt = mysqli_fetch_assoc($qdt));






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
	mysqli_free_result();
	xclose($koneksi);
	xloc($filenya);
	exit();
	}











//jika cari
if ($_POST['btnCARI'])
	{
	//nilai
	$kunci = cegah($_POST['kunci']);


	//re-direct
	//exit
	mysqli_free_result();
	xclose($koneksi);
	$ke = "$filenya?kunci=$kunci";
	xloc($ke);
	exit();
	}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



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
//jika import
if ($s == "import")
	{
	?>
	<div class="row">

	<div class="col-md-12">
		
	<?php
	echo '<form action="'.$filenya.'" method="post" enctype="multipart/form-data" name="formxx2">
	<p>
		<input name="filex_xls" type="file" size="30" class="btn btn-warning">
	</p>

	<p>
		<input name="btnBTL" type="submit" value="BATAL" class="btn btn-info">
		<input name="btnIMX" type="submit" value="IMPORT >>" class="btn btn-danger">
	</p>
	
	
	</form>';	
	?>
		


	</div>
	
	</div>


	<?php
	}


else
	{
	echo '<form action="'.$filenya.'" method="post" name="formxx">';
		
	//jika null
	if (empty($kunci))
		{
		$sqlcount = "SELECT * FROM siswa ".
						"WHERE tapel_kd = '$tapelkd' ".
						"ORDER BY aktif_postdate DESC";
		}
		
	else
		{
		$sqlcount = "SELECT * FROM siswa ".
						"WHERE tapel_kd = '$tapelkd' ".
						"AND (nis LIKE '%$kunci%' ".
						"OR nisn LIKE '%$kunci%' ".
						"OR nama LIKE '%$kunci%' ".
						"OR kelas LIKE '%$kunci%' ".
						"OR kelamin LIKE '%$kunci%' ".
						"OR lahir_tmp LIKE '%$kunci%' ".
						"OR lahir_tgl LIKE '%$kunci%' ".
						"OR postdate LIKE '%$kunci%') ".
						"ORDER BY aktif_postdate DESC";
		}
		
	
	//query
	$p = new Pager();
	$start = $p->findStart($limit);
	
	$sqlresult = $sqlcount;
	
	$count = mysqli_num_rows(mysqli_query($koneksi, $sqlcount));
	$pages = $p->findPages($count, $limit);
	$result = mysqli_query($koneksi, "$sqlresult LIMIT ".$start.", ".$limit);
	$target = "$filenya?tapelkd=$tapelkd";
	$pagelist = $p->pageList($_GET['page'], $pages, $target);
	$data = mysqli_fetch_array($result);
	
	
	//ketahui jumlahnya
	$qyo = mysqli_query($koneksi, "SELECT * FROM siswa ". 
							"WHERE tapel_kd = '$tapelkd'");
	$ryo = mysqli_fetch_assoc($qyo);
	$tyo = mysqli_num_rows($qyo);
	
	
	
	
	
	echo '<hr>
	<p>
	<input name="kunci" type="text" value="'.$kunci2.'" size="20" class="btn btn-warning">
	<input name="btnCARI" type="submit" value="CARI" class="btn btn-danger">
	<input name="btnBTL" type="submit" value="RESET" class="btn btn-info">
	<input name="btnIM" type="submit" value="IMPORT EXCEL" class="btn btn-primary">
	<input name="btnEX" type="submit" value="EXPORT EXCEL" class="btn btn-success">
	<input name="s" type="hidden" value="'.$s.'">
	
	</p>
		
	
	
	[TOTAL : <b>'.$tyo.'</b>].
	
	<div class="table-responsive">          
	<table class="table" border="1">
	<thead>
	
	<tr valign="top" bgcolor="'.$warnaheader.'">
	<td width="50"><strong><font color="'.$warnatext.'">VERIFIKASI</font></strong></td>
	<td width="50"><strong><font color="'.$warnatext.'">NIS</font></strong></td>
	<td width="50"><strong><font color="'.$warnatext.'">NISN</font></strong></td>
	<td><strong><font color="'.$warnatext.'">NAMA</font></strong></td>
	<td width="150"><strong><font color="'.$warnatext.'">KELAS</font></strong></td>
	<td width="150"><strong><font color="'.$warnatext.'">KELAMIN</font></strong></td>
	<td width="150"><strong><font color="'.$warnatext.'">LAHIR</font></strong></td>
	
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
	
			$nomer = $nomer + 1;
			$i_kd = nosql($data['kd']);
			$i_postdate = balikin($data['postdate']);
			$i_nis = balikin($data['nis']);
			$i_nisn = balikin($data['nisn']);
			$i_nama = balikin($data['nama']);
			$i_kelas = balikin($data['kelas']);
			$i_kelamin = balikin($data['kelamin']);
			$i_lahir_tmp = balikin($data['lahir_tmp']);
			$i_lahir_tgl = balikin($data['lahir_tgl']);
			$i_aktif = balikin($data['aktif']);
			$i_aktif_postdate = balikin($data['aktif_postdate']);


			 
 
		
			echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
			echo '<td>'.$i_aktif_postdate.'</td>
			<td>'.$i_nis.'</td>
			<td>'.$i_nisn.'</td>
			<td>
			'.$i_nama.'
			</td>
			<td>
			'.$i_kelas.'
			</td>
			
			<td>'.$i_kelamin.'</td>
			<td>'.$i_lahir_tmp.', '.$i_lahir_tgl.'</td>
	        </tr>';
			}
		while ($data = mysqli_fetch_assoc($result));
		}
	
	
	echo '</tbody>
	  </table>
	  </div>
	
	
	<table width="500" border="0" cellspacing="0" cellpadding="3">
	<tr>
	<td>
	<strong><font color="#FF0000">'.$count.'</font></strong> Data. '.$pagelist.'
	<br>
	
	<input name="jml" type="hidden" value="'.$count.'">
	<input name="s" type="hidden" value="'.$s.'">
	<input name="kd" type="hidden" value="'.$kdx.'">
	<input name="page" type="hidden" value="'.$page.'">
	</td>
	</tr>
	</table>
	</form>';
	}




//isi
$isi = ob_get_contents();
ob_end_clean();

require("../../inc/niltpl.php");


//null-kan
mysqli_free_result();
xclose($koneksi);
exit();
?>