<?php
class report_lb
{
    public $CI;

    public $TYPE = [
        1 => "ออเดอร์หน้าร้าน",
        2 => "ออเดอร์ออนไลน์",
    ];

    public $PAY_TYPE = [
        1 => "เงินสด",
        2 => "เงินโอน",
    ];

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->database();
        $this->CI->load->model('tbl_order_model');
        $this->CI->load->model('tbl_order_detail_model');
        $this->CI->load->model('tbl_product_model');
    }

    public function get_datesoteThai($strDate)
    {
        $strYear = date("Y", strtotime($strDate)) + 543;
        $strDay = date("j", strtotime($strDate));
        $strMonth = date("n", strtotime($strDate));
        $strHour = date("H", strtotime($strDate));
        $strMinute = date("i", strtotime($strDate));
        $strSeconds = date("s", strtotime($strDate));
        $strMonthCut = array("", "ม.ค", "ก.พ", "มี.ค", "เม.ย", "พ.ค", "มิ.ย", "ก.ค", "ส.ค", "ก.ย", "ต.ค", "พ.ย", "ธ.ค");
        $strMonthThai = $strMonthCut[$strMonth];
        return "$strDay $strMonthThai $strYear";
    }

    public function _get_report_date()
    {
        $post = $this->CI->input->post();

        $date_start = $post['date_start'] . " 00:00:00";
        $date_end = $post['date_end'] . " 23:59:59";

        $rec_data = $this->CI->tbl_order_model->report_date($date_start, $date_end);

        $html = "";
        if (!empty($rec_data)) {

            $i = 1;
            $tt_total = 0;
            $tt_discount = 0;
            $tt_net = 0;
            $tt_cost = 0;

            foreach ($rec_data as $key => $value) {

                $order_no = $value["order_no"];
                $order_type = $this->TYPE[$value["order_type"]];
                $pay_type = $this->PAY_TYPE[$value["pay_type"]];
                $user_order = isset($value["user_order"]) ? $this->CI->tbl_order_model->get_person($value["user_order"]) : "-";
                $customer_order = isset($value["customer_order"]) ? $this->CI->tbl_order_model->get_person($value["customer_order"]) : "-";
                $date_order = $this->get_datesoteThai($value["date_order"]);
                $total_order = $value["total_order"];
                $discount_order = $value["discount_order"];
                $total = number_format($total_order - $discount_order, 2);

                $data_cost_order = $this->CI->tbl_order_detail_model->get_cost_order($value["order_no"]);

                $cost_order = 0;
                foreach ($data_cost_order as $key => $value) {
                    $cost_order += $value['num_product'] * $value['cost_product'];
                }

                $tt_cost += $cost_order;
                $tt_total += $total_order;
                $tt_discount += $discount_order;
                $tt_net += ($total_order - $discount_order);

                $html .= "<tr><td style='text-align: center;'>{$i}</td><td>{$order_no}</td><td>{$order_type}</td><td>{$pay_type}</td><td>{$date_order}</td><td>{$customer_order}</td><td>{$user_order}</td><td>" . number_format($cost_order, 2) . "</td><td>{$total_order}</td><td>{$discount_order}</td><td>{$total}</td></tr>";
                $i++;
            }

            $html .= "<tr><td colspan='6'></td><td style='text-align: center;'><b>รวม</b></td><td><b>" . number_format($tt_cost, 2) . "</b></td>><td><b>" . number_format($tt_total, 2) . "</b></td><td><b>" . number_format($tt_discount, 2) . "</b></td><td><b>" . number_format($tt_net, 2) . "</b></td></tr>";
            $html .= "<tr><td colspan='6'></td><td style='text-align: center;'><b>กำไรสุทธิ</b></td><td colspan='4'><b>" . number_format($tt_net - $tt_cost, 2) . "</b></td></tr>";
        } else {
            $html = "<tr><td colspan='11' style='text-align: center;'>ไมพบข้อมูล</td></tr>";
        }

        echo json_encode(['html' => $html]);
    }

    public function _get_report_date_pdf($get)
    {

        $date_start = $get['date_start'] . " 00:00:00";
        $date_end = $get['date_end'] . " 23:59:59";

        $rec_data = $this->CI->tbl_order_model->report_date($date_start, $date_end);

        $html = "";
        $arr_data = [];
        if (!empty($rec_data)) {

            $i = 1;
            $tt_total = 0;
            $tt_discount = 0;
            $tt_net = 0;
            $tt_cost = 0;

            foreach ($rec_data as $key => $value) {

                $order_no = $value["order_no"];
                $order_type = $this->TYPE[$value["order_type"]];
                $pay_type = $this->PAY_TYPE[$value["pay_type"]];
                $user_order = isset($value["user_order"]) ? $this->CI->tbl_order_model->get_person($value["user_order"]) : "-";
                $customer_order = isset($value["customer_order"]) ? $this->CI->tbl_order_model->get_person($value["customer_order"]) : "-";
                $date_order = $this->get_datesoteThai($value["date_order"]);
                $total_order = $value["total_order"];
                $discount_order = $value["discount_order"];
                $total = number_format($total_order - $discount_order, 2);

                $data_cost_order = $this->CI->tbl_order_detail_model->get_cost_order($value["order_no"]);

                $cost_order = 0;
                foreach ($data_cost_order as $key => $value) {
                    $cost_order += $value['num_product'] * $value['cost_product'];
                }

                $tt_cost += $cost_order;
                $tt_total += $total_order;
                $tt_discount += $discount_order;
                $tt_net += ($total_order - $discount_order);

                array_push($arr_data, [$i, $order_no, $order_type, $pay_type, $date_order, $customer_order, $user_order, number_format($cost_order, 2), $total_order, $discount_order, $total]);

                $i++;
            }

            array_push($arr_data, ["", "", "", "", "", "", "รวม", number_format($tt_cost, 2), number_format($tt_total, 2), number_format($tt_discount, 2), number_format($tt_net, 2)]);
            array_push($arr_data, ["", "", "", "", "", "", "", "", "", "กำไรสุทธิ", number_format($tt_net - $tt_cost, 2)]);
        }

        return $arr_data;
    }

    public function _get_report_sale()
    {
        $post = $this->CI->input->post();

        $date_start = $post['date_start'] . " 00:00:00";
        $date_end = $post['date_end'] . " 23:59:59";

        $rec_data = $this->CI->tbl_order_model->report_sale($date_start, $date_end, $post['sale']);

        $html = "";
        if (!empty($rec_data)) {

            $i = 1;
            $tt_total = 0;
            $tt_discount = 0;
            $tt_net = 0;
            $tt_cost = 0;

            foreach ($rec_data as $key => $value) {

                $order_no = $value["order_no"];
                $order_type = $this->TYPE[$value["order_type"]];
                $pay_type = $this->PAY_TYPE[$value["pay_type"]];
                $user_order = isset($value["user_order"]) ? $this->CI->tbl_order_model->get_person($value["user_order"]) : "-";
                $customer_order = isset($value["customer_order"]) ? $this->CI->tbl_order_model->get_person($value["customer_order"]) : "-";
                $date_order = $this->get_datesoteThai($value["date_order"]);
                $total_order = $value["total_order"];
                $discount_order = $value["discount_order"];
                $total = number_format($total_order - $discount_order, 2);

                $data_cost_order = $this->CI->tbl_order_detail_model->get_cost_order($value["order_no"]);

                $cost_order = 0;
                foreach ($data_cost_order as $key => $value) {
                    $cost_order += $value['num_product'] * $value['cost_product'];
                }

                $tt_cost += $cost_order;
                $tt_total += $total_order;
                $tt_discount += $discount_order;
                $tt_net += ($total_order - $discount_order);

                $html .= "<tr><td style='text-align: center;'>{$i}</td><td>{$order_no}</td><td>{$order_type}</td><td>{$pay_type}</td><td>{$date_order}</td><td>{$customer_order}</td><td>{$user_order}</td><td>" . number_format($cost_order, 2) . "</td><td>{$total_order}</td><td>{$discount_order}</td><td>{$total}</td></tr>";
                $i++;
            }

            $html .= "<tr><td colspan='6'></td><td style='text-align: center;'><b>รวม</b></td><td><b>" . number_format($tt_cost, 2) . "</b></td>><td><b>" . number_format($tt_total, 2) . "</b></td><td><b>" . number_format($tt_discount, 2) . "</b></td><td><b>" . number_format($tt_net, 2) . "</b></td></tr>";
            $html .= "<tr><td colspan='6'></td><td style='text-align: center;'><b>กำไรสุทธิ</b></td><td colspan='4'><b>" . number_format($tt_net - $tt_cost, 2) . "</b></td></tr>";
        } else {
            $html = "<tr><td colspan='11' style='text-align: center;'>ไมพบข้อมูล</td></tr>";
        }

        echo json_encode(['html' => $html]);
    }

    public function _get_report_sale_pdf($get)
    {
        $date_start = $get['date_start'] . " 00:00:00";
        $date_end = $get['date_end'] . " 23:59:59";

        $rec_data = $this->CI->tbl_order_model->report_sale($date_start, $date_end, $get['sale']);

        $html = "";
        $arr_data = [];
        if (!empty($rec_data)) {

            $i = 1;
            $tt_total = 0;
            $tt_discount = 0;
            $tt_net = 0;
            $tt_cost = 0;

            foreach ($rec_data as $key => $value) {

                $order_no = $value["order_no"];
                $order_type = $this->TYPE[$value["order_type"]];
                $pay_type = $this->PAY_TYPE[$value["pay_type"]];
                $user_order = isset($value["user_order"]) ? $this->CI->tbl_order_model->get_person($value["user_order"]) : "-";
                $customer_order = isset($value["customer_order"]) ? $this->CI->tbl_order_model->get_person($value["customer_order"]) : "-";
                $date_order = $this->get_datesoteThai($value["date_order"]);
                $total_order = $value["total_order"];
                $discount_order = $value["discount_order"];
                $total = number_format($total_order - $discount_order, 2);

                $data_cost_order = $this->CI->tbl_order_detail_model->get_cost_order($value["order_no"]);

                $cost_order = 0;
                foreach ($data_cost_order as $key => $value) {
                    $cost_order += $value['num_product'] * $value['cost_product'];
                }

                $tt_cost += $cost_order;
                $tt_total += $total_order;
                $tt_discount += $discount_order;
                $tt_net += ($total_order - $discount_order);

                array_push($arr_data, [$i, $order_no, $order_type, $pay_type, $date_order, $customer_order, $user_order, number_format($cost_order, 2), $total_order, $discount_order, $total]);
                $i++;
            }

            array_push($arr_data, ["", "", "", "", "", "", "รวม", number_format($tt_cost, 2), number_format($tt_total, 2), number_format($tt_discount, 2), number_format($tt_net, 2)]);
            array_push($arr_data, ["", "", "", "", "", "", "", "", "", "กำไรสุทธิ", number_format($tt_net - $tt_cost, 2)]);
        }

        return $arr_data;
    }

    public function _get_report_customer()
    {
        $post = $this->CI->input->post();

        $date_start = $post['date_start'] . " 00:00:00";
        $date_end = $post['date_end'] . " 23:59:59";

        $rec_data = $this->CI->tbl_order_model->report_customer($date_start, $date_end, $post['customer']);

        $html = "";
        if (!empty($rec_data)) {

            $i = 1;
            $tt_total = 0;
            $tt_discount = 0;
            $tt_net = 0;
            $tt_cost = 0;

            foreach ($rec_data as $key => $value) {

                $order_no = $value["order_no"];
                $order_type = $this->TYPE[$value["order_type"]];
                $pay_type = $this->PAY_TYPE[$value["pay_type"]];
                $user_order = isset($value["user_order"]) ? $this->CI->tbl_order_model->get_person($value["user_order"]) : "-";
                $customer_order = isset($value["customer_order"]) ? $this->CI->tbl_order_model->get_person($value["customer_order"]) : "-";
                $date_order = $this->get_datesoteThai($value["date_order"]);
                $total_order = $value["total_order"];
                $discount_order = $value["discount_order"];
                $total = number_format($total_order - $discount_order, 2);

                $data_cost_order = $this->CI->tbl_order_detail_model->get_cost_order($value["order_no"]);

                $cost_order = 0;
                foreach ($data_cost_order as $key => $value) {
                    $cost_order += $value['num_product'] * $value['cost_product'];
                }

                $tt_cost += $cost_order;
                $tt_total += $total_order;
                $tt_discount += $discount_order;
                $tt_net += ($total_order - $discount_order);

                $html .= "<tr><td style='text-align: center;'>{$i}</td><td>{$order_no}</td><td>{$order_type}</td><td>{$pay_type}</td><td>{$date_order}</td><td>{$customer_order}</td><td>{$user_order}</td><td>" . number_format($cost_order, 2) . "</td><td>{$total_order}</td><td>{$discount_order}</td><td>{$total}</td></tr>";
                $i++;
            }

            $html .= "<tr><td colspan='6'></td><td style='text-align: center;'><b>รวม</b></td><td><b>" . number_format($tt_cost, 2) . "</b></td>><td><b>" . number_format($tt_total, 2) . "</b></td><td><b>" . number_format($tt_discount, 2) . "</b></td><td><b>" . number_format($tt_net, 2) . "</b></td></tr>";
            $html .= "<tr><td colspan='6'></td><td style='text-align: center;'><b>กำไรสุทธิ</b></td><td colspan='4'><b>" . number_format($tt_net - $tt_cost, 2) . "</b></td></tr>";
        } else {
            $html = "<tr><td colspan='11' style='text-align: center;'>ไมพบข้อมูล</td></tr>";
        }

        echo json_encode(['html' => $html]);
    }

    public function _get_report_customer_pdf($get)
    {
        $date_start = $get['date_start'] . " 00:00:00";
        $date_end = $get['date_end'] . " 23:59:59";

        $rec_data = $this->CI->tbl_order_model->report_customer($date_start, $date_end, $get['customer']);

        $html = "";
        $arr_data = [];
        if (!empty($rec_data)) {

            $i = 1;
            $tt_total = 0;
            $tt_discount = 0;
            $tt_net = 0;
            $tt_cost = 0;

            foreach ($rec_data as $key => $value) {

                $order_no = $value["order_no"];
                $order_type = $this->TYPE[$value["order_type"]];
                $pay_type = $this->PAY_TYPE[$value["pay_type"]];
                $user_order = isset($value["user_order"]) ? $this->CI->tbl_order_model->get_person($value["user_order"]) : "-";
                $customer_order = isset($value["customer_order"]) ? $this->CI->tbl_order_model->get_person($value["customer_order"]) : "-";
                $date_order = $this->get_datesoteThai($value["date_order"]);
                $total_order = $value["total_order"];
                $discount_order = $value["discount_order"];
                $total = number_format($total_order - $discount_order, 2);

                $data_cost_order = $this->CI->tbl_order_detail_model->get_cost_order($value["order_no"]);

                $cost_order = 0;
                foreach ($data_cost_order as $key => $value) {
                    $cost_order += $value['num_product'] * $value['cost_product'];
                }

                $tt_cost += $cost_order;
                $tt_total += $total_order;
                $tt_discount += $discount_order;
                $tt_net += ($total_order - $discount_order);

                array_push($arr_data, [$i, $order_no, $order_type, $pay_type, $date_order, $customer_order, $user_order, number_format($cost_order, 2), $total_order, $discount_order, $total]);
                $i++;
            }

            array_push($arr_data, ["", "", "", "", "", "", "รวม", number_format($tt_cost, 2), number_format($tt_total, 2), number_format($tt_discount, 2), number_format($tt_net, 2)]);
            array_push($arr_data, ["", "", "", "", "", "", "", "", "", "กำไรสุทธิ", number_format($tt_net - $tt_cost, 2)]);
        }

        return $arr_data;
    }

    public function _get_report_minstock()
    {
        $rec_data = $this->CI->tbl_product_model->get_product_all();

        $html = "";
        if (!empty($rec_data)) {
            $i = 1;

            foreach ($rec_data as $key => $value) {

                $html .= "<tr><td style='text-align: center;'>{$i}</td><td>{$value['product_code']}</td><td>{$value['name_product']}</td><td>{$value['name_type']}</td><td>{$value['num']}</td><td>{$value['minstock']}</td><td>{$value['cost']}</td><td>{$value['price']}</td><td>{$value['unit']}</td></tr>";
                $i++;
            }

        } else {
            $html = "<tr><td colspan='11' style='text-align: center;'>ไมพบข้อมูล</td></tr>";
        }

        echo json_encode(['html' => $html]);
    }

    public function _get_report_minstock_pdf()
    {
        $rec_data = $this->CI->tbl_product_model->get_product_all();

        $html = "";
        $arr_data = [];
        if (!empty($rec_data)) {
            $i = 1;

            foreach ($rec_data as $key => $value) {

                array_push($arr_data, [$i, $value['product_code'], $value['name_product'], $value['name_type'], $value['num'], $value['minstock'], $value['cost'], $value['price'], $value['unit']]);

                $i++;
            }

        }

        return $arr_data;
    }

    public function _get_report_product_exp()
    {
        $rec_data = $this->CI->tbl_product_model->get_product_exp();

        $date_now = date("Y-m-d");

        $html = "";
        if (!empty($rec_data)) {
            $i = 1;

            $bg_color = "";
            $text_noti = "";

            foreach ($rec_data as $key => $value) {

                if ($value['date_exp'] <= $date_now) {
                    $text_noti = "***";
                    $bg_color = "#eb6e6e";
                } else {
                    $text_noti = "";
                    $bg_color = "#ebdc6e";
                }

                $date = new DateTime($value['date_exp']);

                $formattedDate = $date->format('d-m-Y');

                $html .= "<tr><td style='text-align: center;'>{$i}</td><td>{$value['product_code']}</td><td>{$value['name_product']}</td><td>{$value['name_type']}</td><td>{$value['num']}</td><td style='background-color: {$bg_color};'>{$formattedDate}{$text_noti}</td><td>{$value['unit']}</td></tr>";
                $i++;
            }

        } else {
            $html = "<tr><td colspan='11' style='text-align: center;'>ไมพบข้อมูล</td></tr>";
        }

        echo json_encode(['html' => $html]);
    }

    public function _get_report_product_exp_pdf()
    {
        $rec_data = $this->CI->tbl_product_model->get_product_exp();

        $date_now = date("Y-m-d");

        $arr_data = [];
        if (!empty($rec_data)) {
            $i = 1;

            $bg_color = "";
            $text_noti = "";

            foreach ($rec_data as $key => $value) {

                if ($value['date_exp'] <= $date_now) {
                    $text_noti = "***";
                    $bg_color = "#eb6e6e";
                } else {
                    $text_noti = "";
                    $bg_color = "#ebdc6e";
                }

                $date = new DateTime($value['date_exp']);

                $formattedDate = $date->format('d-m-Y');

                array_push($arr_data, [$i, $value['product_code'], $value['name_product'], $value['name_type'], $value['num'], $formattedDate . $text_noti, $value['unit']]);

                $i++;
            }

        }

        return $arr_data;
    }

}
