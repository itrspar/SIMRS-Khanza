<?php
    if(strpos($_SERVER['REQUEST_URI'],"pages")){
        exit(header("Location:../index.php"));
    }
?>

<div id="post">
    <div class="entry">

    <div align="center" class="link">
        <a href=?act=InputPegawai&action=TAMBAH>| Input Data |</a>
        <a href=?act=ListCariPegawai>| List Data |</a>
        <a href=?act=ListIndexPegawai>| Index Pegawai |</a>
        <a href=?act=HomeAdmin>| Menu Utama |</a>
    </div>
        <form name="frm_aturadmin" onsubmit="return validasiIsi();" method="post" action="" enctype=multipart/form-data>
        <?php
            $_sqlthn         = "SELECT * FROM set_tahun";
            $hasilthn        = bukaquery($_sqlthn);
            $baristhn        = mysqli_fetch_row($hasilthn);
            $tahun     = empty($baristhn[0])?date("Y"):$baristhn[0];
            $blnini    = empty($baristhn[1])?date("m"):$baristhn[1];
            $hari      = empty($baristhn[2])?date("d"):$baristhn[2];
            $bln_leng  = strlen($blnini);
            $bulan     = "0";
            if ($bln_leng==1){
                $bulan="0".$blnini;
            }else{
                $bulan=$blnini;
            }

            $keyword = trim(isset($_POST['keyword']))?trim($_POST['keyword']):NULL;
            $keyword = validTeks($keyword);
            $status  = validTeks(trim(isset($_POST['status']))?trim($_POST['status']):"AKTIF");
            echo "<input type=hidden name=keyword value=$keyword><input type=hidden name=status value=$status>";
        ?>
          <table width="100%" align="center">
              <tr class="head">
                  <td width="10%">Status</td><td width="">:</td>
                  <td width="25%">
                      <select name="status" class="text2">
                         <?php
                            echo "<option value='$status'>$status</option>";
                         ?>
                         <option value='AKTIF'>AKTIF</option>
                         <option value='CUTI'>CUTI</option>
                         <option value='TENAGA LUAR'>TENAGA LUAR</option>
                         <option value='KELUAR'>KELUAR</option>
                      </select>
                  </td>
                  <td width="10%">Keyword</td><td width="">:</td>
                  <td width="63%">
                      <input name="keyword" class="text" onkeydown="setDefault(this, document.getElementById('MsgIsi1'));" type=text id="TxtIsi1" value="<?php echo $keyword;?>" size="65" maxlength="50" pattern="[a-zA-Z 0-9-]{1,50}" title=" a-z A-Z 0-9 (Maksimal 50 karakter)" autocomplete="off" autofocus/>
                      <input name=BtnCari type=submit class="button" value="&nbsp;&nbsp;Cari&nbsp;&nbsp;">
                  </td>
              </tr>
        </table><br>

    <div style="width: 100%; height: 78%; overflow: auto;">
    <?php
        $_sql = "select pegawai.id,pegawai.nik,pegawai.nama,pegawai.jbtn,pegawai.pendidikan,pegawai.mulai_kerja,
                kelompok_jabatan.indek as indekkelompok,resiko_kerja.indek as indekresiko,emergency_index.indek as indekemergency,
                jnj_jabatan.indek as indekjabatan,CONCAT(FLOOR(PERIOD_DIFF(DATE_FORMAT('$tahun-$bulan-$hari', '%Y%m'),
                DATE_FORMAT(mulai_kerja, '%Y%m'))/12), ' Tahun ',MOD(PERIOD_DIFF(DATE_FORMAT('$tahun-$bulan-$hari', '%Y%m'), DATE_FORMAT(mulai_kerja, '%Y%m')),12), ' Bulan ') as lama,
                pendidikan.indek as index_pendidikan,(To_days('$tahun-$bulan-$hari')-to_days(mulai_kerja))/365 as masker,stts_kerja.indek as index_status,stts_kerja.hakcuti,
                pegawai.indek as index_struktural,pegawai.pengurang,pegawai.mulai_kontrak,CONCAT(FLOOR(PERIOD_DIFF(DATE_FORMAT('$tahun-$bulan-$hari', '%Y%m'),DATE_FORMAT(mulai_kontrak, '%Y%m'))/12), ' Tahun ',MOD(PERIOD_DIFF(DATE_FORMAT('$tahun-$bulan-$hari', '%Y%m'), DATE_FORMAT(mulai_kontrak, '%Y%m')),12), ' Bulan ') as lamakontrak,
                (To_days('$tahun-$bulan-$hari')-to_days(mulai_kontrak))/365 as maskon, pegawai.cuti_diambil,pegawai.dankes
                from pegawai inner join pendidikan on pegawai.pendidikan=pendidikan.tingkat inner join stts_kerja on pegawai.stts_kerja=stts_kerja.stts
                inner join kelompok_jabatan on pegawai.kode_kelompok=kelompok_jabatan.kode_kelompok inner join resiko_kerja on pegawai.kode_resiko=resiko_kerja.kode_resiko
                inner join emergency_index on pegawai.kode_emergency=emergency_index.kode_emergency inner join jnj_jabatan on pegawai.jnj_jabatan=jnj_jabatan.kode
                where stts_aktif='$status' ".(!empty($keyword)?"and (pegawai.nik like '%".$keyword."%' or pegawai.nama like '%".$keyword."%' or pegawai.jbtn like '%".$keyword."%' or
                pegawai.pendidikan like '%".$keyword."%' or pegawai.mulai_kerja like '%".$keyword."%')":"")." order by pegawai.id ASC ";
        $hasil=bukaquery($_sql);
        $jumlah=mysqli_num_rows($hasil);

        if(mysqli_num_rows($hasil)!=0) {
            echo "<table width='2950px' border='0' align='center' cellpadding='0' cellspacing='0' class='tbl_form'>
                    <tr class='head'>
                         <td width='60px'><div align='center'>Proses</div></td>
                         <td width='80px'><div align='center'>NIP</div></td>
                         <td width='190px'><div align='center'>Nama</div></td>
                         <td width='100px'><div align='center'>Jabatan</div></td>
                         <td width='150px'><div align='center'>Pendidikan</div></td>
                         <td width='80px'><div align='center'>Mulai Kerja</div></td>
                         <td width='150px'><div align='center'>Lama Kerja</div></td>
                         <td width='80px'><div align='center'>Index Pendidikan</div></td>
                         <td width='80px'><div align='center'>Index Masa Kerja</div></td>
                         <td width='100px'><div align='center'>Index Status</div></td>
                         <td width='100px'><div align='center'>Index Jenjang Jabatan</div></td>
                         <td width='100px'><div align='center'>Index Kelompok Jabatan</div></td>
                         <td width='100px'><div align='center'>Index Resiko Kerjo</div></td>
                         <td width='100px'><div align='center'>Index Tingkat Emergency</div></td>
                         <td width='100px'><div align='center'>Index Evaluasi Kinerja</div></td>
                         <td width='100px'><div align='center'>Index Pencapaian Kinerja</div></td>
                         <td width='80px'><div align='center'>Index Struktural</div></td>
                         <td width='80px'><div align='center'>Pengurang</div></td>
                         <td width='100px'><div align='center'>Total Index</div></td>
                         <td width='80px'><div align='center'>Mulai Kontrak</div></td>
                         <td width='150px'><div align='center'>Lama Kontrak</div></td>
                         <td width='150px'><div align='center'>Gaji Pokok</div></td>
                         <td width='100px'><div align='center'>Hak Cuti</div></td>
                         <td width='100px'><div align='center'>Cuti Diambil</div></td>
                         <td width='100px'><div align='center'>Sisa Cuti</div></td>
                         <td width='100px'><div align='center'>DanKes</div></td>
                         <td width='100px'><div align='center'>Sisa DanKes</div></td>
                    </tr>";
                    while($baris = mysqli_fetch_array($hasil)) {
                        $_sql4     = "SELECT pendidikan.gapok1,pendidikan.kenaikan,pendidikan.maksimal from pendidikan  where pendidikan.tingkat='".$baris["pendidikan"]."' ";
                        $hasil4    = bukaquery($_sql4);
                        $baris4    = mysqli_fetch_array($hasil4);
                        $gapok     = 0;
                        @$gapok1   = $baris4["gapok1"];
                        @$kenaikan = $baris4["kenaikan"];
                        @$maksimal = $baris4["maksimal"];

                        $_sql6     = "SELECT sum(jml) from ketidakhadiran  where id='$baris[0]' and tgl like '%".$tahun."%' and jns='C' group by id";
                        $hasil6    = bukaquery($_sql6);
                        $baris6    = mysqli_fetch_row($hasil6);
                        @$cuti     = $baris6[0];
                        if(empty ($cuti)){
                            $ttlc = 0;
                        }

                        $ttlc      = $cuti+$baris["cuti_diambil"]+getOne("select sum(jumlah) from pengajuan_cuti where tanggal_awal like '%".$tahun."%' and status='Disetujui' and nik='".$baris["nik"]."'");

                        $masa_kerja=0;
                        if($baris["masker"]<1){
                            $masa_kerja=0;
                        }else if(($baris["masker"]>=1)&&($baris["masker"]<2)){
                            $masa_kerja=2;
                        }else if(($baris["masker"]>=2)&&($baris["masker"]<3)){
                            $masa_kerja=4;
                        }else if(($baris["masker"]>=3)&&($baris["masker"]<4)){
                            $masa_kerja=6;
                        }else if(($baris["masker"]>=4)&&($baris["masker"]<5)){
                            $masa_kerja=8;
                        }else if(($baris["masker"]>=5)&&($baris["masker"]<6)){
                            $masa_kerja=10;
                        }else if(($baris["masker"]>=6)&&($baris["masker"]<7)){
                            $masa_kerja=12;
                        }else if($baris["masker"]>=7){
                            $masa_kerja=14;
                        }

                        if($baris["maskon"]<$maksimal){
                            $gapok=$gapok1+($kenaikan*round($baris["maskon"]));
                        }elseif($baris["maskon"]>=$maksimal){
                            $gapok=$gapok1+($kenaikan*$maksimal);
                        }

                        $indexevaluasi= getOne("select evaluasi_kinerja.indek from evaluasi_kinerja inner join evaluasi_kinerja_pegawai
                                                on evaluasi_kinerja_pegawai.kode_evaluasi=evaluasi_kinerja.kode_evaluasi where
                                                evaluasi_kinerja_pegawai.id='$baris[0]' order by evaluasi_kinerja_pegawai.tahun,
                                                evaluasi_kinerja_pegawai.bulan desc limit 1");
                        if(empty($indexevaluasi)){
                            $indexevaluasi=0;
                        }

                        $indexpencapaian= getOne("select pencapaian_kinerja.indek from pencapaian_kinerja inner join pencapaian_kinerja_pegawai
                                                on pencapaian_kinerja_pegawai.kode_pencapaian=pencapaian_kinerja.kode_pencapaian where
                                                pencapaian_kinerja_pegawai.id='$baris[0]' order by pencapaian_kinerja_pegawai.tahun,
                                                pencapaian_kinerja_pegawai.bulan desc limit 1");
                        if(empty($indexpencapaian)){
                            $indexpencapaian=0;
                        }

                        $total=0;
                        if($baris["pengurang"]==0){
                            $total=($baris["index_pendidikan"]+$masa_kerja+$baris["index_status"]+$baris["index_struktural"]+
                                    $baris["indekjabatan"]+$baris["indekkelompok"]+$baris["indekresiko"]+$baris["indekemergency"]+
                                    $indexevaluasi+$indexpencapaian);
                        }else if($baris["pengurang"]>0){
                            $total=($baris["index_pendidikan"]+$masa_kerja+$baris["index_status"]+$baris["index_struktural"]+
                                    $baris["indekjabatan"]+$baris["indekkelompok"]+$baris["indekresiko"]+$baris["indekemergency"]+
                                    $indexevaluasi+$indexpencapaian)*($baris["pengurang"]/100);
                        }

                        echo "<tr class='isi' title='$baris[1] $baris[2]'>
                                  <td width='70'>
                                        <center>
                                        <a href=?act=EditIndexPegawai&action=UBAH&id=$baris[0]>[edit]</a>";
                           echo "       </center>
                                </td>
                                <td><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["nik"]."</a></td>
                                <td><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["nama"]."</a></td>
                                <td><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["jbtn"]."</a></td>
                                <td><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["pendidikan"]."</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["mulai_kerja"]."</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["lama"]."</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["index_pendidikan"]."</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>$masa_kerja</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["index_status"]."</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["indekjabatan"]."</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["indekkelompok"]."</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["indekresiko"]."</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["indekemergency"]."</a></td>
                                <td align='center'><a href=?act=DetailEvaluasiKinerja&action=TAMBAH&id=$baris[0]>".$indexevaluasi."</a></td>
                                <td align='center'><a href=?act=DetailPencapaianKinerja&action=TAMBAH&id=$baris[0]>".$indexpencapaian."</a></td>
                                <td align='center'><a href=?act=EditIndexPegawai&action=UBAH&id=$baris[0]>".$baris["index_struktural"]."</a></td>
                                <td align='center'><a href=?act=EditIndexPegawai&action=UBAH&id=$baris[0]>".$baris["pengurang"]." %</a></td>
                                <td align='center'><a href=?act=EditIndexPegawai&action=UBAH&id=$baris[0]>$total</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["mulai_kontrak"]."</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["lamakontrak"]."</a></td>
                                <td><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".formatDuit($gapok)."</a></td>
                                <td align='center'><a href=?act=InputPegawai&action=UBAH&id=$baris[0]>".$baris["hakcuti"]."</a></td>
                                <td align='center'><a href=?act=EditIndexPegawai&action=UBAH&id=$baris[0]>$ttlc</a></td>
                                <td align='center'><a href=?act=EditIndexPegawai&action=UBAH&id=$baris[0]>".($baris["hakcuti"]-$ttlc)."</a></td>
                                <td><a href=?act=EditIndexPegawai&action=UBAH&id=$baris[0]>".formatDuit($baris["dankes"])."</a></td>
                                <td><a href=?act=SisaDankes&id=$baris[0]&action=TAMBAH>".formatDuit($baris["dankes"]-getOne("select sum(dankes) from ambil_dankes where id='$baris[0]' and tanggal like '%$tahun%'"))."</a></td>
                              </tr>";
                    }
            echo "</table>";

        } else {
          echo "<table width='2950px' border='0' align='center' cellpadding='0' cellspacing='0' class='tbl_form'>
                  <tr class='head'>
                       <td width='60px'><div align='center'>Proses</div></td>
                       <td width='80px'><div align='center'>NIP</div></td>
                       <td width='190px'><div align='center'>Nama</div></td>
                       <td width='100px'><div align='center'>Jabatan</div></td>
                       <td width='150px'><div align='center'>Pendidikan</div></td>
                       <td width='80px'><div align='center'>Mulai Kerja</div></td>
                       <td width='150px'><div align='center'>Lama Kerja</div></td>
                       <td width='80px'><div align='center'>Index Pendidikan</div></td>
                       <td width='80px'><div align='center'>Index Masa Kerja</div></td>
                       <td width='100px'><div align='center'>Index Status</div></td>
                       <td width='100px'><div align='center'>Index Jenjang Jabatan</div></td>
                       <td width='100px'><div align='center'>Index Kelompok Jabatan</div></td>
                       <td width='100px'><div align='center'>Index Resiko Kerjo</div></td>
                       <td width='100px'><div align='center'>Index Tingkat Emergency</div></td>
                       <td width='100px'><div align='center'>Index Evaluasi Kinerja</div></td>
                       <td width='100px'><div align='center'>Index Pencapaian Kinerja</div></td>
                       <td width='80px'><div align='center'>Index Struktural</div></td>
                       <td width='80px'><div align='center'>Pengurang</div></td>
                       <td width='100px'><div align='center'>Total Index</div></td>
                       <td width='80px'><div align='center'>Mulai Kontrak</div></td>
                       <td width='150px'><div align='center'>Lama Kontrak</div></td>
                       <td width='150px'><div align='center'>Gaji Pokok</div></td>
                       <td width='100px'><div align='center'>Hak Cuti</div></td>
                       <td width='100px'><div align='center'>Cuti Diambil</div></td>
                       <td width='100px'><div align='center'>Sisa Cuti</div></td>
                       <td width='100px'><div align='center'>DanKes</div></td>
                       <td width='100px'><div align='center'>Sisa DanKes</div></td>
                  </tr>
              </table>";
        }
    ?>
    </div>
    </form>
       <?php
            if($jumlah>0) {
                echo("<table width='99.6%' border='0' align='center' cellpadding='0' cellspacing='0' class='tbl_form'>
                    <tr class='head'>
                        <td><div align='left'>Data : $jumlah | <a target=_blank href=../penggajian/pages/pegawai/LaporanIndex.php?status=$status&keyword=$keyword> Laporan </a> | <a target=_blank href=../penggajian/pages/pegawai/LaporanIndexExcel.php?status=$status&keyword=$keyword>Excel</a> |</div></td>
                    </tr>
                 </table>");
             }
       ?>
    </div>
</div>
