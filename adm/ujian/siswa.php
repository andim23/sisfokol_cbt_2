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
$filenya = "siswa.php";
$judul = "[Ujian] Ujian Siswa";
$judulku = "$judul";
$judulx = $judul;
$kd = nosql($_REQUEST['kd']);
$jkd = nosql($_REQUEST['jkd']);
$s = nosql($_REQUEST['s']);
$kunci = cegah($_REQUEST['kunci']);
$kunci2 = balikin($_REQUEST['kunci']);
$page = nosql($_REQUEST['page']);
if ((empty($page)) OR ($page == "0"))
	{
	$page = "1";
	}



//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//jika mulai
if ($s == "mulai")
	{
	//update
	mysqli_query($koneksi, "UPDATE m_jadwal SET proses = 'true', ".
					"postdate_mulai = '$today' ".
					"WHERE kd = '$jkd'");
					
	//re-direct
	$ke = "$filenya?kunci=$kunci";
	xloc($ke);
	exit();	
	}




//jika selesai
if ($s == "selesai")
	{
	//update
	mysqli_query($koneksi, "UPDATE m_jadwal SET proses = 'false', ".
					"postdate_selesai = '$today' ".
					"WHERE kd = '$jkd'");
					
	//re-direct
	$ke = "$filenya?kunci=$kunci";
	xloc($ke);
	exit();	
	}

	
	
	
	





//nek batal
if ($_POST['btnBTL'])
	{
	//re-direct
	xloc($filenya);
	exit();
	}





//jika cari
if ($_POST['btnCARI'])
	{
	//nilai
	$kunci = cegah($_POST['kunci']);


	//re-direct
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
//jika null
if (empty($kunci))
	{
	$sqlcount = "SELECT * FROM m_jadwal ".
					"ORDER BY proses ASC, ".
					"round(no) ASC, ".
					"pukul ASC";
	}
	
else
	{
	$sqlcount = "SELECT * FROM m_jadwal ".
					"WHERE waktu LIKE '%$kunci%' ".
					"OR durasi LIKE '%$kunci%' ".
					"OR mapel LIKE '%$kunci%' ".
					"OR tingkat LIKE '%$kunci%' ".
					"ORDER BY proses ASC, ".
					"round(no) ASC, ".
					"pukul ASC";
	}
	
	

//query
$p = new Pager();
$start = $p->findStart($limit);

$sqlresult = $sqlcount;

$count = mysqli_num_rows(mysqli_query($koneksi, $sqlcount));
$pages = $p->findPages($count, $limit);
$result = mysqli_query($koneksi, "$sqlresult LIMIT ".$start.", ".$limit);
$pagelist = $p->pageList($_GET['page'], $pages, $target);
$data = mysqli_fetch_array($result);



//ketahui ujian yg sedang berjalan...
$qyuk = mysqli_query($koneksi, "SELECT * FROM m_jadwal ".
						"WHERE proses = 'true'");
$tyuk = mysqli_num_rows($qyuk);




echo '<form action="'.$filenya.'" method="post" name="formx">
<p>
<input name="kunci" type="text" value="'.$kunci2.'" size="20" class="btn btn-warning" placeholder="Kata Kunci...">
<input name="btnCARI" type="submit" value="CARI" class="btn btn-danger">
<input name="btnBTL" type="submit" value="RESET" class="btn btn-info">
</p>
	




<p>
Ujian Sedang Berlangsung : <font color="green"><b>'.$tyuk.'</b></font>
</p>

<div class="table-responsive">          
<table class="table" border="1">
<thead>

<tr valign="top" bgcolor="'.$warnaheader.'">
<td width="50"><strong><font color="'.$warnatext.'">NO</font></strong></td>
<td><strong><font color="'.$warnatext.'">WAKTU</font></strong></td>
<td width="150"><strong><font color="'.$warnatext.'">PUKUL</font></strong></td>
<td width="50"><strong><font color="'.$warnatext.'">DURASI</font></strong></td>
<td><strong><font color="'.$warnatext.'">MAPEL</font></strong></td>
<td width="100"><strong><font color="'.$warnatext.'">TINGKAT</font></strong></td>
<td width="100"><strong><font color="'.$warnatext.'">STATUS</font></strong></td>
<td width="100"><strong><font color="'.$warnatext.'">LOG</font></strong></td>
<td width="100"><strong><font color="'.$warnatext.'">REKAP</font></strong></td>
<td width="100"><strong><font color="'.$warnatext.'">ULANG TES</font></strong></td>
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
		$i_no = balikin($data['no']);
		$i_waktu = balikin($data['waktu']);
		$i_pukul = balikin($data['pukul']);
		$i_durasi = balikin($data['durasi']);
		$i_mapel = balikin($data['mapel']);
		$i_tingkat = balikin($data['tingkat']);
		$i_soal_jml = balikin($data['soal_jml']);
		$i_soal_postdate = balikin($data['soal_postdate']);
		$i_postdate_mulai = balikin($data['postdate_mulai']);
		$i_postdate_selesai = balikin($data['postdate_selesai']);
		$i_proses = balikin($data['proses']);



		echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
		echo '<td>'.$i_no.'</td>
		<td>'.$i_waktu.'</td>
		<td>'.$i_pukul.'</td>
		<td>'.$i_durasi.'</td>
		<td>
		'.$i_mapel.'
		
		<hr>
		<p>
		Jumlah Soal : <b>'.$i_soal_jml.'</b>
		</p>
		</td>
		<td>'.$i_tingkat.'</td>';
		
		
		//jika sedang proses ujian
		if ($i_proses == "true")
			{
			echo '<td>
			<p>
			<a href="'.$filenya.'?jkd='.$i_kd.'&s=selesai" class="btn btn-block btn-danger">SELESAI ></a>
			</p>
			
			<p>
			Postdate Mulai :
			<br>
			<b>'.$i_postdate_mulai.'</b>
			</p>
			
			
			</td>';
			}


		//jika belum ato selesai...
		else if ($i_proses == "false")
			{
			echo '<td>
			<p>
			<a href="'.$filenya.'?jkd='.$i_kd.'&s=mulai" class="btn btn-block btn-success">MULAI ></a>
			</p>
			
			<p>
			Postdate Mulai :
			<br>
			<b>'.$i_postdate_mulai.'</b>
			</p>
			
			
			<p>
			Postdate Selesai :
			<br>
			<b>'.$i_postdate_selesai.'</b>
			</p>
			
			
			</td>';
			}
			
					
					
					
		echo '<td>
		<p>
			<a href="siswa_log.php?jkd='.$i_kd.'" class="btn btn-block btn-primary">LOG SISWA</a>
		</p>
		</td>
		<td>
		<p>
			<a href="siswa_rekap.php?jkd='.$i_kd.'" class="btn btn-block btn-primary">REKAP</a>
		</p>
		</td>
		<td>
		<p>
			<a href="siswa_ulang.php?jkd='.$i_kd.'" class="btn btn-block btn-warning">ULANG TES</a>
		</p>
		</td>
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







//isi
$isi = ob_get_contents();
ob_end_clean();

require("../../inc/niltpl.php");


//null-kan
xclose($koneksi);
exit();
?>