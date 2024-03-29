<?php
class tbl_product_model extends CI_Model
{

    public $tableName;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->tableName = 'tbl_product';
    }

    public function get_product_wheretype($type, $order, $search)
    {
        $temp = array();

        if ($type != '0' && $type != '999') {
            $this->db->where("tbl_type_product.code_type", $type);
        }

        if ($search != "") {
            $this->db->like('tbl_product.name_product', $search);
        }

        switch ($order) {
            case '1':
                $this->db->order_by("tbl_product.price", "ASC");
                break;

            case '2':
                $this->db->order_by("tbl_product.price", "DESC");
                break;

            default:
                break;
        }

        if ($type != '999') {
            $this->db->where("tbl_product.status_product <>", 2);
        } elseif ($type == '999') {
            $this->db->where("tbl_product.status_product", 3);
        }

        $this->db->from($this->tableName);
        $this->db->join('tbl_type_product', 'tbl_product.code_type = tbl_type_product.code_type', 'left');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return $temp;
        }
    }

    public function get_product_wherecode($code)
    {
        $temp = array();

        $this->db->where("product_code", $code);
        $this->db->from($this->tableName);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_object();
        } else {
            return $temp;
        }
    }

    public function update_data($id, $data)
    {
        $this->db->where('product_code', $id);
        $this->db->update($this->tableName, $data);
    }

    public function check_product_barcode($code)
    {
        $this->db->where("product_code", $code);
        $this->db->from($this->tableName);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
