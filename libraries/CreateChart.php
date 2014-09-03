<?php
require_once 'libchart/classes/libchart.php';

use models\Order as Order;

$chart = new VerticalBarChart();

$dataSet = new XYDataSet();
foreach(Order::sumTotalOrder() as $row) {
	$dataSet->addPoint(new Point("$row[order_year] - $row[order_month]", $row['sum_total']));
}
$chart->setDataSet($dataSet);

$chart->setTitle("Chart for Orders");
$chart->render("assets/img_public/OrderGraph.png");
?>