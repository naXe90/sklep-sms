<?php

$heart->register_page("admin_payment_transfer", "PageAdminPaymentTransfer");

class PageAdminPaymentTransfer extends PageAdmin {

	function __construct()
	{
		global $lang;
		$this->title = $lang['payment_transfer'];

		parent::__construct();
	}

	protected function content($get, $post) {
		global $db, $settings, $lang, $G_PAGE;

		$where = "( t.payment = 'transfer' ) ";

		// Wyszukujemy dane ktore spelniaja kryteria
		if (isset($get['search']))
			searchWhere(array("t.payment_id", "t.income", "t.ip"), urldecode($get['search']), $where);

		// Jezeli jest jakis where, to dodajemy WHERE
		if (strlen($where))
			$where = "WHERE " . $where . " ";

		// Wykonujemy zapytanie
		$result = $db->query(
			"SELECT SQL_CALC_FOUND_ROWS * " .
			"FROM ({$settings['transactions_query']}) as t " .
			$where .
			"ORDER BY t.timestamp DESC " .
			"LIMIT " . get_row_limit($G_PAGE)
		);
		$rows_count = $db->get_column("SELECT FOUND_ROWS()", "FOUND_ROWS()");

		// Pobieramy dane
		$tbody = "";
		while ($row = $db->fetch_array_assoc($result)) {
			$row['income'] = $row['income'] ? number_format($row['income'], 2) . " " . $settings['currency'] : "";

			// Podświetlenie konkretnej płatności
			if ($get['highlight'] && $get['payid'] == $row['payment_id'])
				$row['class'] = "highlighted";

			// Pobranie danych do tabeli
			eval("\$tbody .= \"" . get_template("admin/payment_transfer_trow") . "\";");
		}

		// Nie ma zadnych danych do wyswietlenia
		if (!strlen($tbody))
			eval("\$tbody = \"" . get_template("admin/no_records") . "\";");

		// Pole wyszukiwania
		$search_text = htmlspecialchars($get['search']);
		eval("\$buttons = \"" . get_template("admin/form_search") . "\";");

		// Pobranie paginacji
		$pagination = get_pagination($rows_count, $G_PAGE, "admin.php", $get);
		if (strlen($pagination))
			$tfoot_class = "display_tfoot";

		// Pobranie nagłówka tabeli
		eval("\$thead = \"" . get_template("admin/payment_transfer_thead") . "\";");

		// Pobranie struktury tabeli
		eval("\$output = \"" . get_template("admin/table_structure") . "\";");
		return $output;
	}

}