<?php
class Modelapp extends CI_Model {
    function __database($query){
            $db_data = $this->db->query($query);
            if($db_data->num_rows() > 0){
                    foreach($db_data->result() as $row){$data[] = $row;	}
                    return $data; }
    }

    function get_login($username,$userpass){
		$sql    = " SELECT	peg_id,
							peg_nama,
							peg_aktif
					FROM	psdm.pegawai
					WHERE	peg_aktif IS TRUE AND
							peg_username='".$username."' AND
							peg_passwd = crypt('".$userpass."',\"peg_passwd\")";
		$q      = $this->db->query($sql);
		return $q;
    }


    function get_grup_menu($peg_id) {
        $sql    = " SELECT	ref_mu_id,
							ref_grp_mn_id,
							ref_grp_mn_ket
                    FROM	v_menu_gimpis
                    WHERE	ref_mu_user_id=".$peg_id."
                    GROUP BY ref_mu_id,ref_grp_mn_id,ref_grp_mn_ket,ref_mu_urut
                    ORDER BY ref_mu_urut";
		$q      = $this->db->query($sql);
        return $q;
    }

    function get_opt_menu($grup_id) {
        $sql    = " SELECT	ref_menu_id,
							ref_menu_judul,
							ref_menu_option
                    FROM	v_menu_gimpis
                    WHERE	ref_mg_grup_id=".$grup_id."
                    GROUP BY ref_menu_id,ref_menu_judul,ref_menu_option,ref_mg_urut
                    ORDER BY ref_mg_urut";
		$q      = $this->db->query($sql);
        return $q;
    }

    function show_menu($peg_id){
        $grup_menu	= $this->get_grup_menu($peg_id);
        $menu	= '';
        foreach($grup_menu->result() as $row_grup){
            $grup_id	= $row_grup->ref_grp_mn_id;
            $grup_ket	= $row_grup->ref_grp_mn_ket;
            $opt_menu	= $this->get_opt_menu($grup_id);
            $menu	.= ' <li><a><i class="fa fa-edit"></i> '.$grup_ket.' <span class="fa fa-chevron-down"></span></a>';
            $menu	.=' <ul class="nav child_menu">';
            foreach($opt_menu->result() as $row_opt){
                $menu_ket	= $row_opt->ref_menu_judul;
                $menu_url	= base_url().$row_opt->ref_menu_option;
                $menu   .= '<li><a href="'.$menu_url.'">'.$menu_ket.'</a></li>';
            }
            $menu   .= '</ul>';
            $menu	.= '</li>';
        }
        return $menu;
    }

    function get_opt_menu_list($opt_menu, $peg_id) {
        $sql        = " SELECT	ref_menu_id,
                                ref_menu_judul,
                                ref_grp_mn_ket
                        FROM	v_menu_gimpis
                        WHERE	ref_mu_user_id=".$peg_id." AND
                                ref_menu_option='".$opt_menu."'";
        $query      = $this->db->query($sql);
        $row        = $query->row();
        return $row;
    }


    function insert_table($table, $data) {
        $this->db->insert($table, $data);
    }

    function update_table($table, $data, $cond) {
		$this->db->where($cond);
		$this->db->update($table, $data);
    }

    function delete_table($table, $cond) {
		$this->db->where($cond);
		$this->db->delete($table);
    }


    function get_kode_rekening() {
        $sql    = " SELECT	kd_rkng_id,
							kd_rkng_kode,
							kd_rkng_ket
					FROM	kode_rekening
					WHERE	kd_rkng_aktif IS TRUE
					ORDER BY kd_rkng_kode";
		$q      = $this->db->query($sql);
        return $q;
    }

    function show_kode_rekening() {
		$coa	= $this->get_kode_rekening();
        $list	= '';
        foreach($coa->result() as $row_coa){
            $coa_id		= $row_coa->kd_rkng_id;
            $coa_kode	= $row_coa->kd_rkng_kode;
            $coa_ket	= $row_coa->kd_rkng_ket;
            $list	.= '<option value="'.$coa_id.'">'.$coa_kode.'&nbsp;-&nbsp;'.$coa_ket.'</option>';
        }
        return $list;
    }

    function show_combo($table, $fieldId, $fieldName, $clause, $fieldOrder, $value) {
		$list	= '';
        $sql    = " SELECT	".$fieldId.",
							".$fieldName."
					FROM	".$table."
					WHERE	".$clause."
					ORDER BY ".$fieldOrder;
		$rhQ      = $this->db->query($sql);
        foreach($rhQ->result() as $rrQ){
            $field_id		= $rrQ->$fieldId;
            $field_name	= $rrQ->$fieldName;
			($value == $field_id) ? $selected = "selected" : $selected = "";
            $list	.= '<option value="'.$field_id.'" '.$selected.'>'.$field_name.'</option>';
        }
        return $list;
    }

    function get_stok($dist, $prod_id) {
		$dbDist = $this->load->database($dist, TRUE);
        $sql    = " SELECT	COALESCE(SUM(gdng_qty),0) AS stok
                    FROM	gudang
                    WHERE	gdng_qty >= 0 AND
							gdng_prod_id=".$prod_id;
		$q      = $dbDist->query($sql);
		foreach($q->result() as $rrQ){
            $stok		= $rrQ->stok;
        }
        return $stok;
    }

    function getSequences($fieldNextval) {
		$sql	= "	SELECT nextval('" . $fieldNextval . "') AS seq_id";
		$rhQ	= $this->db->query($sql);
		$ret	= $rhQ->row();
		$nextId	= $ret->seq_id;
		return $nextId;

    }

    function getNextNumber($kode, $bulan, $tahun) {
        $sql	= "	SELECT  MAX(rgn_no_urut) AS nomor
					FROM    ref_generator_nomor
					WHERE   rgn_kode	= '".$kode."' AND
							rgn_bulan	= '".$bulan."' AND
							rgn_tahun	= '".$tahun."'";
		$rhQ	= $this->db->query($sql);
		$ret	= $rhQ->row();
        $nextNo	= (int)$ret->nomor + 1;
		return $nextNo;

    }

    /* =================================== FUNCTION GET NOMOR FAKTUR ===================================== */
	function generateNomorFaktur($kode, $periode, $nextNo){
        $muchChar	= strlen($nextNo);
        if ($muchChar == 1) {
            $newNumber	= $kode .'-'. $periode . '000' . $nextNo;
        }
        elseif ($muchChar == 2) {
            $newNumber	= $kode .'-'. $periode . '00' . $nextNo;
        }
        elseif ($muchChar == 3) {
            $newNumber	= $kode .'-'. $periode . '0' . $nextNo;
        }
        elseif ($muchChar == 4) {
            $newNumber	= $kode .'-'. $periode . $nextNo;
        }
        else {
            $muchChar	= $kode .'-'. $periode . $nextNo;
        }
        return $newNumber;
	}
/* ------------------------------------------------------ */
    function m_newsticker($num,$offset){
            $db_data = $this->db->query('select ticker_id, ticker_text, ticker_date from tbl_ticker order by ticker_date desc limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_news($num,$offset){
            $db_data = $this->db->query('select news_id, news_title, news_writer, news_date, cat_name from tbl_news as a left join tbl_category as b on b.cat_id=a.cat_id order by news_date desc limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_clublist($num,$offset){
            $db_data = $this->db->query('select club_id, club_name, club_logo, stad_name from tbl_club as a left join tbl_stadium as b on b.stad_id=a.stad_id order by club_name asc limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_playerlist($query){
            $db_data = $this->db->query('select '.$query.' from tbl_players as a left join tbl_club as b on b.club_id=a.club_id order by player_no asc');
            return $db_data;
    }
    function m_stadiumlist($num,$offset){
            $db_data = $this->db->query('select * from tbl_stadium order by stad_name asc limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_userlist($num,$offset,$query=''){
            $db_data = $this->db->query('select * from tbl_user '.$query.' order by user_level desc, user_status desc, user_nickname asc limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_complist($num,$offset){
            $db_data = $this->db->query('select * from tbl_competition order by comp_id asc limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_event($num,$offset){
            $db_data = $this->db->query('select * from tbl_event limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_galcat($num,$offset){
            $db_data = $this->db->query('select * from tbl_galcat limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_gallery($num,$offset,$galid){
            $db_data = $this->db->query('select * from tbl_gallery where galcat_id=\''.$galid.'\' limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_match($num,$offset,$query){
            $db_data = $this->db->query('select a.match_id, b.club_id as club_homeid, b.club_name as club_home, c.club_id as club_awayid, c.club_name as club_away, match_homegoal, match_awaygoal, match_date, match_stage, d.comp_name
from tbl_match as a left join tbl_club as b on b.club_id=a.match_home left join tbl_club as c on c.club_id=a.match_away left join tbl_competition as d on d.comp_id=a.comp_id
'.$query.' order by match_date desc limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_stats($num,$offset,$query){
            $db_data = $this->db->query('select a.match_id, b.club_id as club_homeid, b.club_name as club_home, c.club_id as club_awayid, c.club_name as club_away, match_homegoal, match_awaygoal, match_date, match_stage, d.comp_name,
ml_id,(select count(ms_id) from tbl_matchstats as f where f.match_id=a.match_id) as match_stats,(select count(mp_id) from tbl_matchplayer as g where g.match_id=a.match_id) as player_stats
from tbl_match as a left join tbl_club as b on b.club_id=a.match_home left join tbl_club as c on c.club_id=a.match_away left join tbl_competition as d on d.comp_id=a.comp_id left join tbl_matchlineup as e on e.match_id=a.match_id
'.$query.' order by match_date desc limit '.$num.','.$offset.'');
            return $db_data;
    }
    function m_matchplayer($query){
            $db_data = $this->db->query('select * from tbl_players as a left join tbl_matchplayer as b on (a.player_id=b.players_id and b.match_id='.$query.') order by player_no asc');
            return $db_data;
    }
    function m_category(){
            $db_data = $this->db->query('select * from tbl_category');
            return $db_data;
    }
    function m_club($query){
            $db_data = $this->db->query('select club_id, club_name from tbl_club '.$query.'');
            return $db_data;
    }
    function m_stadium(){
            $db_data = $this->db->query('select stad_id, stad_name from tbl_stadium order by stad_name asc');
            return $db_data;
    }
    function m_seriea($stage){
            $db_data = $this->db->query('select b.club_name as club_home, c.club_name as club_away, match_homegoal, match_awaygoal, match_date, match_stage
from tbl_match as a left join tbl_club as b on b.club_id=a.match_home left join tbl_club as c on c.club_id=a.match_away
where comp_id=\'1\' and match_stage=\''.$stage.'\'');
            return $db_data;
    }
    function m_lineup($query){
            $db_data = $this->db->query('select * from tbl_matchlineup '.$query.'');
            return $db_data;
    }
    function m_matchstat($query){
            $db_data = $this->db->query('select * from tbl_matchstats '.$query.'');
            if($db_data->num_rows() > 0){
                    foreach($db_data->result() as $row){
                            $data[$row->club_id][]= $row;
                    }
                    return $data;
            }

    }
    function m_uphomeclass($compid,$clubid,$query,$gf,$ga){
            $db_data = $this->db->query('update tbl_classement SET
'.$query.',
cls_homegf=cls_homegf+'.$gf.',
cls_homega=cls_homega+'.$ga.'
WHERE club_id='.$clubid.' and comp_id='.$compid.'');
            return $db_data;
    }
    function m_upawayclass($compid,$clubid,$query,$gf,$ga){
            $db_data = $this->db->query('update tbl_classement SET
'.$query.',
cls_awaygf=cls_awaygf+'.$gf.',
cls_awayga=cls_awayga+'.$ga.'
WHERE club_id='.$clubid.' and comp_id='.$compid.'');
            return $db_data;
    }

    function m_cek($id,$table){
            $db_data = $this->db->query('select '.$id.' from '.$table.'');
            return $db_data->num_rows;
    }
    function m_data($field,$query){
            $db_data = $this->db->query('select '.$field.' from '.$query.'');
            return $db_data;
    }

    function save($table,$data)
    {
            if($this->db->insert($table, $data)):
                    return $this->db->insert_id();
            else:
                    return false;
            endif;
    }

    function update($table,$data,$primarykey = array())
    {
            if(count($primarykey) > 0) {
                    if($this->db->update($table, $data, $primarykey)) $success = TRUE;
            }else{
                    if($this->db->update($table, $data)) $success = TRUE;
            }
            if($success):
                    return $this->db->affected_rows();
            else:
                    return false;
            endif;
    }

}
?>
