<?php

$heart->register_page("payment_service_code", "PageAdminPaymentServiceCode", "admin");

class PageAdminPaymentServiceCode extends PageAdmin
{

	const PAGE_ID = "payment_service_code";

	function __construct()
	{
		global $lang;
		$this->title = $lang->payments_service_code;

		parent::__construct();
	}

	protected function content($get, $post)
	{
		global $db, $settings, $lang, $G_PAGE, $templates;

		$where = "";
		if (isset($get['payid']))
			$where .= $db->prepare(" AND `payment_id` = '%d' ", array($get['payid']));

		$result = $db->query(
			"SELECT SQL_CALC_FOUND_ROWS * " .
			"FROM ({$settings['transactions_query']}) as t " .
			"WHERE t.payment = 'service_code' " . $where .
			"ORDER BY t.timestamp DESC " .
			"LIMIT " . get_row_limit($G_PAGE)
		);
		$rows_count = $db->get_column("SELECT FOUND_ROWS()", "FOUND_ROWS()");

		$tbody = "";
		while ($row = $db->fetch_array_assoc($result)) {
			$row['cost'] = $row['cost'] ? number_format($row['cost'] / 100.0, 2) . " " . $settings['currency'] : "";

			// Podświetlenie konkretnej płatności
			if ($get['highlight'] && $get['payid'] == $row['payment_id'])
				$row['class'] = "highlighted";

			$row['platform'] = get_platform($row['platform']);

			// Poprawienie timestampa
			$row['timestamp'] = convertDate($row['timestamp']);

			// Pobranie danych do tabeli
			$tbody .= eval($templates->render("admin/payment_service_code_trow"));
		}

		// Nie ma zadnych danych do wyswietlenia
		if (!strlen($tbody))
			$tbody = eval($templates->render("admin/no_records"));

		// Pobranie paginacji
		$pagination = get_pagination($rows_count, $G_PAGE, "admin.php", $get);
		if (strlen($pagination))
			$tfoot_class = "display_tfoot";

		// Pobranie nagłówka tabeli
		$thead = eval($templates->render("admin/payment_service_code_thead"));

		// Pobranie struktury tabeli
		$output = eval($templates->render("admin/table_structure"));
		return $output;
	}

}