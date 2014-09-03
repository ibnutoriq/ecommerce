<?php
namespace controllers;

use libraries\Controller as Controller;
use models\Order as Order;

class SalesReportController extends Controller
{
	public function __construct()
	{

	}

	public function index($id = null)
	{
		parent::routes()->render('sales_report.twig', array(
				'app_base' => $this->appBase,
				'grand_total' => Order::grandTotalSalesReport(),
				'sales_report_page' => Order::salesReportPage($id),
				'results' => Order::salesReportPagination($id),
				'title' => 'Sales Report',
			));
	}

	public function indexByDate($id = null)
	{
		parent::routes()->render('sales_report.twig', array(
				'app_base' => $this->appBase,
				'grand_total' => Order::grandTotalSalesReportByDate(),
				'results' => Order::salesReportByDate(),
				'title' => 'Sales Report By Date',
			));
	}
}
?>