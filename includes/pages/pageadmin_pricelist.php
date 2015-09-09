<?php

use Admin\Table;
use Admin\Table\Wrapper;
use Admin\Table\Structure;
use Admin\Table\BodyRow;
use Admin\Table\Cell;
use Admin\Table\Input;

$heart->register_page("pricelist", "PageAdminPriceList", "admin");

class PageAdminPriceList extends PageAdmin implements IPageAdmin_ActionBox
{

	const PAGE_ID = "pricelist";
	protected $privilage = "manage_settings";

	function __construct()
	{
		global $lang;
		$this->title = $lang->pricelist;

		parent::__construct();
	}

	protected function content($get, $post)
	{
		global $heart, $db, $lang, $G_PAGE;

		$wrapper = new Wrapper();
		$wrapper->setTitle($this->title);

		$table = new Structure();

		$cell = new Cell($lang->id);
		$cell->setParam('headers', 'id');
		$table->addHeadCell($cell);

		$table->addHeadCell(new Cell($lang->service));
		$table->addHeadCell(new Cell($lang->tariff));
		$table->addHeadCell(new Cell($lang->amount));
		$table->addHeadCell(new Cell($lang->server));

		$result = $db->query(
			"SELECT SQL_CALC_FOUND_ROWS * " .
			"FROM `" . TABLE_PREFIX . "pricelist` " .
			"ORDER BY `service`, `server`, `tariff` " .
			"LIMIT " . get_row_limit($G_PAGE)
		);

		$table->setDbRowsAmount($db->get_column("SELECT FOUND_ROWS()", "FOUND_ROWS()"));

		while ($row = $db->fetch_array_assoc($result)) {
			$body_row = new BodyRow();

			$service = $heart->get_service($row['service']);

			if ($row['server'] != -1) {
				$temp_server = $heart->get_server($row['server']);
				$server_name = $temp_server['name'];
				unset($temp_server);
			} else {
				$server_name = $lang->all_servers;
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
		$button->setParam('value', $lang->add_price);
		$wrapper->addButton($button);

		return $wrapper->toHtml();
	}

	public function get_action_box($box_id, $data)
	{
		global $heart, $db, $lang, $templates;

		if (!get_privilages("manage_settings"))
			return array(
				'status' => "not_logged_in",
				'text' => $lang->not_logged_or_no_perm
			);

		if ($box_id == "price_edit") {
			$result = $db->query($db->prepare(
				"SELECT * FROM `" . TABLE_PREFIX . "pricelist` " .
				"WHERE `id` = '%d'",
				array($data['id'])
			));
			$price = $db->fetch_array_assoc($result);

			$all_servers = $price['server'] == -1 ? "selected" : "";
		}

		// Pobranie usług
		$services = "";
		foreach ($heart->get_services() as $service_id => $service)
			$services .= create_dom_element("option", $service['name'] . " ( " . $service['id'] . " )", array(
				'value' => $service['id'],
				'selected' => isset($price) && $price['service'] == $service['id'] ? "selected" : ""
			));

		// Pobranie serwerów
		$servers = "";
		foreach ($heart->get_servers() as $server_id => $server)
			$servers .= create_dom_element("option", $server['name'], array(
				'value' => $server['id'],
				'selected' => isset($price) && $price['server'] == $server['id'] ? "selected" : ""
			));

		// Pobranie taryf
		$tariffs = "";
		foreach ($heart->getTariffs() as $tariff)
			$tariffs .= create_dom_element("option", $tariff->getId(), array(
				'value' => $tariff->getId(),
				'selected' => isset($price) && $price['tariff'] == $tariff->getId() ? "selected" : ""
			));

		switch ($box_id) {
			case "price_add":
				$output = eval($templates->render("admin/action_boxes/price_add"));
				break;

			case "price_edit":
				$output = eval($templates->render("admin/action_boxes/price_edit"));
				break;
		}

		return array(
			'status' => 'ok',
			'template' => $output
		);
	}

}