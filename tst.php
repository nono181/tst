<?php

declare(strict_types=1);

use KCAdmin\Reports\PortalChargebacksReport;

set_time_limit(0);
require_once '../include/kcadmin.inc.php';
require_once '../include/store.inc.php';

$report = new PortalChargebacksReport();
$report->setTitle("Portal Chargebacks");
$report->setSlaveLagDatabase($dbi_stores_report);

$report->addParameter('store', 'Store', 'store_select');
$report->addParameter(
	"order_capture_start_date",
	"Transaction start date",
	"date",
	array(
		'default' => date('m/d/Y', strtotime("-1 week", strtotime("now")))
	)
);
$report->addParameter("order_capture_end_date", "Transaction end date", "date", array('default' => date('m/d/Y')));
$report->addParameter("dispute_start_date", "Dispute start date", "date");
$report->addParameter("dispute_end_date", "Dispute end date", "date");
$report->addParameter("chargeback_creation_start_date", "Response start date", "date");
$report->addParameter("chargeback_creation_end_date", "Response end date", "date");
$report->addParameter("transaction_start_date", "Financial action start date", "date");
$report->addParameter("transaction_end_date", "Financial action end date", "date");
$report->addParameter("chargeback_last_modified_start", "Last modified start date", "date");
$report->addParameter("chargeback_last_modified_end", "Last modified end date", "date");
$report->addParameter(
	'approved',
	'Approved',
	'select',
	array(
		'options' => array(
			'all' => 'All',
			'manual' => 'Manual',
			'auto' => 'Auto',
		)
	)
);
$report->addParameter(
	'verify_3ds',
	'3DS Status',
	'select',
	array(
		'options' => array(
			'all' => 'All',
			'Y' => 'Match',
			'N' => 'No Match',
			'X' => 'Requested but not enrolled',
			'R' => 'Abandoned'
		)
	)
);
$report->addParameter('product', 'Product Type', 'checkboxes', [
	'options' => $report->getParameterOptions(
		'product_type',
		['money_transfer'],
		false
	)
]);
$report->addParameter('product_name_credit', 'PP Products', 'select', [
	'options' => $report->getParameterOptions('product_name_credit'),
	'default' => 'all'
]);
$report->addParameter('product_name_mobile_recharge', 'MR Products', 'select', [
	'options' => $report->getParameterOptions('product_name_mobile_recharge'),
	'default' => 'all'
]);

$nauta_products = $report->getNautaProducts();
$report->addParameter('product_name_nauta', 'Nauta Recharge Products', 'select', [
	'options' => array_merge(array('all' => 'All'), $nauta_products['nauta']),
	'default' => 'all'
]);

$report->addParameter('product_name_membership', 'Membership Products', 'select', [
	'options' => $report->getParameterOptions('product_name_membership'),
	'default' => 'all'
]);
$report->addParameter('country_vn', 'VN product Country', 'dblist', array(
	'dbi' => $dbi_stores_report, 'table' => 'countries', 'key' => 'code', 'all' => 'All'
));
$report->addParameter('country_tariff', 'TP product Country', 'dblist', array(
	'dbi' => $dbi_stores_report, 'table' => 'countries', 'key' => 'code', 'all' => 'All'
));

$report->addParameter('country_fax', 'FAX product Country', 'dblist', array(
	'dbi' => $dbi_stores_report, 'table' => 'countries', 'key' => 'code', 'all' => 'All'
));

$mvno_products = $report->getMvnoProducts();

$report->addParameter('product_mobile_credit', 'MVNO Pay As You Go Products', 'select', [
	'options' => array_merge(array('all' => 'All'), $mvno_products['mobile_credit']),
	'default' => 'all'
]);

$report->addParameter('product_mobile_voice', 'MVNO Voice Products', 'select', [
	'options' => array_merge(array('all' => 'All'), $mvno_products['mobile_voice']),
	'default' => 'all'
]);

$report->addParameter('product_mvno_sms', 'MVNO SMS Products', 'select', [
	'options' => array_merge(array('all' => 'All'), $mvno_products['mobile_sms']),
	'default' => 'all'
]);

$report->addParameter('product_mvno_sim', 'Mobile Sim', 'select', [
	'options' => array_merge(['all' => 'All'], $mvno_products['mobile_sim']),
	'default' => 'all'
]);

$report->addParameter('mobile_data_bundle', 'MVNO Data Bundle', 'select', [
	'options' => array_merge(array('all' => 'All'), $mvno_products['mobile_data']),
	'default' => 'all'
]);

$report->addParameter('product_mvno_device', 'Mobile Device', 'select', [
	'options' => array_merge(array('all' => 'All'), $mvno_products['mobile_device']),
	'default' => 'all'
]);

$report->addParameter('product_mvno_msisdn', 'MVNO MSISDN', 'select', [
	'options' => $report->getParameterOptions('product_mvno_msisdn'),
	'default' => 'all'
]);

$report->addParameter('product_mvno_supplier', 'MVNO supplier', 'select', [
	'options' => $report->getParameterOptions('product_mvno_supplier'),
	'default' => 'all'
]);

$report->addParameter('product_shipping', 'MVNO Shipping', 'select', [
	'options' => array_merge(['all' => 'All'], $mvno_products['shipping']),
	'default' => 'all'
]);

$report->addParameter('mobile_pack', 'MVNO Bundle Products', 'select', [
	'options' => array_merge(array('all' => 'All'), $mvno_products['mobile_pack']),
	'default' => 'all'
]);

$report->addParameter('product_rate_plan', 'Rate Plans', 'select', [
	'options' => $report->getParameterOptions('product_rate_plan'),
	'default' => 'all'
]);

$report->addParameter('order_country_code', 'IP Country Code', 'text');
$report->addParameter('cc_country_code', 'CC Country Code', 'text');
$report->addParameter('mr_country_code', 'MR Country Code', 'text');
$report->addParameter('customer_id', 'Customer ID', 'text');
$report->addParameter('cc_number', 'CC Number', 'text');
$report->addParameter('bin', 'BIN', 'text');
$report->addParameter(
	'chargeback_source',
	'Chargeback Source',
	'select',
	array(
		'options' => array(
			'all' => 'All',
			'amex' => 'Amex',
			'chase' => 'Chase',
			'elavon' => 'Elavon',
			'appleiap' => 'Apple In App Purchase',
			'paypal' => 'PayPal',
			'globalcollect' => 'GlobalCollect',
			'payu' => 'PayU',
		)
	)
);
$report->addParameter(
	'cc_type',
	'CC Type',
	'select',
	array(
		'options' => array(
			'all' => 'All',
			'VISA' => 'Visa',
			'MASTERCARD' => 'Mastercard',
			'AMERICAN EXPRESS' => 'Amex',
			'DISCOVER' => 'Discover',
			'JCB' => 'JCB',
			'CHINA UNION PAY' => 'Union Pay',
			'paypal' => 'PayPal',
		)
	)
);
$report->addParameter('reason_code_category', 'Reason Code Category', 'select', [
	'options' => $report->getParameterOptions('reason_code_category'),
	'default' => 'all'
]);

$report->addParameter(
	'pay_processor',
	'Payment Processor',
	'select',
	[
		'options' => ['all' => 'All'] + $dbi_stores_report->queryAssoc(
			"SELECT DISTINCT processor, processor FROM payment_methods WHERE processor IS NOT NULL ORDER BY processor"
		),
	]
);
$report->addParameter('payment_module', 'Payment Method', 'dblist', array(
	'dbi' => $dbi_stores_report, 'table' => 'payment_methods', 'name' => 'name', 'all' => 'All',
	'where' => "real_money = 1 AND processor IS NOT NULL",
));
$report->addParameter('fraud_review_status', '3rd Party Review', 'select', [
	'options' => $report->getParameterOptions('fraud_review_status'),
	'default' => 'no_filter'
]);

$report->addParameter('fraud_review_risk_category', '3rd Party Review - Risk Category', 'select', [
	'options' => $report->getParameterOptions('fraud_review_risk_category'),
	'default' => 'all'
]);

$report->addParameter(
	"chargeback_status",
	"Chargeback Status",
	"select",
	array(
		"options" => array(
			"all" => "All",
			"open" => "Open",
			"closed" => "Closed"
		)
	)
);
$report->addParameter(
	"resolution",
	"Resolution",
	"select",
	array(
		"options" => array(
			"all" => "All",
			"pending" => "Pending",
			"won" => "Won",
			"lost" => "Lost"
		)
	)
);
$report->addParameter(
	'currency',
	'Currency',
	'select',
	['options' => array_merge(['all' => 'All'], $report->getAvailableCurrencies())]
);
$report->addParameter(
	'payments_user',
	'User',
	'dblist',
	array(
		'dbi' => $dbi_portal_report,
		'table' => 'users',
		"key" => "id",
		"name" => "login",
		"all" => "All",
		"where" => " id IN (SELECT user_id from user_groups WHERE user_groups.group_id = 3111)"
	)
);
$report->addParameter(
	'group_by',
	'Group By',
	'select',
	array(
		"options" => array(
			"" => "None",
			"store" => "Store",
			"product_type" => "Product Type",
			"processor" => "Processor",
			"pay_method" => "Payment Method",
			"currency_code" => "Currency",
			"cc_type" => "Card Type",
			"cc_type/reason_code" => "Card Type/Reason Code",
			"reason_code_category" => "Reason Code Category",
			"cc_country_code" => "CC Country Code",
			"financial_action_date/product_type" => "Date/Product Type",
			"order_country_code" => "Order IP Country Code",
			"mr_country_code" => "MR Country Code",
			"chargeback_source" => "Chargeback Source",
			"payments_user/chargeback_source/charge_type" => "User/ Chargeback Source/ Chargeback Type",
			"processor/chargeback_status" => "Processor/Status",
			"processor/resolution" => "Processor/Resolution",
			"processor/pay_method" => "Processor/Payment method",
			"store/processor" => "Store/Processor",
			"store/pay_method" => "Store/Payment method",
			"store/currency_code" => "Store/Currency",
			"store/cc_type" => "Store/Card type",
			"store/customer_id" => "Store/Customer ID",
			"bin" => "BIN",
			"chargeback_summary" => "Chargeback Summary as of Today",
			"fraud_review_risk_category" => "Fraud Review Risk Category",
		)
	)
);

$store = $report->getParameterValue('store');
$payments_user = $report->getParameterValue("payments_user");
$approved = $report->getParameterValue("approved");
$products = $report->getParameterValue("product");
$product_name_credit = $report->getParameterValue('product_name_credit');
$product_name_mobile_recharge = $report->getParameterValue('product_name_mobile_recharge');
$product_name_nauta = $report->getParameterValue('product_name_nauta');
$product_name_membership = $report->getParameterValue('product_name_membership');
$country_vn = $report->getParameterValue('country_vn');
$country_tariff = $report->getParameterValue('country_tariff');
$country_fax = $report->getParameterValue('country_fax');
$product_mobile_credit = $report->getParameterValue('product_mobile_credit');
$product_mobile_voice = $report->getParameterValue('product_mobile_voice');
$product_mvno_sms = $report->getParameterValue('product_mvno_sms');
$product_mvno_sim = $report->getParameterValue('product_mvno_sim');
$mobile_data_bundle = $report->getParameterValue('mobile_data_bundle');
$product_mvno_device = $report->getParameterValue('product_mvno_device');
$product_mvno_msisdn = $report->getParameterValue('product_mvno_msisdn');
$product_mvno_supplier = $report->getParameterValue('product_mvno_supplier');
$product_shipping = $report->getParameterValue('product_shipping');
$mobile_pack = $report->getParameterValue('mobile_pack');
$product_rate_plan = $report->getParameterValue('product_rate_plan');
$pay_processor = $report->getParameterValue("pay_processor");
$order_country_code = $report->getParameterValue("order_country_code");
$cc_country_code = $report->getParameterValue("cc_country_code");
$mr_country_code = $report->getParameterValue("mr_country_code");
$customer_id = $report->getParameterValue("customer_id");
$cc_number = $report->getParameterValue("cc_number");
$bin = $report->getParameterValue("bin");
$payment_module = $report->getParameterValue("payment_module");
$fraud_review_status = $report->getParameterValue("fraud_review_status");
$fraud_review_risk_category = $report->getParameterValue("fraud_review_risk_category");
$cc_type = $report->getParameterValue("cc_type");
$reason_code_category = $report->getParameterValue("reason_code_category");
$chargeback_source = $report->getParameterValue("chargeback_source");
$chargeback_status = $report->getParameterValue("chargeback_status");
$resolution = $report->getParameterValue("resolution");
$currency = $report->getParameterValue("currency");
$group_by = $report->getParameterValue("group_by");
$verify_3ds = $report->getParameterValue('verify_3ds');
$transaction_start_date = $report->getParameterValue('transaction_start_date');
$transaction_end_date = $report->getParameterValue('transaction_end_date');

$grouped_rows = array();
$group_sep = "\x00";
if (isset($_REQUEST['CSVReportOutputCustom']) && $group_by != 'product_type') {
	$report->setHeader("<p class=\"error\">Incompatible group for export, use group by Product type</p>");
	//add dummy column
	$report->addColumn('dummy', 'dummy', 'string');
	unset($_REQUEST['CSVReportOutputCustom']);
} elseif ($report->needsData()) {
	$having_condition = $where_condition = array();
	$stores = array();
	
	if (!empty($mr_country_code)) {
		$having_condition[] = "mr_country_code IS NOT NULL";
	}
	$date_fields = array(
		array(
			"fields" => array("order_capture_start_date", "order_capture_end_date"),
			"column" => "orders.date"
		),
		array(
			"fields" => array("dispute_start_date", "dispute_end_date"),
			"column" => "chargebacks.dispute_date"
		),
		array(
			"fields" => array("chargeback_creation_start_date", "chargeback_creation_end_date"),
			"column" => "chargebacks.created_at"
		),
		array(
			"fields" => array("chargeback_last_modified_start", "chargeback_last_modified_end"),
			"column" => "chargebacks.last_modified_date"
		),
		array(
			"fields" => array("transaction_start_date", "transaction_end_date"),
			"column" => "ot.date"
		),
	);

	foreach ($date_fields as $date_data) {
		[$start_field, $end_field] = $date_data['fields'];

		$column_name = $date_data['column'];
		$$start_field = $report->getParameterValue($start_field);
		$$end_field = $report->getParameterValue($end_field);

		if ($$start_field && $$end_field && $column_name) {
			$$start_field = date("Y-m-d 00:00:00", strtotime($$start_field));
			$$end_field = date("Y-m-d 23:59:59", strtotime($$end_field));
			$where_condition[] = " {$column_name} >= '{$$start_field}' AND {$column_name} <= '{$$end_field}' ";
		}
	}

	if ($payments_user != 'all') {
		if (strlen($chargeback_last_modified_start) > 0) {
			$where_condition[] = " chargebacks.last_modified_user = " . $dbi_stores_report->escape($payments_user) . " ";
		} else {
			$where_condition[] = " chargebacks.created_by = " . $dbi_stores_report->escape($payments_user) . " ";
		}
	}

	if ($pay_processor != 'all') {
		$where_condition[] = " pm.processor = " . $dbi_stores_report->escape($pay_processor);
	}

	if ($payment_module != 'all') {
		$where_condition[] = " pm.id = " . $dbi_stores_report->escape($payment_module);
	}

	if ($fraud_review_status !== 'no_filter') {
		if ($fraud_review_status === 'all') {
			$where_condition[] = " orders.fraud_review_status IS NOT NULL ";
		} elseif ($fraud_review_status === 'none') {
			$where_condition[] = " orders.fraud_review_status IS NULL ";
		} else {
			$where_condition[] = " orders.fraud_review_status = " . $dbi_stores_report->escape($fraud_review_status) . " ";
		}
	}

	$fraud_review_risk_category_link = "LEFT JOIN order_options oop_fraud_review
		ON oop_fraud_review.order_id = orders.id AND oop_fraud_review.type = 'fraud_review'";
	$fraud_review_data = "
		CASE
			WHEN JSON_VALUE(oop_fraud_review.value,'$.fraud_review_risk_type') IS NOT NULL
				THEN JSON_VALUE(oop_fraud_review.value,'$.fraud_review_risk_type')
			ELSE 'No verification'
		END
	";
	if (
		$fraud_review_risk_category !== 'all'
		&& !in_array($fraud_review_status, ['no_filter', 'none'])
	) {
		$fraud_review_risk_category = str_replace('_', ' ', $fraud_review_risk_category);
		$where_condition[] = "JSON_VALUE(oop_fraud_review.value,'$.fraud_review_risk_type') = "
			. $dbi_stores_report->escape($fraud_review_risk_category) . " ";
	}

	if ($chargeback_status != 'all') {
		$where_condition[] = " chargebacks.status = " . $dbi_stores_report->escape($chargeback_status) . " ";
	}

	if ($resolution != 'all') {
		$where_condition[] = " chargebacks.resolution = " . $dbi_stores_report->escape($resolution) . " ";
	}

	if ($currency != 'all') {
		$where_condition[] = " currencies.code =  " . $dbi_stores_report->escape($currency) . " ";
	}

	$products_rules = array(
		'credit' => array(
			'condition' => 'LIKE',
			'prod_str' => '',
			'recharge' => false,
			'value' => $product_name_credit,
		),
		'mobile_recharge' => array(
			'condition' => '',
			'prod_str' => '',
			'recharge' => true,
			'value' => $product_name_mobile_recharge,
		),
		'nauta' => array(
			'condition' => '',
			'prod_str' => '',
			'recharge' => false,
			'value' => $product_name_nauta,
		),
		'credit_subscription' => array(
			'condition' => 'LIKE',
			'prod_str' => 'PHONECLUB-',
			'value' => $product_name_membership,
		),
		'vn' => array(
			'condition' => '',
			'prod_str' => 'VN-01-',
			'value' => $country_vn,
		),
		'tariff_plan' => array(
			'condition' => 'LIKE',
			'prod_str' => 'TP-01-%-',
			'value' => $country_tariff,
		),
		'fax' => array(
			'condition' => '',
			'prod_str' => 'TP-01-%-',
			'value' => $country_fax,
		),
		'mobile_credit' => array(
			'condition' => '',
			'prod_str' => '',
			'value' => $product_mobile_credit,
		),
		'mobile_voice' => array(
			'condition' => '',
			'prod_str' => '',
			'value' => $product_mobile_voice,
		),
		'mobile_sms' => array(
			'condition' => '',
			'prod_str' => '',
			'value' => $product_mvno_sms,
		),
		'mobile_sim' => array(
			'condition' => '',
			'prod_str' => '',
			'value' => $product_mvno_sim,
			'recharge' => false,
		),
		'mobile_msisdn' => [
			'condition' => '',
			'prod_str' => '',
			'value' => '',
			'recharge' => false,
		],
		'rate_plan' => [
			'condition' => '',
			'prod_str' => '',
			'value' => $product_rate_plan,
			'recharge' => true,
		],
		'mobile_data' => array(
			'condition' => '',
			'prod_str' => '',
			'value' => $mobile_data_bundle,
		),
		'mobile_device' => array(
			'condition' => '',
			'prod_str' => '',
			'recharge' => false,
			'value' => $product_mvno_device,
		),
		'shipping' => [
			'condition' => '',
			'prod_str' => '',
			'value' => $product_shipping,
			'recharge' => false,
		],
		'mobile_pack' => array(
			'condition' => '',
			'prod_str' => '',
			'value' => $mobile_pack,
		),
		'donation' => [
			'condition' => '',
			'prod_str' => '',
			'value' => '',
			'recharge' => false,
		],
		'vn_p' => array(
			'condition' => '',
			'prod_str' => '',
			'value' => '',
			'recharge' => false,
		),
		'gift_card' => array(
			'condition' => '',
			'prod_str' => '',
			'value' => '',
			'recharge' => false,
		),
	);
	$product_condition = '';
	$mvno_supplier_condition = '';
	if (
		!empty($products)
		&& in_array('mobile_msisdn', $products)
		&& !empty($product_mvno_supplier)
		&& $product_mvno_supplier != 'all'
	) {
		if ($product_mvno_supplier == 'sprint') {
			$mvno_supplier_condition = " products.code = 'M-MSISDN' ";
		} elseif ($product_mvno_supplier == 't-mobile') {
			$mvno_supplier_condition = " products.code = 'M-MSISDN-GSM' ";
		}
	}

	$mobile_msisdn_condition = '';
	$products_rules_default = $products_rules;
	if (
		!empty($products)
		&& in_array('mobile_msisdn', $products)
		&& (!empty($product_mvno_msisdn) || empty($mvno_supplier_condition))
		&& ($product_mvno_msisdn != 'all' || $product_mvno_supplier != 'all')
	) {
		unset($products_rules_default['mobile_msisdn']);
		$mobile_msisdn_condition = $report->mobileMsisdnConditions(
			$product_mvno_msisdn,
			'products',
			'op',
			'oo'
		);
	}

	$prod_type = false;
	if ($products) {
		if (!is_array($products)) {
			redirect(url('reports/portal_chargebacks.php'));
		}

		$prod_type = true;
		$product_conds = $report->productsConditions($products_rules, $products);
		if ($product_conds || $mobile_msisdn_condition) {
			if (!empty($product_conds) && !empty($mobile_msisdn_condition)) {
				if (empty($mvno_supplier_condition)) {
					$mobile_msisdn_condition = "OR ({$mobile_msisdn_condition})";
				} else {
					$mobile_msisdn_condition_applied = true;
					$mobile_msisdn_condition = "OR ({$mobile_msisdn_condition} AND {$mvno_supplier_condition})";
				}
			}

			$product_condition = $product_conds . $mobile_msisdn_condition;
			// add supplier condition, if provided and not previously applied.
			if (!empty($mvno_supplier_condition) && !isset($mobile_msisdn_condition_applied)) {
				$product_condition = $product_conds
					. $mobile_msisdn_condition
					. 'AND' . $mvno_supplier_condition;
			}
			// add the products condition inside ( ) since the string can contain OR
			$where_condition[] = ' (' . $product_condition . ') ';
		}
	}

	if ($group_by == 'product_type' || $prod_type || $group_by == 'financial_action_date/product_type') {
		$where_condition[] = " ot.result = 'successful' ";
	}

	switch ($group_by) {
		case '':
			$grouping = array('store');
			$summaries = array();

			$group_by_string = " GROUP BY identifier";

			$report->addColumn("store", "Store", "string");
			$report->addColumn("ticket_id", "Internal Case Number", "string");
			$report->addColumn("merchant_case_number", "Merchant Case Number", "string");
			$report->addColumn("charge_type", "Chargeback Case Type", "string");
			$report->addColumn("chargeback_type", "Chargeback Type", "string");
			$report->addColumn("cc_type", "CC Type", "string");
			$report->addColumn("cc_country_code", "CC Country Code", "string");
			$report->addColumn("account_age", "Account Age", "string");
			$report->addColumn("capture_date", "Transaction Date", "string");
			$report->addColumn("dispute_date", "Dispute Date", "string");
			$report->addColumn("last_modified_date", "Last Modified Date", "string");
			$report->addColumn("response_due_date", "Response Due Date", "string");
			$report->addColumn("created_at", "Case Creation Date", "string");
			$report->addColumn("gap_creation_dispute", "Gap Creation-Dispute", "number2");
			$report->addColumn("gap_capture_dispute", "Gap Capture-Dispute", "number2");
			$report->addColumn("chargeback_source", "Chargeback Source", "string");
			$report->addColumn("currency_code", "Currency", "string");
			$report->addColumn("order_value", "Order Value USD", "number2");
			$report->addColumn("order_value_initial", "Order Value", "number2");
			$report->addColumn("customer_name", "Customer Name", "string");
			$report->addColumn("order_id", "Order ID", "string");
			$report->addColumn("verify_3ds", "3D Secure Status", "string");
			$report->addColumn("product_type", "Product", "string");

			if (
				is_array($products)
				&& (
					in_array('mobile_recharge', $products)
					|| in_array('mobile_recharge_charge', $products)
				)
			) {
				$report->addColumn("mr_country_code", "MR Country Code", "string");
			}

			$report->addColumn("cc_number", "CC Number", "string");
			$report->addColumn("customer_id", "Customer ID", "string");
			$report->addColumn("identifier", "Identifier", "string");
			$report->addColumn("order_country_code", "Order Country", "string");
			$report->addColumn("chargeback_status", "Chargeback status", "string");
			$report->addColumn("resolution", "Chargeback resolution", "string");
			$report->addColumn("payments_user", "User - Created", "string");
			$report->addColumn("last_payments_user", "User - Modified", "string");
			$report->addColumn("reason_code", "Chargeback reason code", "string");
			$report->addColumn("reason_code_category", "Reason Code Category", "string");
			$report->addColumn("bin", "BIN", 'string');
			$report->addColumn("issuing_bank", "Issuing Bank", 'string');
			$report->addColumn("last_financial_action", "Last Financial Action Date", 'string');
			break;

		case 'store':
		case 'product_type':
		case 'mr_country_code':
		case 'processor':
		case 'pay_method':
		case 'currency_code':
		case 'cc_type':
		case 'cc_type/reason_code':
		case 'reason_code_category':
		case 'cc_country_code':
		case 'order_country_code':
		case 'cc_number':
		case 'chargeback_source':
		case 'payments_user/chargeback_source/charge_type':
		case 'financial_action_date/product_type':
		case 'processor/chargeback_status':
		case 'processor/resolution':
		case 'processor/pay_method':
		case 'store/processor':
		case 'store/pay_method':
		case 'store/currency_code':
		case 'store/customer_id':
		case 'store/cc_type':
		case 'bin':
		case 'fraud_review_risk_category':
			$grouping = explode('/', $group_by);
			$summaries = array(
				'id' => 'sum',
				'order_value' => 'sum',
				'order_refunded' => 'sum',
				'gap_creation_dispute' => 'avg',
				'gap_capture_dispute' => 'avg',
				'gap_capture_created_at' => 'avg',
				'order_refunded_amount' => 'sum',
				'transactions_amount' => 'sum',
				'tax' => 'sum',
			);

			foreach ($grouping as $g) {
				$report->addColumn($g, ucwords(str_replace('_', ' ', $g)), 'string');
			}

			if ($group_by == 'bin') {
				$report->addColumn("issuing_bank", "Issuing Bank", 'string');
			}

			$report->addColumn("id", "Chargebacks", 'number');
			$report->addColumn("order_value", "Order Value USD", 'number2');
			//$report->addColumn("order_value", "Chargebacks amount", 'number2');

			if ($order_capture_start_date && $order_capture_end_date) {
				$report->addColumn("order_refunded", "Refunds", 'number');
			}

			if ($payments_user != 'all') {
				$report->addColumn("order_refunded_amount", "Refunds amount", 'number2');
				if ($order_capture_start_date && $order_capture_end_date) {
					$report->addColumn("order_refunded_amount_percent", "Refunds amount %", 'percent2');
				}
				$report->addColumn("gap_creation_dispute", "Gap Creation-Dispute", "string");
				$report->addColumn("gap_capture_dispute", "Gap Capture-Dispute", "string");
				$report->addColumn("gap_capture_created_at", "Gap Capture-Created", "string");
			}

			if ($group_by == 'payments_user/chargeback_source/charge_type') {
				if (strlen($chargeback_last_modified_start) > 0) {
					$report->addColumn("payments_user", "User - Modified", "string");
				} else {
					$report->addColumn("payments_user", "User - Created", "string");
				}
			}

			$group_by_string = " GROUP BY " . str_replace("/", ', ', $group_by);
			break;

		case 'chargeback_summary':
			$report->addColumn("key_metrics", "Key Metrics", 'string');
			$report->addColumn("count", "Count", 'string');
			$report->addColumn("amount", "Amount", 'string');
			$group_by_string = '';
			$grouping = [];
			break;

		default:
			exit("Grouping by $group_by not implemented");
	}

	if ($group_by != 'chargeback_summary') {
		$report->addColumn("transactions_amount_max", "Chargebacks amount", 'number2');
		$report->addColumn("transactions_amount", "Financial action", 'number2');
		$report->addColumn("tax", "Financial action tax", 'number2');
		$report->addColumn("financial_action_total", "Financial action total", 'number2');
	}

	if ($chargeback_source != 'all') {
		$where_condition[] = " chargebacks.merchant_name = '$chargeback_source' ";
	}

	$portal_users = [];
	$chargebacks_reasons = [];
	$portal_users = $dbi_portal_report->queryAssocRows("
		SELECT
			id AS user_id,
			login
		FROM
			users
		WHERE
			id IN (SELECT user_id FROM user_groups WHERE user_groups.group_id = 3111)
	");

	$chargebacks_reasons = $dbi_portal_report->queryAssocRows("
		SELECT
			id,
			CONCAT(`code`, ' ', description) AS description,
			category
		FROM
			chargeback_reason
	");
	
	$reason_code_categories = $dbi_portal_report->queryAssocRows("
		SELECT
			GROUP_CONCAT(DISTINCT `id`) AS ids,
			`category`
		FROM
			chargeback_reason
		GROUP BY `category`
	");
	
	$reason_code_categories_select = '(CASE ';
	$reason_code_categories_condition = [];
	foreach ($reason_code_categories as $reason_category) {
		$reason_code_categories_condition[] = "
		WHEN chargeback_reason_id IN ({$reason_category['ids']})
		THEN '{$reason_category['category']}' ";
	}
	$reason_code_categories_select .= implode(' ', $reason_code_categories_condition);
	$reason_code_categories_select .= " ELSE '' END)";
	if (!$reason_code_categories_condition) {
		$reason_code_categories_select = 'null';
	}
	if ($reason_code_category != 'all') {
		$having_condition[] = "reason_code_category = '{$reason_code_category}'";
	}
	
	if ($verify_3ds != 'all') {
		$where_condition[] = " orders.3dsecure_verified = '{$verify_3ds}' ";
	}

	if ($order_country_code) {
		$where_condition[] = " orders.ip_country_code = '{$order_country_code}' ";
	}

	if ($cc_country_code) {
		$where_condition[] = " orders.cc_country_code = '{$cc_country_code}' ";
	}

	if ($customer_id) {
		$where_condition[] = " orders.customer_id = '{$customer_id}' ";
	}

	if ($cc_number) {
		$where_condition[] = "  orders.cc_number = '{$cc_number}' ";
	}

	if ($bin) {
		$where_condition[] = " IF(LENGTH(orders.cc_number) > 1, LEFT(orders.cc_number, 6) , 'PayPal') = '{$bin}' ";
	}

	if ($approved != 'all') {
		if ($approved == 'manual') { /* manually approved orders - exclude orders sent to fraud review */
			$where_condition[] = " orders.approved = 'manual'
				AND (fraud_review_status <> 'approved' OR fraud_review_status IS NULL)";
		} else {
			$where_condition[] = " orders.approved = '{$approved}' ";
		}
	}

	switch ($cc_type) {
		case 'all':
			break;

		case 'paypal':
			$where_condition[] = ' pm.module = "paypalexpress" ';
			break;

		default:
			$where_condition[] = " orders.cc_card_brand = '{$cc_type}' ";
			break;
	}

	if ($group_by == 'chargeback_summary') {
		$where_condition = []; // reset all other conditions
	}

	$where_condition_string = '';
	if ($where_condition) {
		$where_condition_string .= ' WHERE ' . implode(' AND ', $where_condition);
	}

	$rows = [];
	if ($group_by != 'chargeback_summary') {
		if ($group_by == 'product_type' || $prod_type || $group_by == 'financial_action_date/product_type') {
			$dbi_stores_report->query("
				CREATE TEMPORARY TABLE IF NOT EXISTS tmp_portal_chargebacks (
					opt_id INT(11),
					order_product_id INT(11),
					order_transaction_id INT(11),
					cb_id INT(11),
					store VARCHAR(40) NOT NULL,
					cc_country_code VARCHAR(2),
					account_age INT(4),
					capture_date DATETIME,
					cc_number VARCHAR(20),
					id INT(11),
					order_country_code VARCHAR(2),
					verify_3ds VARCHAR(40),
					product_type VARCHAR(50),
					product_code VARCHAR(100),
					mr_country_code VARCHAR(20),
					customer_id INT(11),
					identifier VARCHAR(20),
					customer_name VARCHAR(128),
					order_refunded TINYINT(1),
					order_refunded_amount DECIMAL(20,4),
					cc_type VARCHAR(50),
					pay_method VARCHAR(50),
					processor VARCHAR(50),
					gap_creation_dispute DECIMAL(20,4),
					gap_capture_dispute DECIMAL(20,4),
					gap_capture_created_at DECIMAL(20,4),
					ticket_id INT(11),
					merchant_case_number VARCHAR(50),
					dispute_date DATETIME,
					last_modified_date DATETIME,
					response_due_date DATETIME,
					created_at DATETIME,
					order_id INT(11) NOT NULL,
					reason_code INT(11),
					chargeback_reason_id INT(11),
					reason_code_category VARCHAR(50),
					last_financial_action DATE,
					financial_action_date DATETIME,
					currency_code VARCHAR(10),
					chargeback_source varchar(50),
					chargeback_type ENUM('accept','dispute'),
					payments_user INT(11),
					last_payments_user INT(11),
					chargeback_status ENUM('open','closed'),
					resolution ENUM('pending','won','lost'),
					charge_type ENUM('chargeback','prearbitration','inquiry','refund_request','secondchargeback','retrieval',
							'firstchargeback','bank_issued_payment_reversal'),
					bin VARCHAR(20),
					issuing_bank VARCHAR(128),
					store_id INT(11),
					order_transaction_ids TEXT,
					all_transaction_types TEXT,
					transaction_type ENUM('capture','void','refund','chargeback'),
					order_value DECIMAL(20,4),
					order_value_initial DECIMAL(20,4),
					tax DECIMAL(20,4),
					transactions_amount DECIMAL(20,4),
					transactions_amount_max DECIMAL(20,4),
					order_total_value DECIMAL(20,4),
					financial_action_total DECIMAL(20,4),
					bank_action_total DECIMAL(20,4),
					order_product_amount DECIMAL(20,4),
					order_product_amount_initial DECIMAL(20,4),
					all_products_ids_nr INT(10),
					KEY `order_id` (`order_id`),
					KEY `identifier` (`identifier`)
				)
			");
		} else {
			$dbi_stores_report->query("
				CREATE TEMPORARY TABLE IF NOT EXISTS tmp_portal_chargebacks (
					store VARCHAR(40) NOT NULL,
					processor VARCHAR(50),
					pay_method VARCHAR(50),
					currency_code VARCHAR(10),
					order_country_code VARCHAR(2),
					account_age INT(4),
					capture_date DATETIME,
					identifier VARCHAR(20),
					cc_type VARCHAR(50),
					" . (
					($mr_country_code || $group_by == 'mr_country_code') ? "mr_country_code VARCHAR(20)," : ""
					) . "
					order_refunded TINYINT(1),
					product_type TEXT,
					last_payments_user INT(11),
					chargeback_type ENUM('accept','dispute'),
					last_financial_action DATE,
					financial_action_date DATETIME,
					order_refunded_amount DECIMAL(20,4),
					verify_3ds VARCHAR(40),
					reason_code INT(11),
					cc_country_code VARCHAR(2),
					chargeback_reason_id INT(11),
					reason_code_category VARCHAR(50),
					gap_creation_dispute DECIMAL(20,4),
					gap_capture_dispute DECIMAL(20,4),
					gap_capture_created_at DECIMAL(20,4),
					cc_number VARCHAR(20),
					chargeback_source varchar(50),
					payments_user INT(11),
					customer_name VARCHAR(128),
					issuing_bank VARCHAR(128),
					dispute_date DATETIME,
					last_modified_date DATETIME,
					response_due_date DATETIME,
					created_at DATETIME,
					charge_type ENUM('chargeback','prearbitration','inquiry','refund_request','secondchargeback','retrieval',
							'firstchargeback','bank_issued_payment_reversal'),
					chargeback_status ENUM('open','closed'),
					resolution ENUM('pending','won','lost'),
					customer_id INT(11),
					bin VARCHAR(20),
					store_id INT(11),
					chargeback_id INT(11),
					ticket_id INT(11),
					order_id INT(11),
					merchant_case_number VARCHAR(50),
					tax DECIMAL(20,4),
					order_value DECIMAL(20,4),
					order_value_initial DECIMAL(20,4),
					transactions_amount DECIMAL(20,4),
					transactions_amount_max DECIMAL(20,4),
					financial_action_total DECIMAL(20,4),
					order_transaction_ids TEXT,
					fraud_review_risk_category VARCHAR(20),
					KEY `chargeback_id` (`chargeback_id`),
					KEY `identifier` (`identifier`)
				)
			");
		}
	}

	foreach (connect_to_each_store($store) as $name => $store_conn) {
		if ($group_by != 'chargeback_summary') {
			if ($group_by == 'product_type' || $prod_type || $group_by == 'financial_action_date/product_type') {
				$query = "
					SELECT
						opt.id AS opt_id,
						op.id AS order_product_id,
						ot.id AS order_transaction_id,
						chargebacks.id AS cb_id,
						'$name' AS store,
						orders.cc_country_code,
						TIMESTAMPDIFF(
							DAY,
							(SELECT customers.registered FROM customers WHERE customers.id = orders.customer_id),
							orders.date
						) AS account_age,
						orders.capture_date,
						orders.cc_number,
						orders.id AS id,
						orders.ip_country_code AS order_country_code,
						(
							CASE
								WHEN orders.3dsecure_verified = 'X' THEN 'Requested but not enrolled'
								WHEN orders.3dsecure_verified = 'Y' THEN 'Match'
								WHEN orders.3dsecure_verified = 'N' THEN 'No Match'
								WHEN orders.3dsecure_verified = 'R' THEN 'Abandoned'
								ELSE '-'
							END
						) AS verify_3ds,
						products.type AS product_type,
						products.code AS product_code,
						(
							SELECT
							order_products_mobile_recharge.country_code
							FROM
								order_products,
								order_products_mobile_recharge
							WHERE order_products.order_id = orders.id
							AND order_products_mobile_recharge.order_product_id = order_products.id
							" . ($mr_country_code ? " AND order_products_mobile_recharge.country_code = '"
								. strtoupper($mr_country_code) . "'" : "") . "
							LIMIT 1
						) AS mr_country_code,
						orders.customer_id,
						(
							CONCAT('S',(SELECT value FROM store_settings WHERE parameter = 'store_id'), 'O', orders.id)
						 ) AS identifier,
						CONCAT(orders.bill_first_name, ' ', orders.bill_last_name) AS customer_name,
						IF(orders.bill_status IN ('refund_ok', 'void_ok'), 1, 0) AS order_refunded,
						(IF(orders.bill_status IN ('refund_ok', 'void_ok'),
							(SUM(((op.quantity * op.price + op.processing_fee + op.total_tax) / total * refund_amount)
								* orders.currency_rate) / COUNT(op.id)),
							0
						)) AS order_refunded_amount,
						(
							IF (
								orders.cc_number IS NULL OR orders.cc_number = '', 'PayPal',
								IFNULL(orders.cc_card_brand, 'Unknown')
							)
						) AS cc_type,
						pm.name AS pay_method,
						pm.processor AS processor,
						IF (TIMESTAMPDIFF(DAY, chargebacks.dispute_date, chargebacks.created_at),
							AVG(TIMESTAMPDIFF(DAY, chargebacks.dispute_date, chargebacks.created_at)),
							NULL) AS gap_creation_dispute,
						IF (TIMESTAMPDIFF(DAY, orders.capture_date, chargebacks.dispute_date),
							AVG(TIMESTAMPDIFF(DAY, orders.capture_date, chargebacks.dispute_date))
						, NULL) AS gap_capture_dispute,
						IF (TIMESTAMPDIFF(DAY, orders.capture_date, chargebacks.created_at),
							AVG(TIMESTAMPDIFF(DAY, orders.capture_date, chargebacks.created_at))
						, NULL) AS gap_capture_created_at,
						chargebacks.ticket_id,
						chargebacks.merchant_case_number,
						MAX(chargebacks.dispute_date) AS dispute_date,
						MAX(chargebacks.last_modified_date) AS last_modified_date,
						MAX(chargebacks.response_due_date) AS response_due_date,
						chargebacks.created_at AS created_at,
						orders.id AS order_id,
						chargebacks.chargeback_reason_id AS reason_code,
						chargebacks.chargeback_reason_id,
						{$reason_code_categories_select} AS reason_code_category,
						(
							SELECT
								MAX(order_transactions.date)
							FROM order_transactions
							WHERE order_transactions.chargeback_id = chargebacks.id
							AND order_transactions.type = 'chargeback'
							ORDER BY order_transactions.date DESC
						) AS last_financial_action,
						ot.date AS financial_action_date,
						currencies.code AS currency_code,
						chargebacks.merchant_name AS chargeback_source,
						chargebacks.type AS chargeback_type,
						(" . ($chargeback_last_modified_start
							&& ($group_by == 'payments_user/chargeback_source/charge_type') ? "
							chargebacks.last_modified_user" : " chargebacks.created_by ") . "
						) AS payments_user,
						(
							SELECT
								chargebacks.last_modified_user
							FROM chargebacks c
							WHERE
								c.order_id = orders.id
							ORDER BY c.last_modified_date DESC
							LIMIT 1
						) AS last_payments_user,
						chargebacks.status AS chargeback_status,
						chargebacks.resolution AS resolution,
						chargebacks.case_type as charge_type,
						IF(LENGTH(orders.cc_number) > 1, LEFT(orders.cc_number, 6) , 'PayPal') as bin,
						orders.cc_issuing_bank AS issuing_bank,
						(select value from store_settings where parameter = 'store_id') as store_id,
						GROUP_CONCAT(IF( ot.type = 'chargeback', opt.id, 0)) as order_transaction_ids,
						GROUP_CONCAT(distinct ot.type) as all_transaction_types,
						ot.type as transaction_type,
						(IF(
							ot.type = 'capture',
							(SUM(orders.total) * orders.currency_rate) / COUNT(orders.id),
							0
						)) as order_value,
						(IF(
							ot.type = 'capture',
							(SUM(orders.total)) / COUNT(orders.id),
							0
						)) as order_value_initial,
						(IF(
							ot.type = 'chargeback',
							SUM((opt.total_tax) * ot.currency_rate),
							0
						)) as tax,
						(IF(
							ot.type = 'chargeback',
							SUM((opt.price * opt.quantity + opt.processing_fee) * ot.currency_rate) / COUNT(orders.id),
							0
						)) as transactions_amount,
						MAX(ABS(ot.chargeback_amount * ot.chargeback_currency_rate)) as transactions_amount_max,
						orders.total * ot.currency_rate as order_total_value,
						SUM(ot.chargeback_amount * ot.chargeback_currency_rate) / COUNT(orders.id) as financial_action_total,
						SUM(ot.chargeback_amount * ot.chargeback_currency_rate) / COUNT(orders.id) as bank_action_total,
						(SUM(op.price * op.quantity + op.processing_fee + op.total_tax) * orders.currency_rate)
							/ COUNT(orders.id)
							as order_product_amount,
						SUM(op.price * op.quantity + op.processing_fee + op.total_tax) / COUNT(orders.id)
							as order_product_amount_initial,
						(SELECT 
							COUNT(distinct all_op.id) 
						FROM 
							order_products AS all_op
						WHERE 
							all_op.order_id = orders.id
							AND all_op.price != 0
						) AS all_products_ids_nr
					FROM order_products_transactions opt
					INNER JOIN order_transactions ot ON ot.id = opt.order_transaction_id
					INNER JOIN order_products op ON op.id = opt.order_product_id
					INNER JOIN orders ON orders.id = op.order_id
					INNER JOIN currencies ON orders.currency_id = currencies.id
					INNER JOIN products ON products.id = op.product_id
					INNER JOIN payment_methods pm ON pm.id = orders.payment_method_id
					INNER JOIN chargebacks ON chargebacks.order_id = orders.id
						{$where_condition_string}
					GROUP BY opt.id
					" . ($having_condition ? " HAVING " . (implode(' AND ', $having_condition)) : "" ) . "
					ORDER BY orders.id";
			} else {
				$query = "SELECT
					'$name' AS store,
					pm.processor AS processor,
					pm.name AS pay_method,
  					TIMESTAMPDIFF(
					    DAY,
					    (SELECT customers.registered FROM customers WHERE customers.id = orders.customer_id),
				  		orders.date
					) AS account_age,
					orders.capture_date,
					orders.ip_country_code AS order_country_code,
					orders.total * orders.currency_rate as order_value,
					orders.total as order_value_initial,
					orders.cc_issuing_bank AS issuing_bank,
					currencies.code AS currency_code,
					IF (
						orders.cc_number IS NULL OR orders.cc_number = '', 'PayPal',
						IFNULL(orders.cc_card_brand, 'Unknown')
					) AS cc_type,
					chargebacks.chargeback_reason_id AS reason_code,
					chargebacks.chargeback_reason_id,
					{$reason_code_categories_select} AS reason_code_category,
					orders.cc_country_code,
					orders.cc_number,
					chargebacks.ticket_id,
					(
						SELECT
							GROUP_CONCAT(DISTINCT products.type)
						FROM order_products
						INNER JOIN products ON products.id = order_products.product_id
						WHERE order_products.order_id IN (orders.id)
					) AS product_type,
					" . (
						($mr_country_code || $group_by == 'mr_country_code') ?
						"(SELECT
							SUBSTR(
								order_products.data,
								LOCATE('country_code\":\"', order_products.data) + 15,
								LOCATE('\"', order_products.data, LOCATE('country_code\":\"', order_products.data) + 15)
									- LOCATE('country_code\":\"', order_products.data) - 15
							)
							FROM order_products, products
							WHERE
								order_products.order_id = orders.id
								AND order_products.product_id = products.id
								AND products.type IN ('mobile_recharge', 'mobile_recharge_charge')
								" . ($mr_country_code ? " AND order_products.data LIKE 
								" . $dbi_stores_report->escape('%"country_code":"'
										. strtoupper($mr_country_code) . '"%')  : "" ) . "
							LIMIT 1
						) AS mr_country_code," : ""
					) . "

					chargebacks.type AS chargeback_type,
					(
						SELECT
							MAX(order_transactions.date)
						FROM order_transactions
						WHERE order_transactions.chargeback_id = chargebacks.id
						AND order_transactions.type = 'chargeback'
						ORDER BY order_transactions.date DESC
					) AS last_financial_action,
					ot.date AS financial_action_date,
					CONCAT(orders.bill_first_name, ' ', orders.bill_last_name) AS customer_name,
					CONCAT('S',(SELECT value FROM store_settings WHERE parameter = 'store_id'), 'O', orders.id) AS identifier,
					(select value from store_settings where parameter = 'store_id') as store_id,
					(CASE
						WHEN orders.3dsecure_verified = 'X' THEN 'Requested but not enrolled'
						WHEN orders.3dsecure_verified = 'Y' THEN 'Match'
						WHEN orders.3dsecure_verified = 'N' THEN 'No Match'
						WHEN orders.3dsecure_verified = 'R' THEN 'Abandoned'
						ELSE '-'
					END
					) AS verify_3ds,

					(" . ($chargeback_last_modified_start
					&& ($group_by == 'payments_user/chargeback_source/charge_type') ? "
						chargebacks.last_modified_user" : " chargebacks.created_by ") . "
					) AS payments_user,
					(SELECT
						chargebacks.last_modified_user
					FROM chargebacks c
					WHERE
						c.order_id = orders.id
					ORDER BY c.last_modified_date DESC
					LIMIT 1) AS last_payments_user,
					MAX(chargebacks.dispute_date) AS dispute_date,
					MAX(chargebacks.last_modified_date) AS last_modified_date,
					MAX(chargebacks.response_due_date) AS response_due_date,
					chargebacks.merchant_case_number,
					chargebacks.merchant_name AS chargeback_source,
					orders.id AS order_id,
					SUM(IF(orders.bill_status IN ('refund_ok', 'void_ok'),
						((opt.quantity * opt.price + opt.processing_fee) / total * refund_amount) * orders.currency_rate , 0)
						) AS order_refunded_amount,
					(" . ($chargeback_last_modified_start
					&& ($group_by == 'payments_user/chargeback_source/charge_type') ? "
						chargebacks.last_modified_user" : " chargebacks.created_by ") . "
					) AS payments_user,
					IF (TIMESTAMPDIFF(DAY, chargebacks.dispute_date, chargebacks.created_at),
						AVG(TIMESTAMPDIFF(DAY, chargebacks.dispute_date, chargebacks.created_at)),
						NULL) AS gap_creation_dispute,
					IF (TIMESTAMPDIFF(DAY, orders.capture_date, chargebacks.dispute_date),
						AVG(TIMESTAMPDIFF(DAY, orders.capture_date, chargebacks.dispute_date))
					, NULL) AS gap_capture_dispute,
					IF (TIMESTAMPDIFF(DAY, orders.capture_date, chargebacks.created_at),
						AVG(TIMESTAMPDIFF(DAY, orders.capture_date, chargebacks.created_at))
					, NULL) AS gap_capture_created_at,
					chargebacks.created_at as created_at,
					chargebacks.case_type as charge_type,
					chargebacks.status AS chargeback_status,
					chargebacks.resolution AS resolution,
					orders.customer_id,
					IF(LENGTH(orders.cc_number) > 1, LEFT(orders.cc_number, 6) , 'PayPal') as bin,
					GROUP_CONCAT(IF( ot.type = 'chargeback', opt.id, 0)) as order_transaction_ids,
					chargebacks.id as chargeback_id,
					SUM(opt.total_tax) as tax,
					MAX(ABS(ot.chargeback_amount * ot.chargeback_currency_rate)) as transactions_amount_max,
					SUM((opt.quantity * opt.price + opt.processing_fee) * orders.currency_rate) AS transactions_amount,
					SUM(ot.chargeback_amount * ot.chargeback_currency_rate) / (COUNT(DISTINCT opt.id) / COUNT(DISTINCT ot.id))
						AS financial_action_total,
					" . ($fraud_review_data) . " AS fraud_review_risk_category
				FROM chargebacks
				INNER JOIN orders ON orders.id = chargebacks.order_id
				INNER JOIN currencies ON orders.currency_id = currencies.id
				INNER JOIN payment_methods pm ON pm.id = orders.payment_method_id
				LEFT JOIN order_transactions ot ON (ot.chargeback_id = chargebacks.id AND ot.result = 'successful')
				LEFT JOIN order_products_transactions opt ON opt.order_transaction_id = ot.id
				{$fraud_review_risk_category_link}
					{$where_condition_string}
					GROUP BY chargebacks.id
					" . ($having_condition ? " HAVING " . (implode(' ', $having_condition)) : "" ) . "
				";
			}
			$details = $store_conn->queryRows($query);
			// discard second chargeback record, system lack business logic to have multiple chargebacks on the same order
			$identifiers = [];
			foreach ($details as $key => &$detail) {
				$chargeback_id_field = isset($detail['cb_id']) ?  'cb_id' : 'chargeback_id';
				if (!isset($identifiers[$detail['identifier']])) {
					$identifiers[$detail['identifier']] = $detail[$chargeback_id_field];
				} elseif ($identifiers[$detail['identifier']] != $detail[$chargeback_id_field]) {
					unset($details[$key]);
				}
			}
			unset($detail);
			unset($identifiers);
			// end discard second chargeback
			$dbi_stores_report->insertBulk("tmp_portal_chargebacks", $details);
		} else {
			$response_due_date = date('Y-m-d');
			$response_due_date_start = date('Y-m-d 00:00:00', strtotime($response_due_date));
			$response_due_date_end = date('Y-m-d 23:59:59', strtotime("+1 month", strtotime($response_due_date)));

			$response_due_date_start_min = date('Y-m-d 00:00:00', strtotime($response_due_date));
			$first_date_of_this_year =
				date('Y-m-d 00:00:00', strtotime("first day of January" . date('Y', strtotime($response_due_date))));

			$query = "SELECT
				/*Total Cases To Be Attended*/
				(SELECT
					COUNT(*)
				FROM
					chargebacks
				WHERE
					status = 'open'
					AND last_modified_date is NULL
					AND merchant_name != 'paypal'
				) as count_total_cases,
				(SELECT
					 SUM(orders.total * orders.currency_rate)
				FROM
					chargebacks
				INNER JOIN orders ON orders.id = chargebacks.order_id
				WHERE
					chargebacks.status = 'open'
					AND chargebacks.merchant_name != 'paypal'
					AND last_modified_date is NULL
				) as amount_total_cases,
				/*Cases due in the next 5/10/30 days*/
				COUNT(IF(first AND last_modified_date is NULL AND response_due_date < '{$response_due_date_start_min}'
					+ INTERVAL 5 DAY, 1, NULL)) as 'count_cases_due_5',
				COUNT(IF(first AND last_modified_date is NULL AND response_due_date < '{$response_due_date_start_min}'
					+ INTERVAL 10 DAY, 1, NULL)) as 'count_cases_due_10',
				COUNT(IF(first AND last_modified_date is NULL AND response_due_date < '{$response_due_date_start_min}'
					+ INTERVAL 30 DAY, 1, NULL)) as 'count_cases_due_30',
				SUM(IF(first AND last_modified_date is NULL AND response_due_date < '{$response_due_date_start_min}'
					+ INTERVAL 5 DAY, order_value, 0)) as 'amount_cases_due_5',
				SUM(IF(first AND last_modified_date is NULL AND response_due_date <= '{$response_due_date_start_min}'
					+ INTERVAL 10 DAY, order_value, 0)) as 'amount_cases_due_10',
				SUM(IF(first AND  last_modified_date is NULL AND response_due_date <= '{$response_due_date_start_min}'
					+ INTERVAL 30 DAY, order_value, 0)) as 'amount_cases_due_30',
				/*Cases Attended Prior day/week*/
				COUNT(IF(!first AND last_modified_date >= '{$response_due_date_start}' - INTERVAL 1 DAY, 1, NULL))
					as 'count_cases_attended_day',
				COUNT(IF(!first AND last_modified_date >= '{$response_due_date_start}' - INTERVAL 7 DAY, 1, NULL))
					as 'count_cases_attended_week',
				SUM(IF(!first AND last_modified_date >= '{$response_due_date_start}' - INTERVAL 1 DAY, order_value, 0))
					as 'amount_cases_attended_day',
				SUM(IF(!first AND last_modified_date >= '{$response_due_date_start}' - INTERVAL 7 DAY, order_value, 0))
					as 'amount_cases_attended_week',
				/*Cases Attended YTD*/
				(SELECT COUNT(*) FROM chargebacks
					WHERE
						status = 'open'
						AND last_modified_date >= '{$first_date_of_this_year}'
				) as 'count_cases_attended_ytd',
				(SELECT SUM(o.total * o.currency_rate) FROM chargebacks c, orders o
					WHERE
					o.id = c.order_id
					AND c.status = 'open'
					AND c.last_modified_date >= '{$first_date_of_this_year}'
				) as 'amount_cases_attended_ytd',
				/*Cases Created YTD/Dispute Date*/
				(SELECT COUNT(*) FROM chargebacks
					WHERE dispute_date >= '{$first_date_of_this_year}'
						AND dispute_date <= '{$response_due_date_start}'
				) as 'count_cases_created_ytd',
				(SELECT SUM(o.total * o.currency_rate) FROM chargebacks c, orders o
					WHERE
					o.id = c.order_id
					AND c.dispute_date >= '{$first_date_of_this_year}'
					AND c.dispute_date <= '{$response_due_date_start}'
				) as 'amount_cases_created_ytd'
				FROM
				(SELECT
					orders.total * orders.currency_rate as order_value,
					chargebacks.last_modified_date as last_modified_date,
					chargebacks.response_due_date as response_due_date,
					chargebacks.created_at,
					1 as first
				FROM
					chargebacks
				INNER JOIN orders ON orders.id = chargebacks.order_id
				WHERE
					response_due_date >= '{$response_due_date_start}' AND response_due_date <= '{$response_due_date_end}'
					AND merchant_name != 'paypal'
				UNION ALL
				SELECT
					orders.total * orders.currency_rate as order_value,
					chargebacks.last_modified_date as last_modified_date,
					chargebacks.response_due_date as response_due_date,
					chargebacks.created_at,
					0 as first
				FROM
					chargebacks
				INNER JOIN orders ON orders.id = chargebacks.order_id
				WHERE
					chargebacks.status = 'open'
					AND last_modified_date >= '{$response_due_date_start}' - INTERVAL 7 DAY
					AND last_modified_date <= '{$response_due_date_start}'
				) as tmp
			";
			$result = $store_conn->queryRows($query);
			if ($result) {
				foreach ($result as $row) {
					$rows[] = $row;
				}
			}
		}
	}

	if ($group_by != 'chargeback_summary') {
		if ($group_by == 'product_type' || $prod_type || $group_by == 'financial_action_date/product_type') {
			$q = "
				SELECT
					opt_id,
					order_product_id,
					order_transaction_id,
					cb_id,
					store,
					cc_country_code,
					account_age,
					capture_date,
					cc_number,
					cb_id AS id,
					order_country_code,
					verify_3ds,
					product_type,
					product_code,
					mr_country_code,
					customer_id,
					identifier,
					customer_name,
					order_refunded,
					order_refunded_amount,
					cc_type,
					pay_method,
					processor,
					gap_creation_dispute,
					gap_capture_dispute,
					gap_capture_created_at,
					ticket_id,
					merchant_case_number,
					dispute_date,
					last_modified_date,
					response_due_date,
					created_at,
					order_id,
					reason_code,
					reason_code_category,
					chargeback_reason_id,
					last_financial_action,
					financial_action_date,
					" . ($group_by == 'financial_action_date/product_type' ?
						"CONCAT(financial_action_date, product_type) AS financial_action_date_product_type,"
						: ""
					) . "
					currency_code,
					chargeback_source,
					chargeback_type,
					payments_user,
					last_payments_user,
					chargeback_status,
					resolution,
					charge_type,
					bin,
					issuing_bank,
					store_id,
					order_transaction_ids,
					all_transaction_types,
					transaction_type,
					order_value AS order_value,
					order_value_initial AS order_value_initial,
					tax AS tax,
					transactions_amount AS transactions_amount,
					transactions_amount_max AS transactions_amount_max,
					order_total_value,
					financial_action_total,
					bank_action_total,
					order_product_amount,
					order_product_amount_initial,
					all_products_ids_nr
				FROM tmp_portal_chargebacks
				GROUP BY opt_id
				ORDER BY order_id, opt_id
			";
		} else {
			$q = "
				SELECT
					store,
					cc_country_code,
				    account_age,
					capture_date,
					cc_number,
					SUM(order_value) AS order_value,
					SUM(order_value_initial) AS order_value_initial,
					order_country_code,
					verify_3ds,
					customer_id,
					product_type,
					identifier,
					customer_name,
					" . (
					($mr_country_code || $group_by == 'mr_country_code') ? "mr_country_code," : ""
					) . "
					order_refunded,
					SUM(order_refunded_amount) AS order_refunded_amount,
					cc_type,
					pay_method,
					processor,
					gap_creation_dispute,
					gap_capture_dispute,
					gap_capture_created_at,
					ticket_id,
					merchant_case_number,
					dispute_date,
					last_modified_date,
					response_due_date,
					created_at,
					order_id,
					reason_code,
					reason_code_category,
					chargeback_reason_id,
					financial_action_date,
					" . ($group_by == 'financial_action_date/product_type'
						? "CONCAT(financial_action_date, product_type) AS last_financial_action,"
						: "last_financial_action,"
					) . "
					currency_code,
					chargeback_source,
					chargeback_type,
					payments_user,
					last_payments_user,
					chargeback_status,
					resolution,
					charge_type,
					bin,
					issuing_bank,
					store_id,
					order_transaction_ids,
					chargeback_id,
					" . (($group_by == 'identifier')
						? 'COUNT(DISTINCT chargeback_id) AS id,' : "COUNT(DISTINCT identifier) AS id," ) . "
					SUM(tax) AS tax,
					SUM(transactions_amount) AS transactions_amount,
					SUM(transactions_amount_max) AS transactions_amount_max,
					SUM(financial_action_total) AS financial_action_total,
					fraud_review_risk_category
				FROM tmp_portal_chargebacks
				$group_by_string
			";
		}
		$result = $dbi_stores_report->queryRows($q);
	}

	if ($group_by != 'chargeback_summary') {
		if ($group_by == 'product_type' || $prod_type || $group_by == 'financial_action_date/product_type') {
			$identifiers = [];
			$final_res = [];
			if ($group_by == '') {
				$group_by = 'identifier';
			}
			$group = str_replace('/', '_', $group_by);
			$recalculate_fields = [];
			$order_total = [];
			$transactions_amount_max = [];
			$order_total_value = [];
			$order_products = [];
			$processed_products = [];
			foreach ($result as $rk => $row) {
				$keys = array_keys($identifiers);
				if (!in_array($row['identifier'], $keys)) {
					$identifiers[$row['identifier']] = [
						'cb_id' => $row['cb_id'],
						'o_id' => $row['order_id']
					];
				}

				if (!isset($row[$group])) {
					$exp = explode('/', $group_by);
					$names = [];
					foreach ($exp as $e) {
						$names[] = $row[$e];
					}
					$row[$group] = implode('_', $names);
				}

				// assign initial values (the ones we do not need to recalculate)
				if (!isset($final_res[$row[$group]])) {
					$final_res[$row[$group]] = $row;
					$final_res[$row[$group]]['tax'] = 0;
					$final_res[$row[$group]]['transactions_amount'] = 0;
					$final_res[$row[$group]]['transactions_amount_max'] = 0;
					$final_res[$row[$group]]['financial_action_total'] = 0;
					$final_res[$row[$group]]['bank_action_total'] = 0;
					$final_res[$row[$group]]['order_value'] = 0;
					$final_res[$row[$group]]['order_value_initial'] = 0;
					$final_res[$row[$group]]['order_product_amount'] = 0;
					$final_res[$row[$group]]['order_product_amount_initial'] = 0;
					$final_res[$row[$group]]['order_transaction_ids'] = '';
					$final_res[$row[$group]]['id'] = 0;
					if (array_key_exists($row['payments_user'], $portal_users)) {
						$final_res[$row[$group]]['payments_user'] = $portal_users[$row['payments_user']]['login'];
					}

					if (array_key_exists($row['last_payments_user'], $portal_users)) {
						$final_res[$row[$group]]['last_payments_user'] = $portal_users[$row['last_payments_user']]['login'];
					}

					if (array_key_exists($row['reason_code'], $chargebacks_reasons)) {
						$reason_code = $row['reason_code'];
						$final_res[$row[$group]]['reason_code'] = $chargebacks_reasons[$reason_code]['description'];
						$final_res[$row[$group]]['reason_code_category'] = $chargebacks_reasons[$reason_code]['category'];
					}
				}
				if (empty($final_res[$row[$group]]['order_transaction_ids'])) {
					$final_res[$row[$group]]['order_transaction_ids'] = $row['order_transaction_ids'];
				} else {
					$final_res[$row[$group]]['order_transaction_ids'] .= ',' . $row['order_transaction_ids'];
				}
				if (!isset($identifiers[$row['identifier']]['order_product_id'])) {
					$identifiers[$row['identifier']]['order_product_id'] = [];
				}
				if ($group_by == 'product_type' || $prod_type || $group_by == 'financial_action_date/product_type') {
					if (!in_array($row['order_product_id'], $identifiers[$row['identifier']]['order_product_id'])) {
						$identifiers[$row['identifier']]['order_product_id'][] = $row['order_product_id'];
						if (strpos($row['product_code'], 'BYOD') === false) {
							$final_res[$row[$group]]['id'] += 1;
						}
					}
				} else {
					$final_res[$row[$group]]['id'] += 1;
				}

				$transactions_amount_max[$row['order_id']] = $row['transactions_amount_max'] ?? 0;
				$order_total_value[$row['order_id']] = $row['order_total_value'] ?? 0;

				if ($row['transaction_type'] == 'chargeback') {
					// chargeback totals
					if (!empty($row['financial_action_total'])) {
						if (!isset($identifiers[$row['identifier']]['bank_action_total'])) {
							$identifiers[$row['identifier']]['bank_action_total'] = $row['bank_action_total'];
							$final_res[$row[$group]]['bank_action_total'] += $row['bank_action_total'];
						}
					}
					$final_res[$row[$group]]['tax'] += $row['tax'];
					$final_res[$row[$group]]['transactions_amount'] += $row['transactions_amount'];
					// calc financial_action_total
					if (
						strpos($row['all_transaction_types'], 'capture') === false
						&& strpos($where_condition_string, 'ot.date') !== false
					) {
						$recalculate_fields[] = [
							'rk' => $rk,
							'group' => $group,
							'order_id' => $row['order_id'],
							'order_product_id' => $row['order_product_id'],
						];

						if (!isset($order_total[$row['order_id']])) {
							$order_total[$row['order_id']] = 0;
							$order_products[$row['order_id']] = [];
						}

						if (
							!in_array($row['order_product_id'], $order_products[$row['order_id']])
							&& $row['order_product_amount'] != 0
						) {
							$order_total[$row['order_id']] += $row['order_product_amount'];
							$order_products[$row['order_id']][] = $row['order_product_id'];
						}

						//$final_res[$row[$group]]['order_value_initial'] += $row['order_product_amount_initial'];
					} else {
						if (!isset($identifiers[$row['identifier']]['order_value'])) {
							$identifiers[$row['identifier']]['order_value'] = $row['order_value'];
						}
						$financial_action_total =
							calculate_partial_action_total($row, $identifiers[$row['identifier']]['order_value']);
						$final_res[$row[$group]]['financial_action_total'] += $financial_action_total;
						// exclude products processed
						if (!in_array($row['order_product_id'], $processed_products)) {
							// calculate from chargeback max value proportional amount for each product.
							$transactions_amount_max_value =
								calculate_partial_action_total($row, $row['transactions_amount_max'], 'transactions_amount_max');
							$final_res[$row[$group]]['transactions_amount_max'] += $transactions_amount_max_value;
							$processed_products[] = $row['order_product_id'];
						}
					}
				} elseif ($row['transaction_type'] == 'capture') {
					if (
						strpos($row['all_transaction_types'], 'chargeback') === false
						&& strpos($where_condition_string, 'ot.date') !== false
					) {
						continue;
					}
					if (!empty($row['order_value'])) {
						if (!isset($identifiers[$row['identifier']]['order_value'])) {
							$identifiers[$row['identifier']]['order_value'] = $row['order_value'];
						}
						$financial_action_total =
							calculate_partial_action_order_total($row, $identifiers[$row['identifier']]['order_value']);
						$final_res[$row[$group]]['order_value'] += $financial_action_total;
						//$final_res[$row[$group]]['order_value'] += $row['order_product_amount'];
						$final_res[$row[$group]]['order_value_initial'] += $row['order_product_amount_initial'];
					}
				}
			}
			// keep this lines for easy debugging.
			//print_r($order_total_value);
			//print_r($transactions_amount_max);
			//print_r($result);
			$processed_products = [];
			foreach ($recalculate_fields as $rf) {
				$financial_action_total = calculate_partial_action_total($result[$rf['rk']], $order_total[$rf['order_id']]);
				$final_res[$result[$rf['rk']][$rf['group']]]['financial_action_total'] += $financial_action_total;
				// exclude products processed
				if (!in_array($rf['order_product_id'], $processed_products)) {
					// calculate from chargeback max value proportional amount for each product.
					$order_value = calculate_partial_action_total(
						$result[$rf['rk']],
						$order_total_value[$rf['order_id']],
						'order_total_value'
					);
					$final_res[$result[$rf['rk']][$rf['group']]]['order_value'] += $order_value;

					$transactions_amount_max_value = calculate_partial_action_total(
						$result[$rf['rk']],
						$order_total_value[$rf['order_id']],
						'transactions_amount_max'
					);

					$present_products = count($order_products[$rf['order_id']]);

					// ?when other chargeback products are missing then > $transactions_amount_max[$rf['order_id']]
					if ($present_products == $result[$rf['rk']]['all_products_ids_nr']) {
						$final_res[$result[$rf['rk']][$rf['group']]]['transactions_amount_max'] += $transactions_amount_max_value;
					} else {
						$final_res[$result[$rf['rk']][$rf['group']]]['transactions_amount_max']
							+= $transactions_amount_max[$rf['order_id']];
						// hide column order_value column because products are missing and calculation is wrong.
						$report->removeColumn('order_value');
					}

					$processed_products[] = $rf['order_product_id'];
				}
			}

			$report->addRows($final_res);
		} else {
			foreach ($result as $res) {
				if (!$group_by) {
					if (isset($result_grouped[$res['id']])) {
						$order = array_merge($result_grouped[$res['id']], $res);
					}
				}
				// Group rows by key for determining totals
				$group_key = "";
				foreach ($grouping as $group_column) {
					$group_key .= $group_sep . strtolower($res[$group_column] ?? '');
				}
				// Group rows by key
				$grouped_rows[$group_key][] = $res;
			}

			if (isset($grouped_rows) && is_array($grouped_rows) && count($grouped_rows) > 0) {
				ksort($grouped_rows, SORT_STRING);
				$grouped_rows = regroup_rows($grouped_rows);

				//compute the totals
				$total_unfiltered_orders_count = $total_unfiltered_orders_value_amount = 0;
				if (isset($rows_totals) && is_array($rows_totals)) {
					foreach ($rows_totals as $index => $row) {
						$total_unfiltered_orders_count += $row['orders_counter'];
						$total_unfiltered_orders_value_amount += $row['orders_value'];
					}
				}

				$chargebacks_counter = $chargebacks_value_total = $orders_refunded_count = $orders_refunded_value = 0;
				foreach ($grouped_rows as $group_index => $rows) {
					foreach ($rows as $index => &$current_row) {
						$current_row['chargebacks_percent'] =
							$current_row['id'] / ($total_unfiltered_orders_count == 0 ? 1 : $total_unfiltered_orders_count);
						$current_row['order_value_percent'] =
							$current_row['order_value'] / ($total_unfiltered_orders_value_amount == 0
								? 1
								: $total_unfiltered_orders_value_amount);
						$current_row['order_refunded_percent'] =
							$current_row['order_refunded'] / ($total_unfiltered_orders_count == 0
								? 1
								: $total_unfiltered_orders_count);
						$current_row['order_refunded_amount_percent'] = $current_row['order_refunded_amount'] /
							($total_unfiltered_orders_value_amount == 0 ? 1 : $total_unfiltered_orders_value_amount);

						$chargebacks_counter += $current_row['id'];
						$chargebacks_value_total += $current_row['order_value'];
						$orders_refunded_count += $current_row['order_refunded'];
						$orders_refunded_value += $current_row['order_refunded_amount'];
						// fix the column name.
						if (!isset($current_row['order_product_amount_initial'])) {
							$current_row['order_product_amount_initial'] = $current_row['order_value_initial'];
						} else {
							$current_row['order_product_amount_initial'] += $current_row['order_value_initial'];
						}
					}
					unset($current_row);

					foreach ($rows as &$order) {
						if (array_key_exists($order['payments_user'], $portal_users)) {
							$order['payments_user'] = $portal_users[$order['payments_user']]['login'];
						}

						if (array_key_exists($order['last_payments_user'], $portal_users)) {
							$order['last_payments_user'] = $portal_users[$order['last_payments_user']]['login'];
						}

						if (array_key_exists($order['reason_code'], $chargebacks_reasons)) {
							$reason_code = $order['reason_code'];
							$order['reason_code'] = $chargebacks_reasons[$reason_code]['description'];
							$order['reason_code_category'] = $chargebacks_reasons[$reason_code]['category'];
						}
					}
					unset($order);
					$report->addRows($rows);
				}
			}
		}

		$report->addSummary(array(
			"ticket_id" => "count",
			"id" => "sum",
			"chargebacks_percent" => "label: ",
			"order_value" => "sum",
			"order_product_amount_initial" => "sum",
			"order_value_percent" => "label: ",
			"order_refunded" => "sum",
			"order_refunded_percent" => "label: ",
			"order_refunded_amount" => "sum",
			"order_refunded_amount_percent" => "label: ",
			"gap_creation_dispute" => "avg",
			"gap_capture_dispute" => "avg",
			"gap_capture_created_at" => "avg",
			"transactions_amount" => "sum",
			"transactions_amount_max" => "sum",
			"tax" => "sum",
			"financial_action_total" => "sum"
		));
	} elseif ($rows) {
		$res = [
			// Total Cases To Be Attended
			'count_total_cases' => 0,
			'amount_total_cases' => 0,
			// Cases due in the next 5/10/30 days
			'count_cases_due_5' => 0,
			'count_cases_due_10' => 0,
			'count_cases_due_30' => 0,
			'amount_cases_due_5' => 0,
			'amount_cases_due_10' => 0,
			'amount_cases_due_30' => 0,
			// Cases Attended Prior day/week
			'count_cases_attended_day' => 0,
			'count_cases_attended_week' => 0,
			'amount_cases_attended_day' => 0,
			'amount_cases_attended_week' => 0,
			//Cases Attended YTD
			'count_cases_attended_ytd' => 0,
			'amount_cases_attended_ytd' => 0,
			//Cases Created YTD/Dispute Date
			'count_cases_created_ytd' => 0,
			'amount_cases_created_ytd' => 0,
		];

		foreach ($rows as $row) {
			foreach ($row as $key => $value) {
				$res[$key] += $value;
			}
		}
		//round values
		foreach ($res as &$value) {
			$value = round($value, 2);
		}
		unset($value);

		$finnal_rows = [
			[
				'key_metrics' => 'Total Cases To Be Attended',
				'count' => $res['count_total_cases'],
				'amount' => round($res['amount_total_cases'], 2),
			],
			[
				'key_metrics' => 'Cases due in the next 5/10/30 days',
				'count' => $res['count_cases_due_5']
					. ' / '
					. $res['count_cases_due_10']
					. ' / '
					. $res['count_cases_due_30'],
				'amount' => $res['amount_cases_due_5']
					. ' / '
					. $res['amount_cases_due_10']
					. ' / '
					. $res['amount_cases_due_30'],
			],
			[
				'key_metrics' => 'Cases Attended Prior day/week',
				'count' => $res['count_cases_attended_day'] . ' / ' . $res['count_cases_attended_week'],
				'amount' => $res['amount_cases_attended_day'] . ' / ' . $res['amount_cases_attended_week']
			],
			[
				'key_metrics' => 'Cases Attended YTD',
				'count' => $res['count_cases_attended_ytd'],
				'amount' => $res['amount_cases_attended_ytd'],
			],
			[
				'key_metrics' => 'Cases Created YTD/Dispute Date',
				'count' => $res['count_cases_created_ytd'],
				'amount' => $res['amount_cases_created_ytd'],
			],
		];
		$report->addRows($finnal_rows);
	}
} else {
	//add dummy column
	$report->addColumn('dummy', 'dummy', 'string');
}

$report->setButton('CSVReportOutputCustom', 'Download Intact CSV');
$report->setFooter("<!--Submit form script-->
	<script language=\"javascript\">
	function show_hide_fields() {

		$('[name=product_name_credit]').closest('tr').hide();
		$('[name=product_name_mobile_recharge]').closest('tr').hide();
		$('[name=product_name_nauta]').closest('tr').hide();
		$('[name=country_tariff]').closest('tr').hide();
		$('[name=country_vn]').closest('tr').hide();
		$('[name=country_fax]').closest('tr').hide();
		$('[name=product_name_membership]').closest('tr').hide();
		$('[name=product_mobile_credit]').closest('tr').hide();
		$('[name=product_mobile_voice]').closest('tr').hide();
		$('[name=product_mvno_sms]').closest('tr').hide();
		$('[name=mobile_data_bundle]').closest('tr').hide();
		$('[name=product_mvno_device]').closest('tr').hide();
		$('[name=product_mvno_sim]').closest('tr').hide();
		$('[name=product_mvno_msisdn]').closest('tr').hide();
		$('[name=product_mvno_supplier]').closest('tr').hide();
		$('[name=product_shipping]').closest('tr').hide();
		$('[name=mobile_pack]').closest('tr').hide();
		$('[name=product_rate_plan]').closest('tr').hide();
		$('[name=\"_list\"').prop('disabled', true);
		
		if ($(\"input[name='product[]'][value=credit]:checked\").val() == 'credit') {
			$('[name=product_name_credit]').closest('tr').show();
		}
		if ($(\"input[name='product[]'][value=mobile_recharge]:checked\").val() == 'mobile_recharge') {
			$('[name=product_name_mobile_recharge]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=nauta]:checked\").val() == 'nauta') {
			$('[name=product_name_nauta]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=tariff_plan]:checked\").val() == 'tariff_plan') {
			$('[name=country_tariff]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=vn]:checked\").val() == 'vn') {
			$('[name=country_vn]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=fax]:checked\").val() == 'fax') {
			$('[name=country_fax]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=credit_subscription]:checked\").val() == 'credit_subscription') {
			$('[name=product_name_membership]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=mobile_credit]:checked\").val() == 'mobile_credit') {
			$('[name=product_mobile_credit]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=mobile_voice]:checked\").val() == 'mobile_voice') {
			$('[name=product_mobile_voice]').closest('tr').show();
		}
		if ($(\"input[name='product[]'][value=mobile_sms]:checked\").val() == 'mobile_sms') {
			$('[name=product_mvno_sms]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=mobile_data]:checked\").val() == 'mobile_data') {
			$('[name=mobile_data_bundle]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=mobile_device]:checked\").val() == 'mobile_device') {
			$('[name=product_mvno_device]').closest('tr').show();
		}
		
		if ($(\"input[name='product[]'][value=mobile_sim]:checked\").val() == 'mobile_sim') {
			$('[name=product_mvno_sim]').closest('tr').show();
		}
		
		if ($(\"input[name='product[]'][value=mobile_msisdn]:checked\").val() == 'mobile_msisdn') {
			$('[name=product_mvno_msisdn]').closest('tr').show();
			$('[name=product_mvno_supplier]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=shipping]:checked\").val() == 'shipping') {
			$('[name=product_shipping]').closest('tr').show();
		}

		if ($(\"input[name='product[]'][value=mobile_pack]:checked\").val() == 'mobile_pack') {
			$('[name=mobile_pack]').closest('tr').show();
		}
		
		if ($(\"input[name='product[]'][value=rate_plan]:checked\").val() == 'rate_plan') {
			$('[name=product_rate_plan]').closest('tr').show();
		}
	}
	$(document).ready(show_hide_fields);
	$(\"[name='product[]']\").change(show_hide_fields);
	</script>
");
$report->run();

function regroup_rows($grouped_rows)
{
	global $summaries;

	$data = $grouped_rows;
	foreach ($data as $group_key => $group_values) {
		if ($summaries) {
			// apply each summarization function to its corresponding column
			foreach ($summaries as $summary_column => $function) {
				switch ($function) {
					case 'count':
						$value = count($group_values);
						break;
					case 'sum':
						$value = 0;
						foreach ($group_values as $row) {
							$value += $row[$summary_column];
						}
						break;
					case 'avg':
						$value = 0;
						foreach ($group_values as $row) {
							$value += $row[$summary_column];
						}
						$value = count($group_values) > 0 ? $value / count($group_values) : 0;
						break;
					default:
						continue 2;
				}

				$data[$group_key][0][$summary_column] = $value;
			}

			// collapse multiple values for non-summarized columns (pick the first value)
			if (count($data[$group_key]) > 1) {
				array_splice($data[$group_key], 1);
			}
		}
	}
	return $data;
}

function calculate_partial_action_total($row, $order_total, $col = 'financial_action_total')
{
	if (empty((float) $order_total)) {
		return 0;
	}
	if (empty((float) $row['order_product_amount'])) {
		return 0;
	}
	bcscale(20);
	$order_product_percent = bcdiv(bcmul($row['order_product_amount'], '100'), (string) $order_total);

	$partial_amount = bcdiv(bcmul($row[$col], $order_product_percent), '100');

	return ((float) $partial_amount);
}

function calculate_partial_action_order_total($row, $order_total)
{
	if (empty($row['order_product_amount'])) {
		return 0;
	}
	bcscale(20);
	$order_product_percent = bcdiv(bcmul($row['order_product_amount'], '100'), (string) $order_total);

	$partial_amount = bcdiv(bcmul($row['order_value'], $order_product_percent), '100');

	return ((float) $partial_amount);
}
