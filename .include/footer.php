		</div>

		<hr />

		<div id="footer">
			<p id="copyright">Copyright &copy; <?php echo $copyright; ?> <a id="admin-link" href="<?php echo $prefix . '/admin/index.php' .  (isset($_SESSION['auth']) && $_SESSION['auth'] ? '?logout">Log out' : '">Admin panel'); ?></a> <a id="afs-link" href="<?php echo $prefix; ?>/accommodation-for-students/">Accommodation for Students</a></p>
			<p id="siteby">Site by <a href="http://starsquare.co.uk">StarSquare Designs</a>.</p>
		</div>
	</body>
</html>