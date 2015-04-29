<?php

	// CONTROLLER
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/controller/Config.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/controller/Controller.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/controller/Core.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/controller/ORM.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/controller/Session.php');

	// MODEL
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/audit.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/accountRecovery.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/authentication.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/calendar.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/category.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/channel.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/channelCategory.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/comment.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/content.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/cookie.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/file.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/form.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/image.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/language.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/mail.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/message.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/rss.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/seo.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/shop.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/sitemap.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/subscription.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/theme.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/user.class.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/model/utilities.class.php');
	
	// VIEW
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/authentication.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/carousel.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/category.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/channel.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/comment.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/content.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/menu.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/message.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/page.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/subscription.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/theme.view.php');
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/view/user.view.php');

	// DB: apply gitignore to taterbase.php
	require($_SERVER['DOCUMENT_ROOT'] . '/jaga/php/config/taterbase.php');

	/* taterbase.php attributes */
	// Config::write('admin.email', '');
	// Config::write('admin.userIdArray', array());
	// Config::write('adsense.data-ad-client', '');
	// Config::write('adsense.data-ad-slot', '');
	// Config::write('alexa.atrk_acct', '');
	// Config::write('analytics.trackingID', '');
	// Config::write('db.basename', '');
	// Config::write('db.host', '');
	// Config::write('db.password', '');
	// Config::write('googlemaps.embed-api-key', '');
	// Config::write('ms.validate', '');
	// Config::write('pingdom.rumID', '');
	// Config::write('pinterest.domain_verify', '');
	// Config::write('site.url', '');
	// Config::write('system.email', '');
	// Config::write('system.url', '');

?>