<?php
session_start();


//ambil nilai
require("../inc/config.php");
require("../inc/fungsi.php");
require("../inc/koneksi.php");
require("../inc/class/paging.php");



nocache;

//nilai
$filenya = "$sumber/android/i_soal.php";
$jkd = nosql($_SESSION['jkd']);
$kdx = $jkd;



//nilai session
$sesiku = $_SESSION['sesiku'];
$brgkd = $_SESSION['brgkd'];
$sesinama = $_SESSION['sesinama'];
$kd6_session = nosql($_SESSION['sesiku']);
$notaku = nosql($_SESSION['notaku']);
$notakux = md5($notaku);








//jika jawabku
if ((isset($_GET['aksi']) && $_GET['aksi'] == 'jawabku'))
	{
	?>
	<?php
	//null-kan
	mysqli_free_result();
	xclose($koneksi);
	exit();	
	}		





//jika form
if ((isset($_GET['aksi']) && $_GET['aksi'] == 'form'))
	{
	$tablenya = "siswa_soal";
	$tablenilai = "siswa_soal_nilai";
	
	$limit = 50;
	
	
	
	//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	
	//view //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//detail jkd jadwal
	$qku = mysqli_query($koneksi, "SELECT * FROM m_jadwal ".
							"WHERE kd = '$jkd'");
	$rku = mysqli_fetch_assoc($qku);
	mysqli_free_result($qku);
	$u_waktu = balikin($rku['waktu']);
	$u_pukul = balikin($rku['pukul']);
	$u_durasi = balikin($rku['durasi']);
	$u_mapel = balikin($rku['mapel']);
	$u_tingkat = balikin($rku['tingkat']);
	$u_proses = balikin($rku['proses']);
	
	
	
	//jumlah soal
	$qjml = mysqli_query($koneksi, "SELECT * FROM m_soal ".
							"WHERE jadwal_kd = '$jkd' ".
							"ORDER BY round(no) ASC");
	$tjml = mysqli_num_rows($qjml);	
		
		
		
	
	//yg dikerjakan...
	$qyuk = mysqli_query($koneksi, "SELECT * FROM $tablenya ".
							"WHERE siswa_kd = '$sesiku' ".
							"AND jadwal_kd = '$jkd' ".
							"AND jawab <> ''");
	$ryuk = mysqli_fetch_assoc($qyuk);
	$yuk_dikerjakan = mysqli_num_rows($qyuk);
	
	
	//jika lebih, itu tjml
	if ($yuk_dikerjakan > $tjml)
		{
		$yuk_dikerjakan = $tjml;
		}
	
	?>
	
	
	  <!-- Bootstrap 3.3.7 -->
	  <link rel="stylesheet" href="<?php echo $sumber;?>/template/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css">
	  <!-- Font Awesome -->
	  <link rel="stylesheet" href="<?php echo $sumber;?>/template/adminlte/bower_components/font-awesome/css/font-awesome.min.css">
	
	  <!-- Theme style -->
	  <link rel="stylesheet" href="<?php echo $sumber;?>/template/adminlte/dist/css/AdminLTE.css">
	  <!-- AdminLTE Skins. Choose a skin from the css/skins
	       folder instead of downloading all of them to reduce the load. -->
	  <link rel="stylesheet" href="<?php echo $sumber;?>/template/adminlte/dist/css/skins/skins-biasawae.css">
		
		
		





<style>


#myfooter{
   position: fixed;
   left: 0;
   bottom: 0;
  height: 4em;
  background-color: #f5f5f5;
  text-align: center;
   width: 100%;
   color: green;;

}




</style>





	<!--================ start footer Area  =================-->
	<footer>
	    <div class="footer">
		<div class="container-fluid"  id="myfooter">
		    <div class="row">
			<div class="col-md-12">

					<table border="0" width="100%">
					<tr valign="top">
					<td align="center" width="50%">
						<a href="#" data-toggle="modal" data-target="#myModalku">
							<font size="2"><p class="text-primary">DIJAWAB</p></font>
							<div id="udahjawab2"><?php echo $yuk_dikerjakan;?></div>
						</a>
					</td>
					
					<td align="center" width="50%">
						<a href="#asisawaktu">
							<font size="2"><p class="text-primary">SISA WAKTU</p></font>
							<div id="sisawaktu2"></div>
						</a>
					</td>
					
					</tr>
					</table>



			</div>
		    </div>
		</div>
	    </div>
	</footer>

	<!--================ End footer Area  =================-->






	
	<script language='javascript'>
	//membuat document jquery
	$(document).ready(function(){

		$.ajax({
			url: "<?php echo $sumber;?>/android/i_timer.php?aksi=sisawaktu2&jkd=<?php echo $jkd;?>&skd=<?php echo $sesiku;?>",
			type:$(this).attr("method"),
			data:$(this).serialize(),
			success:function(data){					
				$("#sisawaktu2").html(data);
				}
			});
			




		$.ajax({
			url: "<?php echo $sumber;?>/android/i_timer.php?aksi=sisawaktu&jkd=<?php echo $jkd;?>&skd=<?php echo $sesiku;?>",
			type:$(this).attr("method"),
			data:$(this).serialize(),
			success:function(data){					
				$("#sisawaktu").html(data);
				}
			});
			
			






		$.ajax({
			url: "<?php echo $sumber;?>/android/i_timer.php?aksi=setpostdate&jkd=<?php echo $jkd;?>&skd=<?php echo $sesiku;?>",
			type:$(this).attr("method"),
			data:$(this).serialize(),
			success:function(data){					
				$("#setpostdate").html(data);
				}
			});
			
			





		
		setInterval(poll,3000);
		
		function poll()
			{
			$.ajax({
				url: "<?php echo $sumber;?>/android/i_jawabku.php?aksi=form&jkd=<?php echo $jkd;?>&skd=<?php echo $sesiku;?>",
				type:$(this).attr("method"),
				data:$(this).serialize(),
				success:function(data){					
					$("#jawabanku").html(data);
					}
				});
			}
		
		

			
	});
	
	</script>
	




	  <!-- Modal -->
	  <div class="modal fade" id="myModalku" role="dialog">
	  	<br>
	  	<br>
	  	<br>
	  	<br>
	  	<br>
	  	<br>
	  	<br>
	  	<br>
	  	<br>
	  	<br>
	    <div class="modal-dialog">
	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <div class="modal-header">
	          <h4 class="modal-title">DETAIL DIJAWAB</h4>
	        </div>
	        <div class="modal-body">
	        	
				<div class="row">
					<div class="col-12" align="center">
	        
			        	<div id="jawabanku"></div>
			        	
			       </div>
			   </div>

				
	        </div>
	        <div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">OK >></button>
	        </div>
	      </div>
	      
	    </div>
	  </div>
  




		

    
	  
		  <script>
	  	$(document).ready(function() {
	    $('#table-responsive').dataTable( {
	        "scrollX": true
	    } );
	} );
	  </script>
	  


	
	      <div class="row">
	
	        <!-- /.col -->
	        <div class="col-md-6 col-sm-6 col-xs-12">
	          <div class="info-box">
	            <span class="info-box-icon bg-green"><i class="glyphicon glyphicon-edit"></i></span>
	
	            <div class="info-box-content">
	              <span class="info-box-text"><?php echo $u_mapel;?> [<?php echo $tjml;?> Soal]</span>
	              <span class="info-box-number">
	              
					<?php
					echo '<p>
					[<b>'.$u_waktu.'</b>]. [<b>'.$u_pukul.'</b>]. [<b>'.$u_durasi.' Menit</b>].
					</p>';
					?>
	
	              </span>
	            </div>
	            <!-- /.info-box-content -->
	          </div>
	          <!-- /.info-box -->
	        </div>
	        <!-- /.col -->
	
	
	
	        <!-- /.col -->
	        <div class="col-md-3 col-sm-6 col-xs-12">
	          <div class="info-box">
	            <span class="info-box-icon bg-blue"><i class="glyphicon glyphicon-education"></i></span>
	
	            <div class="info-box-content">
	              <span class="info-box-text">Telah Dikerjakan</span>
	              <span class="info-box-number">
	              <div id="udahjawab">
	              	<b><?php echo $yuk_dikerjakan;?></b>
					</div>
	
	              </span>
	            </div>
	            <!-- /.info-box-content -->
	          </div>
	          <!-- /.info-box -->
	        </div>
	        <!-- /.col -->
	
	
	
			<a name="asisawaktu"></a>
	
	        <!-- /.col -->
	        <div class="col-md-3 col-sm-6 col-xs-12">
	          <div class="info-box">
	            <span class="info-box-icon bg-yellow"><i class="glyphicon glyphicon-time"></i></span>
	
	            <div class="info-box-content">
	              <span class="info-box-text">Sisa Waktu</span>
	              <span class="info-box-number">
	              <div id="sisawaktu"></div>
	              <div id="setpostdate"></div>
	
	              </span>
	            </div>
	            <!-- /.info-box-content -->
	          </div>
	          <!-- /.info-box -->
	        </div>
	        <!-- /.col -->
	        
	        
	
	
	
	
	       </div>
	      <!-- /.row -->
	
	
	              
					
	<?php
	
	mysqli_free_result();
	
	echo '</form>
	<hr>';
	
	
	
	
		
	//jml soal yg ada
	$qyuk7 = mysqli_query($koneksi, "SELECT * FROM m_soal ".
							"WHERE jadwal_kd = '$jkd'");
	$ryuk7 = mysqli_fetch_assoc($qyuk7);
	$tyuk7 = mysqli_num_rows($qyuk7);
	
	
	
	
	//yg dijawab
	$qyuk8 = mysqli_query($koneksi, "SELECT * FROM $tablenya ".
							"WHERE jadwal_kd = '$jkd' ".
							"AND jawab <> ''");
	$ryuk8 = mysqli_fetch_assoc($qyuk8);
	$tyuk8 = mysqli_num_rows($qyuk8);
	
	
	
	
	
	
	mysqli_free_result();
	
	
	//yg dijawab
	$xyzz = md5("$jkd$sesiku");
	
	//insert
	mysqli_query($koneksi, "INSERT INTO $tablenilai(kd, jadwal_kd, waktu_mulai, postdate) VALUES ".
					"('$xyzz', '$jkd', '$today', '$today')");
	
	//insert
	mysqli_query($koneksi, "INSERT INTO siswa_soal_nilai(kd, jadwal_kd, waktu_mulai, postdate) VALUES ".
					"('$xyzz', '$jkd', '$today', '$today')");
	
						
	
	
	
	//jika udah semua... ///////////////////////////////////////////////////////////////////////////////////
	if ($tyuk7 <= $tyuk8)
		{
		//query
		$p = new Pager();
		$start = $p->findStart($limit);
		
		$sqlcount = "SELECT * FROM m_soal ".
						"WHERE jadwal_kd = '$jkd' ".
						"ORDER BY round(no) ASC";
		
		$sqlresult = $sqlcount;
		
		$count = mysqli_num_rows(mysqli_query($koneksi, $sqlcount));
		$pages = $p->findPages($count, $limit);
		$result = mysqli_query($koneksi, "$sqlresult LIMIT ".$start.", ".$limit);
		$pagelist = $p->pageList($_GET['page'], $pages, $target);
		$data = mysqli_fetch_array($result);
		
		
		
	
		do 
			{
			$i_kd = nosql($data['kd']);
			$i_nox = balikin($data['no']);
			$i_isi = balikin($data['isi']);
			$i_kunci = balikin($data['kunci']);
			$i_postdate = balikin($data['postdate']);
	
			
			//yg dijawab
			$qyuk = mysqli_query($koneksi, "SELECT * FROM $tablenya ".
									"WHERE siswa_kd = $sesiku' ".
									"AND jadwal_kd = '$jkd' ".
									"AND soal_kd = '$i_kd' ".
									"AND jawab <> ''");
			$ryuk = mysqli_fetch_assoc($qyuk);
			$yuk_kd = nosql($ryuk['kd']);
			$yuk_jawabku = balikin($ryuk['jawab']);
			
			
			//jika benar, true
			if ($i_kunci == $yuk_jawabku)
				{
				$setjawab = "true";	
				}
						
			else if ($i_kunci <> $yuk_jawabku)
				{
				$setjawab = "false";	
				}
				
	
	
			//yg dijawab
			$qyuk3 = mysqli_query($koneksi, "SELECT * FROM $tablenilai ".
									"WHERE siswa_kd = '$sesiku' ".
									"AND jadwal_kd = '$jkd'");
			$ryuk3 = mysqli_fetch_assoc($qyuk3);
			$tyuk3 = mysqli_num_rows($qyuk3);
							
			
			//jika ada, gak usah update...
			if (!empty($tyuk3))
				{
				//update
				mysqli_query($koneksi, "UPDATE $tablenya SET kunci = '$i_kunci', ".
								"benar = '$setjawab' ".
								"WHERE siswa_kd = '$sesiku' ".
								"AND kd = '$yuk_kd'");
								
								
								
				//update
				mysqli_query($koneksi, "UPDATE siswa_soal_nilai SET kunci = '$i_kunci', ".
								"benar = '$setjawab' ".
								"WHERE siswa_kd = '$sesiku' ".
								"AND kd = '$yuk_kd'");
				}
			}
		while ($data = mysqli_fetch_assoc($result));
	
		
		
		
		mysqli_free_result();
		
		
		//hitung yg benar
		$qyuk2 = mysqli_query($koneksi, "SELECT * FROM $tablenya ".
								"WHERE siswa_kd = '$sesiku' ".
								"AND jadwal_kd = '$jkd' ".
								"AND benar = 'true'");
		$ryuk2 = mysqli_fetch_assoc($qyuk2);
		$jml_benar = mysqli_num_rows($qyuk2);
		$jml_salah = $count - $jml_benar; 
	
	
		//update
		mysqli_query($koneksi, "UPDATE $tablenilai SET jml_benar = '$jml_benar', ".
						"jml_salah = '$jml_salah', ".
						"postdate = '$today' ".
						"WHERE siswa_kd = '$sesiku' ".
						"AND jadwal_kd = '$jkd'");
						
						
		//update
		mysqli_query($koneksi, "UPDATE siswa_soal_nilai SET jml_benar = '$jml_benar', ".
						"jml_salah = '$jml_salah', ".
						"postdate = '$today' ".
						"WHERE siswa_kd = '$sesiku' ".
						"AND jadwal_kd = '$jkd'");
		?>
	
	
	
	        <!-- /.col -->
	        <div class="col-md-12 col-sm-6 col-xs-12">
	          <div class="info-box">
	            <span class="info-box-icon bg-red"><i class="glyphicon glyphicon-duplicate"></i></span>
	
	            <div class="info-box-content">
	              <span class="info-box-text">Rekap Jawaban</span>
	              <span class="info-box-number">
	              [Benar : <font color="green"><?php echo $jml_benar;?></font>].
	              [Salah : <font color="red"><?php echo $jml_salah;?></font>]. 
	
	              </span>
	            </div>
	            <!-- /.info-box-content -->
	          </div>
	          <!-- /.info-box -->
	        </div>
	        <!-- /.col -->
	        
	        
		<?php
		//null-kan
		mysqli_free_result();
		xclose($koneksi);
		exit();
		}
	
	
	
	else		
	
		{
		
		?>
		
		   <!-- /.col -->
	        <div class="col-md-12 col-sm-6 col-xs-12">
	          <div class="info-box">
	            <span class="info-box-icon bg-red"><i class="glyphicon glyphicon-pushpin"></i></span>
	
	            <div class="info-box-content">
	              <span class="info-box-text">PERHATIAN</span>
	              <span class="info-box-number">
	              Pastikan semua soal telah dikerjakan, selanjutnya bisa tekan tombol "Selesai Mengerjakan". Terima Kasih.  
	
	              </span>
	            </div>
	            <!-- /.info-box-content -->
	          </div>
	          <!-- /.info-box -->
	        </div>
	        <!-- /.col -->
	        
	
	
		<?php
		//query
		$p = new Pager();
		$start = $p->findStart($limit);
		
		$sqlcount = "SELECT * FROM m_soal ".
						"WHERE jadwal_kd = '$jkd' ".
						"ORDER BY round(no) ASC";
		
		$sqlresult = $sqlcount;
		
		$count = mysqli_num_rows(mysqli_query($koneksi, $sqlcount));
		$pages = $p->findPages($count, $limit);
		$result = mysqli_query($koneksi, "$sqlresult LIMIT ".$start.", ".$limit);
		$pagelist = $p->pageList($_GET['page'], $pages, $target);
		$data = mysqli_fetch_array($result);
		
		
		echo "&nbsp;";
		
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
			$i_kunci = balikin($data['kunci']);
			$i_isi = balikin($data['isi']);
			$i_postdate = balikin($data['postdate']);
	
			
			//yg dijawab
			$qyuk = mysqli_query($koneksi, "SELECT * FROM $tablenya ".
									"WHERE siswa_kd = '$sesiku' ".
									"AND jadwal_kd = '$jkd' ".
									"AND soal_kd = '$i_kd'");
			$ryuk = mysqli_fetch_assoc($qyuk);
			mysqli_free_result($qyuk);
			$yuk_kdku = nosql($ryuk['kd']);
			$yuk_jawabku = balikin($ryuk['jawab']);
			
			
			
			
			//nilai
			$xyz = md5("$sesiku$jkd$i_kd");
			
	
			//insert
			mysqli_query($koneksi, "INSERT INTO $tablenya(kd, siswa_kd, jadwal_kd, soal_kd, jawab, postdate) VALUES ".
							"('$xyz', '$sesiku', '$jkd', '$i_kd', '', '$today')");
							
			
			?>
				<script language='javascript'>
			//membuat document jquery
			$(document).ready(function(){
			
			
				$('#xpilih<?php echo $nomer;?>').change(function() {
					var nilku = $(this).val();
		
					$("#iproses<?php echo $i_kd;?>").show();
					
					$.ajax({
						url: "<?php echo $sumber;?>/android/i_jawab.php?aksi=simpan&jkd=<?php echo $jkd;?>&skd=<?php echo $sesiku;?>&soalkd=<?php echo $i_kd;?>&nilku="+nilku,
						type:$(this).attr("method"),
						data:$(this).serialize(),
						success:function(data){					
							$("#ihasil<?php echo $nomer;?>").html(data);
							}
						});
					
					
					
					
					$.ajax({
						url: "<?php echo $sumber;?>/android/i_jawab.php?aksi=hitung&jkd=<?php echo $jkd;?>&skd=<?php echo $sesiku;?>&soalkd=<?php echo $i_kd;?>&nilku="+nilku,
						type:$(this).attr("method"),
						data:$(this).serialize(),
						success:function(data){					
							$("#udahjawab").html(data);		
							$("#udahjawab2").html(data);
							}
						});
					
					
					
					
					$.ajax({
						url: "<?php echo $sumber;?>/android/i_timer.php?aksi=setpostdate&jkd=<?php echo $jkd;?>&skd=<?php echo $sesiku;?>",
						type:$(this).attr("method"),
						data:$(this).serialize(),
						success:function(data){					
							$("#setpostdate").html(data);
							}
						});
						
						
					$("#iproses<?php echo $i_kd;?>").hide();
						
					
			    });
	
	
					
			});
			
			</script>
	
	
	
	
			<?php
	
			echo '<div class="table-responsive">          
			<table class="table" border="1">
			<thead>
			<tr valign="top" bgcolor="'.$warnaheader.'">
			<td width="50"><strong><font color="'.$warnatext.'">NO</font></strong></td>
			<td><strong><font color="'.$warnatext.'">SOAL</font></strong></td>
			</tr>
			</thead>
			<tbody>';
					
			echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
			echo '<td align="center"><a name="ku'.$i_kd.'"></a>'.$i_no.'.</td>
			<td>
			'.$i_isi.'
			
			<hr>
			
			<p>
			 
			<form name="xformx'.$nomer.'" id="xformx'.$nomer.'">
			Jawab : <select name="xpilih'.$nomer.'" id="xpilih'.$nomer.'" class="btn btn-warning">
						<option value="'.$yuk_jawabku.'" selected>'.$yuk_jawabku.'</option>	
						<option value="A">A</option>	
						<option value="B">B</option>	
						<option value="C">C</option>	
						<option value="D">D</option>	
						<option value="E">E</option>	
						</select>			
			
			</p>		
			</form>
					
			<div id="iproses'.$i_kd.'" style="display:none">
				<img src="'.$sumber.'/template/img/progress-bar.gif" width="100" height="16">
			</div>
			
			<div id="ihasil'.$nomer.'"></div>
			
			
			</td>
	        </tr>
			</tbody>
		  	</table>
		  	</div>';
		
			
			//update ///////////////////////////////////////////////////////////////////////////////////
			//yg dijawab
			$qyuk = mysqli_query($koneksi, "SELECT * FROM $tablenya ".
									"WHERE siswa_kd = '$sesiku' ".
									"AND jadwal_kd = '$jkd' ".
									"AND soal_kd = '$i_kd' ".
									"AND jawab <> ''");
			$ryuk = mysqli_fetch_assoc($qyuk);
			mysqli_free_result($qyuk);
			$yuk_kd = nosql($ryuk['kd']);
			$yuk_jawabku = balikin($ryuk['jawab']);
			
			
			//jika benar, true
			if ($i_kunci == $yuk_jawabku)
				{
				$setjawab = "true";	
				}
						
			else if ($i_kunci <> $yuk_jawabku)
				{
				$setjawab = "false";	
				}
				
	
	
			//update
			mysqli_query($koneksi, "UPDATE $tablenya SET kunci = '$i_kunci', ".
							"benar = '$setjawab' ".
							"WHERE kd = '$yuk_kdku'");
			}
		while ($data = mysqli_fetch_assoc($result));
		
	
	
	
					
		?>
		
		<script language='javascript'>
		//membuat document jquery
		$(document).ready(function(){
			
			$("#btnSELESAI").on('click', function(){
				
				$("#xformselesai").submit(function(){
					$.ajax({
						url: "<?php echo $sumber;?>/android/i_jawab.php?aksi=selesai&jkd=<?php echo $jkd;?>&skd=<?php echo $sesiku;?>",
						type:$(this).attr("method"),
						data:$(this).serialize(),
						success:function(data){					
							$("#ihasilselesai").html(data);
							}
						});
					return false;
				});
			
			
			});	
	
	
				
		});
		
		</script>
	
	
		<?php
			
		
		echo '<br>
		
		<div id="ihasilselesai"></div>
		<form name="xformselesai" id="xformselesai">
		<hr>
		<input name="btnSELESAI" id="btnSELESAI" type="submit" class="btn btn-block btn-success" value="SELESAI MENGERJAKAN.">
		<hr>
		
		</form>
		
		
		<br>
		<br>
		<br>';
		
		
		
		
		
		
		mysqli_free_result();
		
		
		//jml soal yg ada
		$qyuk7 = mysqli_query($koneksi, "SELECT * FROM m_soal ".
								"WHERE jadwal_kd = '$jkd'");
		$ryuk7 = mysqli_fetch_assoc($qyuk7);
		$tyuk7 = mysqli_num_rows($qyuk7);
		mysqli_free_result($qyuk7);
		
		//hitung yg benar
		$qyuk2 = mysqli_query($koneksi, "SELECT * FROM $tablenya ".
								"WHERE siswa_kd = '$sesiku' ".
								"AND jadwal_kd = '$jkd' ".
								"AND benar = 'true'");
		$ryuk2 = mysqli_fetch_assoc($qyuk2);
		mysqli_free_result($qyuk2);
		$jml_benar = mysqli_num_rows($qyuk2);
		$jml_salah = $count - $jml_benar; 
		$xyzz = md5("$jkd$sesiku");
	
	
						
	
		//update
		mysqli_query($koneksi, "UPDATE $tablenilai SET jml_benar = '$jml_benar', ".
						"jml_salah = '$jml_salah', ".
						"postdate = '$today' ".
						"WHERE siswa_kd = '$sesiku' ".
						"AND jadwal_kd = '$jkd'");
						
						
		//update
		mysqli_query($koneksi, "UPDATE siswa_soal_nilai SET jml_benar = '$jml_benar', ".
						"jml_salah = '$jml_salah', ".
						"postdate = '$today' ".
						"WHERE siswa_kd = '$sesiku' ".
						"AND jadwal_kd = '$jkd'");
	
	
		//null-kan
		mysqli_free_result();
		xclose($koneksi);
		exit();
		}					
		
		
	//null-kan
	mysqli_free_result();
	xclose($koneksi);
	exit();			
	}	


//null-kan
mysqli_free_result();
xclose($koneksi);
exit();
?>