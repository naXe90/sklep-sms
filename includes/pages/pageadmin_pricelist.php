<?php

use Admin\Table\BodyRow;
use Admin\Table\Cell;
use Admin\Table\Input;
use Admin\Table\Structure;
use Admin\Table\Wrapper;

class PageAdminPriceList extends PageAdmin implements IPageAdmin_ActionBox
{
    const PAGE_ID = 'pricelist';
    protected $privilage = 'manage_settings';

    public function __construct()
    {
        parent::__construct();

        $this->heart->page_title = $this->title = $this->lang->translate('pricelist');
    }

    protected function content($get, $post)
    {
        $wrapper = new Wrapper();
        $wrapper->setTitle($this->title);

        $table = new Structure();

        $cell = new Cell($this->lang->translate('id'));
        $cell->setParam('headers', 'id');
        $table->addHeadCell($cell);

        $table->addHeadCell(new Cell($this->lang->translate('service')));
        $table->addHeadCell(new Cell($this->lang->translate('tariff')));
        $table->addHeadCell(new Cell($this->lang->translate('amount')));
        $table->addHeadCell(new Cell($this->lang->translate('server')));

        $result = $this->db->query(
            "SELECT SQL_CALC_FOUND_ROWS * " .
            "FROM `" . TABLE_PREFIX . "pricelist` " .
            "ORDER BY `service`, `server`, `tariff` " .
            "LIMIT " . get_row_limit($this->currentPage->getPageNumber())
        );

        $table->setDbRowsAmount($this->db->get_column("SELECT FOUND_ROWS()", "FOUND_ROWS()"));

        while ($row = $this->db->fetch_array_assoc($result)) {
            $body_row = new BodyRow();

            $service = $this->heart->get_service($row['service']);

            if ($row['server'] != -1) {
                $temp_server = $this->heart->get_server($row['server']);
                $server_name = $temp_server['name'];
                unset($temp_server);
            } else {
                $server_name = $this->lang->translate('all_servers');
            }

            $body_row->setDbId($row['id']);
            $body_row->addCell(new Cell("{$service['name']} ( {$service['id']} )"));
            $body_row->addCell(new Cell($row['tariff']));
            $body_row->addCell(new Cell($row['amount']));
            $body_row->addCell(new Cell($server_name));

            $body_row->setButtonDelete(true);
            $body_row->setButtonEdit(true);

            $table->addBodyRow($body_row);
        }

        $wrapper->setTable($table);

        $button = new Input();
        $button->setParam('id', 'price_button_add');
        $button->setParam('type', 'button');
        $button->setParam('value', $this->lang->translate('add_price'));
        $wrapper->addButton($button);

        return $wrapper->toHtml();
    }

    public function get_action_box($box_id, $data)
    {
        if (!get_privilages("manage_settings")) {
            return [
                'status' => "not_logged_in",
                'text'   => $this->lang->translate('not_logged_or_no_perm'),
            ];
        }

        if ($box_id == "price_edit") {
            $result = $this->db->query($this->db->prepare(
                "SELECT * FROM `" . TABLE_PREFIX . "pricelist` " .
                "WHERE `id` = '%d'",
                [$data['id']]
            ));
            $price = $this->db->fetch_array_assoc($result);

            $all_servers = $price['server'] == -1 ? "selected" : "";
        }

        // Pobranie usług
        $services = "";
        foreach ($this->heart->get_services() as $service_id => $service) {
            $services .= create_dom_element("option", $service['name'] . " ( " . $service['id'] . " )", [
                'value'    => $service['id'],
                'selected' => isset($price) && $price['service'] == $service['id'] ? "selected" : "",
            ]);
        }

        // Pobranie serwerów
        $servers = "";
        foreach ($this->heart->get_servers() as $server_id => $server) {
            $servers .= create_dom_element("option", $server['name'], [
                'value'    => $server['id'],
                'selected' => isset($price) && $price['server'] == $server['id'] ? "selected" : "",
            ]);
        }

        // Pobranie taryf
        $tariffs = "";
        foreach ($this->heart->getTariffs() as $tariff) {
            $tariffs .= create_dom_element("option", $tariff->getId(), [
                'value'    => $tariff->getId(),
                'selected' => isset($price) && $price['tariff'] == $tariff->getId() ? "selected" : "",
            ]);
        }

        switch ($box_id) {
            case "price_add":
                $output = $this->template->render(
                    "admin/action_boxes/price_add",
                    compact('services', 'servers', 'tariffs')
                );
                break;

            case "price_edit":
                $output = $this->template->render(
                    "admin/action_boxes/price_edit",
                    compact('services', 'servers', 'tariffs', 'price', 'all_servers')
                );
                break;

            default:
                $output = '';
        }

        return [
            'status'   => 'ok',
            'template' => $output,
        ];
    }
}