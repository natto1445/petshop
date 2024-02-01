<?php
class product_lb
{
    public $CI;

    public $TYPE = [
        1 => "ใช้งาน",
        2 => "ไม่ใช้งาน",
    ];

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->database();
        $this->CI->load->model('tbl_type_product_model');
    }

    public function _ajax_load_type()
    {
        $rec_type = $this->CI->tbl_type_product_model->get_type_all();

        if (!empty($rec_type)) {
            $html = '';
            $i = 1;
            foreach ($rec_type as $data) {

                $id = $data['id'];
                $html .= '<tr>';
                $html .= '<td>' . $i . '</td>';
                $html .= '<td>' . $data['code_type'] . '</td>';
                $html .= '<td>' . $data['name_type'] . '</td>';
                $html .= '<td>' . date("Y/m/d", strtotime($data['date_create'])) . '</td>';
                $html .= '<td>' . $this->TYPE[$data['status']] . '</td>';
                $html .= '<td>' . $data['user_create'] . '</td>';
                $html .= '<td>' . $data['user_edit'] . '</td>';
                $html .= "<td>
                    <div class='dropdown'>

                    <a class='btn btn-secondary dropdown-toggle' href='#' role='button' id='dropdownMenuLink' data-bs-toggle='dropdown' aria-expanded='false'>
                        จัดการ
                    </a>

                    <ul class='dropdown-menu' aria-labelledby='dropdownMenuLink'>
                        <li><a class='dropdown-item' data-id_type='" . $data['id'] . "' data-code_type='" . $data['code_type'] . "' data-name_type='" . $data['name_type'] . "' data-status_type='" . $data['status'] . "' data-toggle='modal' data-target='#editType' onclick='editFunction(this)'>แก้ไข</a></li>
                        <li><a class='dropdown-item' onclick='deleteFunction($id)'>ลบ</a></li>
                    </ul>

                    </div>
                </td>";
                $html .= '</tr>';
                $i++;
            }
        } else {
            $html = '';
        }
        echo 'val^' . $html;
    }

    public function _add_type()
    {
        $name_type = $this->CI->input->post('name_type');

        $max_id = $this->CI->tbl_type_product_model->get_max_data();
        $newNumber = $max_id + 1;
        $new_id = "T" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        $data = array(
            "code_type" => $new_id,
            "name_type" => $name_type,
            "status" => 1,
            "date_create" => date("Y-m-d H:i:s"),
            "date_edit" => "",
            "user_create" => $_SESSION['usr_id'],
            "user_edit" => "",
        );

        $this->CI->tbl_type_product_model->insert_data($data);

        echo json_encode(['save' => true]);
    }

    public function _edit_type()
    {
        $Eid_type = $this->CI->input->post('Eid_type');
        $Ename_type = $this->CI->input->post('Ename_type');
        $Estatus_type = $this->CI->input->post('Estatus_type');

        $data = array(
            "name_type" => $Ename_type,
            "status" => $Estatus_type,
            "date_edit" => date("Y-m-d H:i:s"),
            "user_edit" => $_SESSION['usr_id'],
        );

        $this->CI->tbl_type_product_model->update_data($Eid_type, $data);

        echo json_encode(['update' => true]);
    }

    public function _del_type()
    {
        $type_id = $this->CI->input->post('type_id');

        $del = $this->CI->tbl_type_product_model->delete_data($type_id);

        if ($del) {
            echo json_encode(['del' => true]);
        }
    }

}