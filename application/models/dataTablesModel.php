<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class datatablesmodel extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }


    function get_datatables($table, $where, $column_order, $column_search, $order) {
        $this->_get_datatables_query($table, $where, $column_order,$column_search,$order);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function _get_datatables_query($table, $where, $column_order,$column_search,$order) {
          $this->db->from($table);
		      $this->db->where($where);
        $i = 0;
        foreach ($column_search as $item) {
            if(!empty($_POST['search']['value'])) {
                if($i===0) {
                    $this->db->group_start();
                    $this->db->like('LOWER(' .$item. ')', strtolower($_POST['search']['value']));
                }
                else {
                   $this->db->or_like('LOWER(' .$item. ')', strtolower($_POST['search']['value']));
                }

                if(count($column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if(isset($_POST['order'])){
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($order)) {
            $order = $order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function count_filtered($table, $where, $column_order, $column_search, $order) {
        $this->_get_datatables_query($table, $where, $column_order, $column_search, $order);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($table, $where) {
        $this->db->from($table);
		$this->db->where($where);
        return $this->db->count_all_results();
    }





}
