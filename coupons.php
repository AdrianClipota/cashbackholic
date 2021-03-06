<?php
/*******************************************************************\
 * CashbackEngine v3.0
 * http://www.CashbackEngine.net
 *
  * Copyright (c) 2010-2017 CashbackEngine Software. All rights reserved.
 * ------------ CashbackEngine IS NOT FREE SOFTWARE --------------
\*******************************************************************/

	session_start();
	require_once("inc/config.inc.php");
	require_once("inc/pagination.inc.php");

	$results_per_page = COUPONS_PER_PAGE;
	$cc = 0;


	////////////////// filter  //////////////////////
		if (isset($_GET['column']) && $_GET['column'] != "")
		{
			switch ($_GET['column'])
			{
				case "added": $rrorder = "c.added"; break;
				case "visits": $rrorder = "c.visits"; break;
				case "retailer_id": $rrorder = "c.retailer_id"; break;
				case "end_date": $rrorder = "c.end_date"; break;
				default: $rrorder = "c.added"; break;
			}
		}
		else
		{
			$rrorder = "c.added";
		}

		if (isset($_GET['order']) && $_GET['order'] != "")
		{
			switch ($_GET['order'])
			{
				case "asc": $rorder = "asc"; break;
				case "desc": $rorder = "desc"; break;
				default: $rorder = "desc"; break;
			}
		}
		else
		{
			$rorder = "desc";
		}
	//////////////////////////////////////////////////

	$exclusive_coupons_total = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE exclusive='1' AND status='active'"));
	$exclusive_coupons_total = $exclusive_coupons_total['total'];

	$expiring_coupons_total = mysqli_fetch_array(smart_mysql_query("SELECT COUNT(*) AS total FROM cashbackengine_coupons WHERE end_date!='0000-00-00 00:00:00' AND (end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY)) AND status='active'"));
	$expiring_coupons_total = $expiring_coupons_total['total'];


	if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) { $page = (int)$_GET['page']; } else { $page = 1; }
	$from = ($page-1)*$results_per_page;

	$where = " (start_date<=NOW() AND (end_date='0000-00-00 00:00:00' OR end_date > NOW())) AND status='active'";

	$query = "SELECT c.*, DATE_FORMAT(c.end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY $rrorder $rorder LIMIT $from, $results_per_page";
	$total_result = smart_mysql_query("SELECT * FROM cashbackengine_coupons WHERE $where ORDER BY title ASC");
	$total = mysqli_num_rows($total_result);

	$result = smart_mysql_query($query);
	$total_on_page = mysqli_num_rows($result);

	///////////////  Page config  ///////////////
	$PAGE_TITLE = CBE1_COUPONS_TITLE;

	require_once ("inc/header.inc.php");

?>

	<h1><?php echo CBE1_COUPONS_TITLE; ?></h1>

		<?php if ($total > 0) { ?>
		
			<ul class="nav nav-tabs">
				<li class="active"><a href="#all" data-toggle="tab"><span><?php echo CBE1_COUPONS_ALL; ?></span></a></li>
				<li><a href="#top-coupons" data-toggle="tab"><span><?php echo CBE1_COUPONS_POPULAR; ?></span></a></li>
				<?php if ($exclusive_coupons_total > 0) { ?><li><a href="#exclusive" data-toggle="tab"><span><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span></a></li><?php } ?>
				<li><a href="#latest" data-toggle="tab"><span><?php echo CBE1_COUPONS_LATEST; ?></span></a></li>
				<?php if ($expiring_coupons_total > 0) { ?><li><a href="#expiring" data-toggle="tab"><span><?php echo CBE1_COUPONS_EXPIRING; ?></span></a></li><?php } ?>
			</ul>

		<div class="tab-content">
			
		<div class="tab-pane fade active in" id="all">
			
		<div class="container">
		<div class="row browse_top">
		<div class="col-md-6">
			<div class="sortby">
				<form action="" id="form1" name="form1" method="get" class="form-inline">
					<span><?php echo CBE1_SORT_BY; ?>:</span>
					<select name="column" id="column" class="form-control" onChange="document.form1.submit()">
						<option value="added" <?php if ($_GET['column'] == "added") echo "selected"; ?>><?php echo CBE1_COUPONS_SDATE; ?></option>
						<option value="visits" <?php if ($_GET['column'] == "visits") echo "selected"; ?>><?php echo CBE1_COUPONS_SPOPULAR; ?></option>
						<option value="retailer_id" <?php if ($_GET['column'] == "retailer_id") echo "selected"; ?>><?php echo CBE1_COUPONS_SSTORE; ?></option>
						<option value="end_date" <?php if ($_GET['column'] == "end_date") echo "selected"; ?>><?php echo CBE1_COUPONS_SEND; ?></option>
					</select>
					<select name="order" id="order" class="form-control" onChange="document.form1.submit()">
						<option value="desc" <?php if ($_GET['order'] == "desc") echo "selected"; ?>><?php echo CBE1_SORT_DESC; ?></option>
						<option value="asc" <?php if ($_GET['order'] == "asc") echo "selected"; ?>><?php echo CBE1_SORT_ASC; ?></option>
					</select>
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
				</form>
			</div>
		</div>
		<div class="col-md-6 text-right">
			<div class="results">
				<?php echo CBE1_RESULTS_SHOWING; ?> <?php echo ($from + 1); ?> - <?php echo min($from + $total_on_page, $total); ?> <?php echo CBE1_RESULTS_OF; ?> <?php echo $total; ?>
			</div>
		</div>
		</div>
		</div>

			<div class="container">
			<div class="row">
				<?php while ($row = mysqli_fetch_array($result)) { $cc++; ?>
				<div class="item_coupon">
					<div class="col-xs-6 col-md-3 text-center hidden-xs">
						<?php if ($row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?>
						<div class="imagebox"><a href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>"><img src="<?php if (!stristr($row['image'], 'http')) echo SITE_URL."img/"; echo $row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $row['title']; ?>" title="<?php echo $row['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($row['retailer_id'], $row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</div>
					<div class="col-xs-9 col-md-6">
						<span class="coupon_name"><h3><?php echo $row['title']; ?></h3> <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row['coupon_id']; ?>" target="_blank"><?php echo $row['coupon_title']; ?></a></span>
						<?php echo ($row['visits'] > 0) ? "<span class='coupon_times_used'><sup>".$row['visits']." ".CBE1_COUPONS_TUSED."</sup></span>" : ""; ?>
						<br/>
						<?php if ($row['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($row['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
						<?php if ($row['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $row['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($row['time_left']); ?></span>
						<?php } ?>
					</div>
					<div class="col-xs-3 col-md-3 text-right">
						<?php if ($row['code'] != "") { ?><span class="coupon_code"><?php echo (HIDE_COUPONS == 0 || isLoggedIn()) ? $row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br><br><?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $row['retailer_id']; ?>&c=<?php echo $row['coupon_id']; ?>" target="_blank"><?php echo ($row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
					</div>
				</div>		
				<?php } ?>
				</div>
				</div>

				<?php echo ShowPagination("coupons",$results_per_page,"coupons.php?column=$rrorder&order=$rorder&","WHERE ".$where); ?>

		</div>


		<div class="tab-pane fade" id="top-coupons">
		<?php
				$cc = 0;
				// show most popular coupons //
				$top_query = "SELECT c.*, DATE_FORMAT(c.end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY c.visits DESC LIMIT $results_per_page";
				$top_result = smart_mysql_query($top_query);
				$top_total = mysqli_num_rows($top_result);

				if ($top_total > 0)
				{
			?>
				<div class="container">
				<div class="row">
				<?php while ($top_row = mysqli_fetch_array($top_result)) { $cc++; ?>
				<div class="item_coupon">
					<div class="col-xs-6 col-md-3 text-center hidden-xs">
						<?php if ($top_row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?>
						<div class="imagebox"><a href="<?php echo GetRetailerLink($top_row['retailer_id'], $tops_row['title']); ?>"><img src="<?php if (!stristr($top_row['image'], 'http')) echo SITE_URL."img/"; echo $top_row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $top_row['title']; ?>" title="<?php echo $top_row['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($top_row['retailer_id'], $tops_row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</div>
					<div class="col-xs-9 col-md-6">
						<span class="coupon_name"><h3><?php echo $top_row['title']; ?></h3> <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $top_row['retailer_id']; ?>&c=<?php echo $top_row['coupon_id']; ?>" target="_blank"><?php echo $top_row['coupon_title']; ?></a></span>
						<?php echo ($top_row['visits'] > 0) ? "<span class='coupon_times_used'><sup>".$top_row['visits']." ".CBE1_COUPONS_TUSED."</sup></span>" : ""; ?>
						<br/>
						<?php if ($top_row['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($top_row['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
						<?php if ($top_row['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $top_row['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($top_row['time_left']); ?></span>
						<?php } ?>
					</div>
					<div class="col-xs-3 col-md-3 text-right">
						<?php if ($top_row['code'] != "") { ?><span class="coupon_code"><?php echo (HIDE_COUPONS == 0 || isLoggedIn()) ? $top_row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br><br><?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $top_row['retailer_id']; ?>&c=<?php echo $top_row['coupon_id']; ?>" target="_blank"><?php echo ($top_row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
					</div>
				</div>
				<?php } ?>
				</div>
				</div>

				<?php }else{ ?>
					<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>
					<div class="sline"></div>
				<?php } ?>
		</div>


		<div class="tab-pane fade" id="latest">
		<?php
				$cc = 0;
				// show latest coupons //
				$last_query = "SELECT c.*, DATE_FORMAT(c.end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY c.added DESC LIMIT $results_per_page";
				$last_result = smart_mysql_query($last_query);
				$last_total = mysqli_num_rows($last_result);

				if ($last_total > 0)
				{
			?>
				<div class="container">
				<div class="row">
				<?php while ($last_row = mysqli_fetch_array($last_result)) { $cc++; ?>
				<div class="item_coupon">
					<div class="col-xs-6 col-md-3 text-center hidden-xs">
						<?php if ($last_row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?>
						<div class="imagebox"><a href="<?php echo GetRetailerLink($last_row['retailer_id'], $last_row['title']); ?>"><img src="<?php if (!stristr($last_row['image'], 'http')) echo SITE_URL."img/"; echo $last_row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $last_row['title']; ?>" title="<?php echo $last_row['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($last_row['retailer_id'], $last_row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</div>
					<div class="col-xs-9 col-md-6">
						<span class="coupon_name"><h3><?php echo $last_row['title']; ?></h3> <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $last_row['retailer_id']; ?>&c=<?php echo $last_row['coupon_id']; ?>" target="_blank"><?php echo $last_row['coupon_title']; ?></a></span>
						<?php echo ($last_row['visits'] > 0) ? "<span class='coupon_times_used'><sup>".$last_row['visits']." ".CBE1_COUPONS_TUSED."</sup></span>" : ""; ?>
						<br/>
						<?php if ($last_row['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($last_row['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
						<?php if ($last_row['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $last_row['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($last_row['time_left']); ?></span>
						<?php } ?>
					</div>
					<div class="col-xs-3 col-md-3 text-right">
						<?php if ($last_row['code'] != "") { ?><span class="coupon_code"><?php echo (HIDE_COUPONS == 0 || isLoggedIn()) ? $last_row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br><br><?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $last_row['retailer_id']; ?>&c=<?php echo $last_row['coupon_id']; ?>" target="_blank"><?php echo ($last_row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
					</div>
				</div>
				<?php } ?>
				</div>
				</div>

				<?php }else{ ?>
					<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>
					<div class="sline"></div>
				<?php } ?>
		</div>


		<div class="tab-pane fade" id="exclusive">
		<?php
				$cc = 0;
				// show exclusive coupons //
				$ex_query = "SELECT c.*, DATE_FORMAT(c.end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE (c.start_date<=NOW() AND (c.end_date='0000-00-00 00:00:00' OR c.end_date > NOW())) AND c.exclusive='1' AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY c.added DESC LIMIT $results_per_page";
				$ex_result = smart_mysql_query($ex_query);
				$ex_total = mysqli_num_rows($ex_result);

				if ($ex_total > 0)
				{
			?>
				<div class="container">
				<div class="row">
				<?php while ($ex_row = mysqli_fetch_array($ex_result)) { $cc++; ?>
				<div class="item_coupon">
					<div class="col-xs-6 col-md-3 text-center hidden-xs">
						<?php if ($ex_row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?>
						<div class="imagebox"><a href="<?php echo GetRetailerLink($ex_row['retailer_id'], $ex_row['title']); ?>"><img src="<?php if (!stristr($ex_row['image'], 'http')) echo SITE_URL."img/"; echo $ex_row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $ex_row['title']; ?>" title="<?php echo $ex_row['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($ex_row['retailer_id'], $ex_row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</div>
					<div class="col-xs-9 col-md-6">
						<span class="coupon_name"><h3><?php echo $ex_row['title']; ?></h3> <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $ex_row['retailer_id']; ?>&c=<?php echo $ex_row['coupon_id']; ?>" target="_blank"><?php echo $ex_row['coupon_title']; ?></a></span>
						<?php echo ($ex_row['visits'] > 0) ? "<span class='coupon_times_used'><sup>".$ex_row['visits']." ".CBE1_COUPONS_TUSED."</sup></span>" : ""; ?>
						<br/>
						<?php if ($ex_row['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($ex_row['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
						<?php if ($ex_row['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $ex_row['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <?php echo GetTimeLeft($ex_row['time_left']); ?></span>
						<?php } ?>
					</div>
					<div class="col-xs-3 col-md-3 text-right">
						<?php if ($ex_row['code'] != "") { ?><span class="coupon_code"><?php echo (HIDE_COUPONS == 0 || isLoggedIn()) ? $ex_row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br><br><?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $ex_row['retailer_id']; ?>&c=<?php echo $ex_row['coupon_id']; ?>" target="_blank"><?php echo ($ex_row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
					</div>
				</div>
				<?php } ?>
				</div>
				</div>

				<?php }else{ ?>
					<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>
					<div class="sline"></div>
				<?php } ?>
		</div>


		<div class="tab-pane fade" id="expiring">
		<?php
				$cc = 0;
				// show expires in 3 days coupons //
				$exp_query = "SELECT c.*, DATE_FORMAT(c.end_date, '".DATE_FORMAT."') AS coupon_end_date, UNIX_TIMESTAMP(c.end_date) - UNIX_TIMESTAMP() AS time_left, c.title AS coupon_title, r.image, r.title FROM cashbackengine_coupons c LEFT JOIN cashbackengine_retailers r ON c.retailer_id=r.retailer_id WHERE c.end_date!='0000-00-00 00:00:00' AND (c.end_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY)) AND c.status='active' AND (r.end_date='0000-00-00 00:00:00' OR r.end_date > NOW()) AND r.status='active' ORDER BY c.added DESC LIMIT $results_per_page";
				$exp_result = smart_mysql_query($exp_query);
				$exp_total = mysqli_num_rows($exp_result);

				if ($exp_total > 0)
				{
			?>
				<div class="container">
				<div class="row">
				<?php while ($exp_row = mysqli_fetch_array($exp_result)) { $cc++; ?>
				<div class="item_coupon">
					<div class="col-xs-6 col-md-3 text-center hidden-xs">
						<?php if ($exp_row['exclusive'] == 1) { ?><span class="exclusive" alt="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>" title="<?php echo CBE1_COUPONS_EXCLUSIVE; ?>"><?php echo CBE1_COUPONS_EXCLUSIVE; ?></span><?php } ?>
						<div class="imagebox"><a href="<?php echo GetRetailerLink($exp_row['retailer_id'], $exp_row['title']); ?>"><img src="<?php if (!stristr($exp_row['image'], 'http')) echo SITE_URL."img/"; echo $exp_row['image']; ?>" width="<?php echo IMAGE_WIDTH; ?>" height="<?php echo IMAGE_HEIGHT; ?>" alt="<?php echo $exp_row['title']; ?>" title="<?php echo $exp_row['title']; ?>" border="0" /></a></div>
						<br/><a class="more" href="<?php echo GetRetailerLink($exp_row['retailer_id'], $exp_row['title']); ?>#coupons"><?php echo CBE1_COUPONS_SEEALL; ?></a>
					</div>
					<div class="col-xs-9 col-md-6">
						<span class="coupon_name"><h3><?php echo $exp_row['title']; ?></h3> <a href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $exp_row['retailer_id']; ?>&c=<?php echo $exp_row['coupon_id']; ?>" target="_blank"><?php echo $exp_row['coupon_title']; ?></a></span>
						<?php echo ($exp_row['visits'] > 0) ? "<span class='coupon_times_used'><sup>".$exp_row['visits']." ".CBE1_COUPONS_TUSED."</sup></span>" : ""; ?>
						<br/>
						<?php if ($exp_row['description'] != "") { ?><div class="coupon_description"><?php echo TruncateText($exp_row['description'], COUPONS_DESCRIPTION_LIMIT, $more_link = 1); ?>&nbsp;</div><?php } ?>
						<?php if ($exp_row['end_date'] != "0000-00-00 00:00:00") { ?>
							<span class="expires"><?php echo CBE1_COUPONS_EXPIRES; ?>: <?php echo $exp_row['coupon_end_date']; ?></span> &nbsp; 
							<span class="time_left"><?php echo CBE1_COUPONS_TIMELEFT; ?>: <span class="exp_soon_label"><?php echo GetTimeLeft($exp_row['time_left']); ?></span></span>
						<?php } ?>
					</div>
					<div class="col-xs-3 col-md-3 text-right">
						<?php if ($exp_row['code'] != "") { ?><span class="coupon_code"><?php echo (HIDE_COUPONS == 0 || isLoggedIn()) ? $exp_row['code'] : CBE1_COUPONS_CODE_HIDDEN; ?></span><br><br><?php } ?>
						<a class="go2store" href="<?php echo SITE_URL; ?>go2store.php?id=<?php echo $exp_row['retailer_id']; ?>&c=<?php echo $exp_row['coupon_id']; ?>" target="_blank"><?php echo ($exp_row['code'] != "") ? CBE1_COUPONS_LINK : CBE1_COUPONS_LINK2; ?></a>
					</div>
				</div>
				<?php } ?>
				</div>
				</div>

				<?php }else{ ?>
					<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>
					<div class="sline"></div>
				<?php } ?>
		</div>
		</div>


	<?php

		$astores_query = "SELECT * FROM cashbackengine_retailers WHERE (end_date='0000-00-00 00:00:00' OR end_date > NOW()) AND status='active' ORDER BY title";
		$astores_result = smart_mysql_query($astores_query);
		$astores_total = mysqli_num_rows($astores_result);

		if ($total > 0 && $astores_total > 0)
		{
			$stores_per_column = 10;
			$vv = 0;
			$b = 0;
	?>

		<br>
		<h1><?php echo CBE1_COUPONS_BYSTORE; ?></h1>

		<div id="alphabet" class="hidden-xs">
		<ul>
			<?php
					$numLetters = count($alphabet);
					$i = 0;

					foreach ($alphabet as $letter)
					{
						$i++;
						if ($i == $numLetters) $lilast = ' class="last"'; else $lilast = '';
						echo "<li".$lilast."><a href=\"#$letter\">".$letter."</a></li>";
					}
			?>
		</ul>
		</div>

		<ul class="stores_list">
		<?php while ($astores_row = mysqli_fetch_array($astores_result)) { ?>
			<?php

				$first_letter = ucfirst(substr($astores_row['title'], 0, 1));
				if ($old_letter != $first_letter)
				{
					if ($b != 0 && $vv != 1) echo "</ul>";
					if (!in_array($first_letter, $alphabet))
					{
						if ($vv != 1)
						{
							echo "<li class='store2'><div class='letter'>0-9<a name='0-9'></a></div><ul>";
							$vv = 1;
						}
					}
					else
					{
						if ($vv == 1) echo "</ul>";
						echo "<li class='store2'><div class='letter'>$first_letter<a name='$first_letter'></a></div><ul>";
					}
							
					$old_letter = $first_letter;
					$b++;
					$bb = 0;
				}
			?>
				<?php if ($astores_row['featured'] == 1) { $ftag1 = "<b>"; $ftag2 = "</b>"; }else{  $ftag1 = $ftag2 = ""; } ?>

				<li><a href="<?php echo GetRetailerLink($astores_row['retailer_id'], $astores_row['title']); ?>"><?php echo $ftag1; ?><?php echo (strlen($astores_row['title']) > 75) ? substr($astores_row["title"], 0, 70)."..." : $astores_row["title"]; ?><?php echo $ftag2; ?></a> <span class="coupons"><?php echo GetStoreCouponsTotal($astores_row['retailer_id']); ?></span></li>

				<?php $bb++; if ($bb%$stores_per_column == 0) echo "</ul><ul>"; ?>
			<?php } ?>
		</ul>
	<?php } ?>

			<?php }else{ ?>
				<p align="center"><?php echo CBE1_COUPONS_NO; ?></p>
				<div class="sline"></div>
			<?php } ?>


<?php require_once ("inc/footer.inc.php"); ?>