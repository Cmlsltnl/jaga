<?php

class ContentView {

	public function displayContentView($contentID) {
	
		
		$content = new Content($contentID);
		$contentTitle = $content->getTitle();
		$contentContent = $content->getContent();

		$contentSubmissionDateTime = $content->contentSubmissionDateTime;
		$contentPublished = $content->contentPublished;
		
		$contentHasLocation = $content->contentHasLocation;
		$contentLatitude = $content->contentLatitude;
		$contentLongitude = $content->contentLongitude;
		
		$opID = $content->contentSubmittedByUserID;
		$opUserName = User::getUserName($opID);

		Content::contentViewsPlusOne($contentID);
		$imageArray = Image::getObjectImageUrlArray('Content', $contentID);
			
		$html = "\n\t<!-- START CONTENT -->\n";
		$html .= "\t<div class=\"container\">\n\n";
		
			if ($contentPublished == 0) {
				$html .= "\t<div class=\"alert alert-danger\">";
					if ($_SESSION['lang'] == 'ja') { $html .= "今現在、当ページの表示は出来ません。"; } else { $html .= "This post is not currently published."; }
				$html .= "</div>";
			}
		
			$html .= "\t<div class=\"panel panel-default\">\n";
			
				$html .= "\t\t<div class=\"panel-heading jagaContentPanelHeading\">";
					$html .= "<div>";
						$html .= "<strong>" . $contentTitle . "</strong> | ";
						$html .= "<i><a href=\"http://jaga.io/u/" . urlencode($opUserName) . "/\">" . $opUserName . "</a> at " . $contentSubmissionDateTime . "</i>";
						if ($opID == $_SESSION['userID']) { $html .= "<a href=\"/k/update/" . $contentID . "/\" class=\"btn btn-default btn-sm pull-right\"><span class=\"glyphicon glyphicon-pencil\"></span></a>"; }
					$html .= "</div>";
				$html .= "</div>\n";
				
				$html .= "\t\t<div class=\"panel-body\">";
					
					$html .= "<div class=\"row\">";

						$imageHtml = '';
						foreach ($imageArray AS $imageID => $imageURL) {

							if ($contentHasLocation) {
								$imageClasses = "col-xs-12 col-sm-6 col-md-4 col-lg-4";
							} else {
								$imageClasses = "col-xs-12 col-sm-4 col-md-4 col-lg-3";
							}
						
							// IMAGE
							$imageHtml .= "<div class=\"item $imageClasses\" data-toggle=\"modal\" data-target=\"#" . $imageID . "\" style=\"margin-bottom:10px;\">";
								$imageHtml .= "<img src=\"" . $imageURL . "\" class=\"img-responsive jagaContentViewImage\">";
							$imageHtml .= "</div>\n";
							
							// MODAL
							$imageHtml .= "
							<div class=\"modal fade\" id=\"" . $imageID . "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"deluxeNobileFirLabel\" aria-hidden=\"true\">
								<div class=\"modal-dialog\">
									<div class=\"modal-content\">
										<div class=\"modal-header\">
											<button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">" . Lang::getLang('close') . "</span></button>
											<h4 class=\"modal-title\" id=\"" . $imageID . "\">" . $contentTitle ."</h4>
										</div>
										<div class=\"modal-body text-center\"><img src=\"" . $imageURL . "\" class=\"img-responsive\" style=\"margin:0px auto 0px auto;\"></div>
										<div class=\"modal-footer\"><button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">" . Lang::getLang('close') . "</button></div>
									</div>
								</div>
							</div>
							";
							
						}
						
						if ($contentHasLocation) {
							
							$html .= "<div class=\"col-xs-12 col-sm-6 col-md-8 col-lg-9\">";
								$html .= "<div  id=\"panelBodyContent\">" . $contentContent . "</div>";
								$html .= $imageHtml;
							$html .="</div>";
							$html .= "<div class=\"col-xs-12 col-sm-6 col-md-4 col-lg-3\">";
								$html .= "<div id=\"map-canvas\">";
									$html .= "<iframe frameborder=\"0\" style=\"border:0;\" src=\"https://www.google.com/maps/embed/v1/place?key=" . Config::read('googlemaps.embed-api-key') . "&maptype=satellite&q=" . $contentLatitude . "," . $contentLongitude . "\"></iframe>";
								$html .= "</div>";
							$html .= "</div>";

						} else {
							$html .= "<div class=\"col-xs-12\" id=\"panelBodyContent\">" . $contentContent . "</div>";
							$html .= $imageHtml;
						}

					$html .= "</div>";

				$html .= "</div>\n";
			$html .= "\t</div>\n";
		$html .= "\t</div>\n";
		$html .= "\t<!-- END CONTENT -->\n\n";
	
		return $html;
			
	}

	public function displayEasyContentView($contentID) {
	
		$content = new Content($contentID);
		$contentContent = $content->getContent();
		Content::contentViewsPlusOne($contentID);
		return $contentContent;
			
	}
	
	public static function displayChannelContentList($channelID, $contentCategoryKey) {

		$contentArray = Content::getContentListArray($channelID, $contentCategoryKey, 1);

		$category = new Category($contentCategoryKey);
		if ($_SESSION['lang'] == 'ja') { $contentCategoryTitle = $category->contentCategoryJapanese; } else { $contentCategoryTitle = $category->contentCategoryEnglish; }
		
		$html = "\t<!-- START CONTENT LIST -->\n";
		$html .= "\t<div class=\"container\">\n\n";
		
			$html .= "\t\t<div class=\"panel panel-default\">\n\n";
		
				$html .= "\t\t\t<div class=\"panel-heading jagaContentPanelHeading\">\n";
					$html .= "\t\t\t\t<a href=\"/k/create/" . $contentCategoryKey . "/\"><span style=\"float:right;\" class=\"glyphicon glyphicon-plus\"></span></a>\n";
					$html .= "\t\t\t\t<h4>" . strtoupper($contentCategoryTitle) . "</h4>\n";
				$html .= "\t\t\t</div>\n";
				
				$html .= "\t\t\t<ul class=\"list-group\">\n";

					foreach ($contentArray as $contentID => $contentURL) {
					
						$content = new Content($contentID);
						$contentTitle = $content->getTitle();
						$contentViews = $content->contentViews;

						$html .= "\t\t\t\t<a href=\"/k/" . $contentCategoryKey . "/" . $contentURL . "/\" class=\"list-group-item jagaListGroupItem\">";
							$html .= "<span class=\"jagaListGroup\">";
								$html .= "<span class=\"jagaListGroupBadge\">" . $contentViews . "</span>";
								$html .=  $contentTitle;
							$html .= "</span>";
						$html .= "</a>\n";
						
					}

					$html .= "\t\t\t\t<a href=\"/k/" . $contentCategoryKey . "/\" class=\"list-group-item jagaListGroupItemMore\">";
						$html .= "MORE <span class=\"glyphicon glyphicon-arrow-right\"></span>";
					$html .= "</a>\n";
					
				$html .= "\t\t\t</ul>\n";

			$html .= "\t\t</div>\n\n";
			
		$html .= "\t</div>\n";
		$html .= "\t<!-- END CONTENT LIST -->\n\n";
		
		return $html;	

	}
	
	public function getContentForm(
		$type, 
		$contentID = 0, 
		$contentCategoryKey = '', 
		$inputArray = array(), 
		$errorArray = array()
	) {
	
		if (empty($inputArray)) {

			$content = new Content($contentID);
			
			$channelID = $_SESSION['channelID'];
			$contentURL = $content->contentURL;
			if ($contentID != 0) { $contentCategoryKey = $content->contentCategoryKey; }
			$contentSubmittedByUserID = $content->contentSubmittedByUserID;
			$contentSubmissionDateTime = $content->contentSubmissionDateTime;
			$contentPublishStartDate = $content->contentPublishStartDate;
			$contentPublishEndDate = $content->contentPublishEndDate;
			$contentLastModified = $content->contentLastModified;
			$contentTitleEnglish = $content->contentTitleEnglish;
			$contentTitleJapanese = $content->contentTitleJapanese;
			$contentEnglish = $content->contentEnglish;
			$contentJapanese = $content->contentJapanese;
			$contentLinkURL = $content->contentLinkURL;
			$contentPublished = $content->contentPublished;
			$contentViews = $content->contentViews;
			$contentIsEvent = $content->contentIsEvent;
			$contentEventDate = $content->contentEventDate;
			$contentEventStartTime = $content->contentEventStartTime;
			$contentEventEndTime = $content->contentEventEndTime;
			$contentHasLocation = $content->contentHasLocation;
			$contentLatitude = $content->contentLatitude;
			$contentLongitude = $content->contentLongitude;

		} else {

			$channelID = $_SESSION['channelID'];
			if (isset($inputArray['contentURL'])) { $contentURL = $inputArray['contentURL']; }
			if (isset($inputArray['contentCategoryKey'])) { $contentCategoryKey = $inputArray['contentCategoryKey']; }
			if (isset($inputArray['contentSubmittedByUserID'])) { $contentSubmittedByUserID = $inputArray['contentSubmittedByUserID']; }
			if (isset($inputArray['contentSubmissionDateTime'])) { $contentSubmissionDateTime = $inputArray['contentSubmissionDateTime']; }
			if (isset($inputArray['contentPublishStartDate'])) { $contentPublishStartDate = $inputArray['contentPublishStartDate']; }
			if (isset($inputArray['contentPublishEndDate'])) { $contentPublishEndDate = $inputArray['contentPublishEndDate']; }
			if (isset($inputArray['contentLastModified'])) { $contentLastModified = $inputArray['contentLastModified']; }
			if (isset($inputArray['contentTitleEnglish'])) { $contentTitleEnglish = $inputArray['contentTitleEnglish']; }
			if (isset($inputArray['contentTitleJapanese'])) { $contentTitleJapanese = $inputArray['contentTitleJapanese']; }
			if (isset($inputArray['contentEnglish'])) { $contentEnglish = $inputArray['contentEnglish']; }
			if (isset($inputArray['contentJapanese'])) { $contentJapanese = $inputArray['contentJapanese']; }
			if (isset($inputArray['contentLinkURL'])) { $contentLinkURL = $inputArray['contentLinkURL']; }
			if (isset($inputArray['contentPublished']) && $inputArray['contentPublished'] == 1) { $contentPublished = 1; } else { $contentPublished = 0; }
			if (isset($inputArray['contentViews'])) { $contentViews = $inputArray['contentViews']; }
			if (isset($inputArray['contentIsEvent'])) { $contentIsEvent = $inputArray['contentIsEvent']; } else { $contentIsEvent = 0; }
			if (isset($inputArray['contentEventDate'])) { $contentEventDate = $inputArray['contentEventDate']; }
			if (isset($inputArray['contentEventStartTime'])) { $contentEventStartTime = $inputArray['contentEventStartTime']; }
			if (isset($inputArray['contentEventEndTime'])) { $contentEventEndTime = $inputArray['contentEventEndTime']; }
			if (isset($inputArray['contentHasLocation'])) { $contentHasLocation = $inputArray['contentHasLocation']; } else { $contentHasLocation = 0; }
			if (isset($inputArray['contentLatitude'])) { $contentLatitude = $inputArray['contentLatitude']; }
			if (isset($inputArray['contentLongitude'])) { $contentLongitude = $inputArray['contentLongitude']; }

		}

		if ($type == 'create') { $formURL = "/k/create/"; } elseif ($type == 'update') { $formURL = "/k/update/" . $contentID . "/"; }
		
		$html = "\n\t<!-- START CONTENT CONTAINER -->\n";
		$html .= "\t<div class=\"container\">\n\n";
		
			$html .= "\t\t<div class=\"col-md-12\">\n\n";

			$html .= "\t\t<!-- START jagaContent -->\n";
			$html .= "\t\t<div id=\"jagaContent\" class=\"\">\n\n";

				$html .= "\t\t\t<!-- START PANEL -->\n";
				$html .= "\t\t\t<div class=\"panel panel-default\" >\n\n";
					
					$html .= "\t\t\t\t<!-- START PANEL-HEADING -->\n";
					$html .= "\t\t\t\t<div class=\"panel-heading jagaContentPanelHeading\">\n\n";
						
						$html .= "\t\t\t\t\t<div class=\"panel-title\">";
							
							if ($type == 'create') {
								$html .= strtoupper(Lang::getLang('createPost'));
							} elseif ($type == 'update') {
								$html .= strtoupper(Lang::getLang('updatePost'));
							}
							
							
						$html .= "</div>\n";
					
					$html .= "\t\t\t\t</div>\n";
					$html .= "\t\t\t\t<!-- END PANEL-HEADING -->\n\n";
					
					$html .= "\t\t\t\t<!-- START PANEL-BODY -->\n";
					$html .= "\t\t\t\t<div class=\"panel-body\">\n\n";

						$html .= "\t\t\t\t\t<!-- START jagaContentForm -->\n";
						
						$html .= "\t\t\t\t\t<form role=\"form\" id=\"jagaContentForm\" name=\"jagaContentForm\" class=\"form-horizontal\"  method=\"post\" action=\"" . $formURL . "\"  enctype=\"multipart/form-data\">\n\n";
					
							if ($type == 'update') { $html .= "<input type=\"hidden\" name=\"contentID\" value=\"" . $contentID . "\">\n"; }

							$html .= "\t\t\t\t\t\t<div class=\"row\">\n";
								
								$html .= "\t\t\t\t\t\t\t<div>\n";
								
									$html .= "\t\t\t\t\t\t\t<label class=\"col-sm-2 pull-right\">";
										$html .= "\t\t\t\t\t\t\t\t<input type=\"checkbox\" name=\"contentPublished\" value=\"1\"";
											if ($contentPublished == 1) { $html .= " checked"; }
										$html .= "> " . Lang::getLang('publish') . "\n";
									$html .= "\t\t\t\t\t\t\t</label>\n";
									
									$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-4 pull-right\">\n";
										$html .= CategoryView::categoryDropdown($contentCategoryKey);
									$html .= "\t\t\t\t\t\t\t</div>\n";
									
								$html .= "\t\t\t\t\t\t\t</div>\n\n";
								
								
							$html .= "\t\t\t\t\t\t</div>\n\n";
							
							$html .= "<hr />\n\n";

							$html .= "\t\t\t\t\t\t<div class=\"row\">\n";
									
									$html .= "<div class=\"col-sm-6\">";
								
										$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";
											$html .= "\t\t\t\t\t\t\t<label for=\"contentTitleEnglish\" class=\"col-sm-4 control-label\">" . Lang::getLang('contentTitleEnglish') . "</label>\n";
											$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-8\">\n";
												$html .= "\t\t\t\t\t\t\t\t<input type=\"text\" id=\"contentTitleEnglish\" name=\"contentTitleEnglish\" class=\"form-control\" placeholder=\"contentTitleEnglish\" value=\"" . $contentTitleEnglish . "\">\n";
											$html .= "\t\t\t\t\t\t\t</div>\n";
										$html .= "\t\t\t\t\t\t</div>\n\n";
										
										$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";
											$html .= "\t\t\t\t\t\t\t<label for=\"contentEnglish\" class=\"col-sm-4 control-label\">" . Lang::getLang('contentEnglish') . "</label>\n";
											$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-8\">\n";
												$html .= "\t\t\t\t\t\t\t\t<textarea rows=\"7\" id=\"contentEnglish\" name=\"contentEnglish\" class=\"form-control\" placeholder=\"contentEnglish\">" . $contentEnglish . "</textarea>\n";
											$html .= "\t\t\t\t\t\t\t</div>\n";
										$html .= "\t\t\t\t\t\t</div>\n\n";
										
									$html .= "</div>";
									

									$html .= "<div class=\"col-sm-6\">";
										
										$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";	
											$html .= "\t\t\t\t\t\t\t<label for=\"contentTitleJapanese\" class=\"col-sm-4 control-label\">" . Lang::getLang('contentTitleJapanese') . "</label>\n";
											$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-8\">\n";
												$html .= "\t\t\t\t\t\t\t\t<input type=\"text\" id=\"contentTitleJapanese\" name=\"contentTitleJapanese\" class=\"form-control\" placeholder=\"contentTitleJapanese\" value=\"" . $contentTitleJapanese . "\">\n";
											$html .= "\t\t\t\t\t\t\t</div>\n";
										$html .= "\t\t\t\t\t\t</div>\n\n";
										
										$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";	
											$html .= "\t\t\t\t\t\t\t<label for=\"contentJapanese\" class=\"col-sm-4 control-label\">" . Lang::getLang('contentJapanese') . "</label>\n";
											$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-8\">\n";
												$html .= "\t\t\t\t\t\t\t\t<textarea rows=\"7\" id=\"contentJapanese\" name=\"contentJapanese\" class=\"form-control\" placeholder=\"contentJapanese\">" . $contentJapanese . "</textarea>\n";
											$html .= "\t\t\t\t\t\t\t</div>\n";
										$html .= "\t\t\t\t\t\t</div>\n\n";

									$html .= "</div>";
									
								$html .= "</div>";

								$html .= "<hr />";
									
										$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";
										
											$html .= "\t\t\t\t\t\t\t<label for=\"contentImages\" class=\"col-sm-2 control-label\">" . Lang::getLang('images') . "</label>\n";
										
											$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-6\">\n";
												$html .= "<div class=\"input-group\">";
													$html .= "<span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-picture\"></i></span>";
													$html .= "<input type=\"file\" name=\"contentImages[]\" accept=\"image/*\" class=\"form-control\"  multiple=\"multiple\">";
												$html .= "</div>";
											$html .= "</div>";
											
											
											
										$html .= "</div>";
									
									$html .= "<hr />";
									
										$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";
											$html .= "\t\t\t\t\t\t\t<label for=\"contentLinkURL\" class=\"col-sm-2 control-label\">" . Lang::getLang('link') . "</label>\n";
											$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-6\">\n";

												$html .= "<div class=\"input-group\">";
													$html .= "<span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-link\"></i></span>";
													$html .= "<input type=\"url\" name=\"contentLinkURL\" class=\"form-control\" placeholder=\"http://example.com/\" value=\"" . $contentLinkURL . "\">";
												$html .= "</div>";
											$html .= "</div>";
										$html .= "</div>";
								
									$html .= "<hr />";

										$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";
											$html .= "<label for=\"contentHasLocation\" class=\"col-sm-2 control-label\"><input type=\"checkbox\" name=\"contentHasLocation\" value=\"1\"";
												if ($contentHasLocation == 1) { $html .= " checked=\"true\""; }
											$html .= "> " . Lang::getLang('location') . "</label>";
											$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-10\">\n";
												$html .= "\t\t\t\t\t\t\t\t<input type=\"text\" id=\"contentLocationNameInput\" name=\"contentLocationNameInput\" class=\"form-control\">\n";
											$html .= "\t\t\t\t\t\t\t</div>\n";
										$html .= "\t\t\t\t\t\t</div>\n\n";
										
										$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";

											$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-10 col-sm-offset-2\">\n";
												$html .= "\t\t<div id=\"contentCoordinatesMap\"></div>\n";
											$html .= "\t\t\t\t\t\t\t</div>\n";
											
										$html .= "\t\t\t\t\t\t</div>\n\n";
										
										$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";
										
											$html .= "\t\t\t\t\t\t\t<label for=\"contentLatitude\" class=\"col-sm-1 col-sm-offset-2\">" . Lang::getLang('latitude') . "</label>\n";
											$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-2\">\n";
												$html .= "\t\t\t\t\t\t\t\t<input type=\"text\" id=\"contentLatitude\" name=\"contentLatitude\" class=\"form-control\" placeholder=\"0.000000\" value=\"" . $contentLatitude . "\">\n";
											$html .= "\t\t\t\t\t\t\t</div>\n";
											
											$html .= "\t\t\t\t\t\t\t<label for=\"contentLongitude\" class=\"col-sm-1\">" . Lang::getLang('longitude') . "</label>\n";
											$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-2\">\n";
												$html .= "\t\t\t\t\t\t\t\t<input type=\"text\" id=\"contentLongitude\" name=\"contentLongitude\" class=\"form-control\" placeholder=\"0.000000\" value=\"" . $contentLongitude . "\">\n";
											$html .= "\t\t\t\t\t\t\t</div>\n";
											
										$html .= "\t\t\t\t\t\t</div>\n\n";
							
										$html .= "
										
											<script>
												$('#contentCoordinatesMap').locationpicker({
													location: {
														latitude: " . $contentLatitude . ",
														longitude: " . $contentLongitude . "
													},
													radius: 100,
													inputBinding: {
														latitudeInput: $('#contentLatitude'),
														longitudeInput: $('#contentLongitude'),
														locationNameInput: $('#contentLocationNameInput')
													},
													enableAutocomplete: true
												});
											</script>
											
										";
	
									$html .= "<hr />";		

										$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";

											$html .= "<label for=\"contentIsEvent\" class=\"col-sm-2 control-label\"><input type=\"checkbox\" name=\"contentIsEvent\" value=\"1\"";
												if ($contentIsEvent == 1) { $html .= " checked=\"true\""; }
											$html .= "> " . Lang::getLang('event') . "</label>";

											$html .= "<div class=\"col-sm-3\">";
												$html .= "<div class=\"input-group\">";
													$html .= "<span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-calendar\"></i></span>";
													$html .= "<input type=\"date\" name=\"contentEventDate\" class=\"form-control\" value=\"" . $contentEventDate . "\">";
												$html .= "</div>";
											$html .= "</div>";

											$html .= "<div class=\"col-sm-1\">";
												$html .= "<label for=\"contentEventStartTime\">" . Lang::getLang('startTime') . "</label>";
											$html .= "</div>";
											
											$html .= "<div class=\"col-sm-2\">";
												$html .= "<div class=\"input-group\">";
													$html .= "<span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-time\"></i></span>";
													$html .= "<input type=\"time\" name=\"contentEventStartTime\" class=\"form-control\" value=\"" . $contentEventStartTime . "\">";
												$html .= "</div>";
											$html .= "</div>";		
											
											$html .= "<div class=\"col-sm-1\">";
												$html .= "<label for=\"contentEventEndTime\">" . Lang::getLang('endTime') . "</label>";
											$html .= "</div>";
											
											$html .= "<div class=\"col-sm-2\">";
												$html .= "<div class=\"input-group\">";
													$html .= "<span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-time\"></i></span>";
													$html .= "<input type=\"time\" name=\"contentEventEndTime\" class=\"form-control\" value=\"" . $contentEventEndTime . "\">";
												$html .= "</div>";
											$html .= "</div>";
											
										$html .= "</div>";
		
							$html .= "<hr />\n\n";

							$html .= "\t\t\t\t\t\t<div class=\"form-group\">\n";
								$html .= "\t\t\t\t\t\t\t<div class=\"col-sm-12\">\n";
									if ($type == 'update') {
										$html .= "\t\t\t\t\t\t\t\t<a href=\"/k/delete/" . $contentID . "/\" class=\"btn btn-danger col-xs-2 col-sm-3 col-md-2\" style=\"color:#fff;\"><span class=\"glyphicon glyphicon-remove\"></span> <span class=\"hidden-xs\">" . strtoupper(Lang::getLang('delete')) . "</span></a>\n";
									}
									$html .= "\t\t\t\t\t\t\t\t<button type=\"submit\" name=\"jagaContentSubmit\" id=\"jagaContentSubmit\" ";
									if ($type == 'update') {
										$html .= "class=\"btn btn-primary col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-6\">";
									} elseif ($type == 'create') {
										$html .= "class=\"btn btn-primary col-xs-8 col-xs-offset-4 col-sm-6 col-sm-offset-6 col-md-4 col-md-offset-8\">";
									}
									$html .= "<span class=\"glyphicon glyphicon-ok\"></span> " . strtoupper(Lang::getLang($type)) . "</button>\n";
								$html .= "\t\t\t\t\t\t\t</div>\n";
							$html .= "\t\t\t\t\t\t</div>\n\n";
							
						$html .= "\t\t\t\t\t</form>\n";
						$html .= "\t\t\t\t\t<!-- END jagaContentForm -->\n\n";

					$html .= "\t\t\t\t</div>\n";
					$html .= "\t\t\t\t<!-- END PANEL-BODY -->\n\n";
			
				$html .= "\t\t\t</div>\n";
				$html .= "\t\t\t<!-- END PANEL -->\n\n";
			
			$html .= "\t\t</div>\n";
			$html .= "\t\t<!-- END jagaContent -->\n\n";

			$html .= "\t\t</div>\n";
		$html .= "\t</div>\n";
		$html .= "\t<!-- END CONTENT CONTAINER -->\n\n";
			
		return $html;

	}

	public static function displayUserContentList($username) {

		$userID = User::getUserIDwithUserNameOrEmail($username);
		$userContentArray = Content::getUserContent($userID);
				
		$html = "\t<!-- START CONTENT LIST -->\n";
		$html .= "\t<div class=\"container\">\n\n";
		
			$html .= "\t\t<div class=\"panel panel-default\">\n\n";
		
				$html .= "\t\t\t<div class=\"panel-heading jagaContentPanelHeading\"><h4>" . strtoupper($username) . "</h4></div>\n\n";
				
				$html .= "\t\t\t<ul class=\"list-group\">\n";

				
					foreach ($userContentArray as $contentID => $contentURL) {
					
						$content = new Content($contentID);
						$contentTitle = $content->getTitle();
						$contentViews = $content->contentViews;
						$contentCategoryKey = $content->contentCategoryKey;

						$html .= "\t\t\t\t<a href=\"/k/" . $contentCategoryKey . "/" . $contentURL . "/\" class=\"list-group-item jagaListGroupItem\">";
							$html .= "<span class=\"jagaListGroup\">";
								$html .= "<span class=\"jagaListGroupBadge\">" . $contentViews . "</span>";
								$html .=  $contentTitle;
							$html .= "</span>";
						$html .= "</a>\n";
						
					}

					// $html .= "\t\t\t\t<a href=\"/k/" . $contentCategoryKey . "/\" class=\"list-group-item jagaListGroupItemMore\">";
						// $html .= "MORE <span class=\"glyphicon glyphicon-arrow-right\"></span>";
					// $html .= "</a>\n";
					
				$html .= "\t\t\t</ul>\n";
				
			$html .= "\t\t</div>\n\n";
			
		$html .= "\t</div>\n";
		$html .= "\t<!-- END CONTENT LIST -->\n\n";
		
		return $html;	

	
	}
	
	public static function displayRecentContentItems($channelID = 0, $contentCategoryKey = '', $numberOfItems = 50, $subscriptionUserID = 0) {
		
		if ($subscriptionUserID == 0) {
			$recentContentArray = Content::getRecentContentArray($channelID, $contentCategoryKey, $numberOfItems);
		} else {
			$recentContentArray = Subscription::getRecentSubscribedContentArray($subscriptionUserID);
		}

		$html = "\n\n";
		$html .= "\t\t<div class=\"container\"> <!-- START CONTAINER -->\n";
			$html .= "\t\t\t<div class=\"row\" id=\"list\"> <!-- START ROW -->\n";

				$i = 0;
				foreach ($recentContentArray AS $contentID) {
				
					$content = new Content($contentID);
					$contentTitle = $content->getTitle();
					
					$contentURL = $content->contentURL;
					$thisContentCategoryKey = $content->contentCategoryKey;
					$contentSubmittedByUserID = $content->contentSubmittedByUserID;
					$contentSubmissionDateTime = $content->contentSubmissionDateTime;
					$contentViews = $content->contentViews;
					$thisChannelID = $content->channelID;
					
					$channel = new Channel($thisChannelID);
					$thisContentChannelKey = $channel->channelKey;
					$channelTitle = $channel->getTitle();
					
					$contentContent = $content->getContent();
					$contentContent = strip_tags($contentContent);
					$contentContent = preg_replace('/\s+/', ' ', $contentContent);
					$contentContent = Utilities::truncate($contentContent, 100, ' ', '...');
					
					$user = new User($contentSubmittedByUserID);
					$username = $user->username;

					$html .= "\t\t\t\t<div class=\"item col-xs-12 col-sm-6 col-md-4 col-lg-3\">\n";
						$html .= "\t\t\t\t\t<div class=\"panel panel-default\">\n";
							
							$html .= "\t\t\t\t\t\t<div class=\"panel-heading jagaContentPanelHeading\">\n";
								$html .= "<h4><a href=\"http://" . $thisContentChannelKey . ".jaga.io/k/" . $thisContentCategoryKey . "/" . $contentURL . "/\">" . strtoupper($contentTitle) . "</a></h4>";
								$html .= "<a href=\"http://jaga.io/u/" . $username . "/\">" . $username . "</a> ";
								$html .= "<a href=\"http://" . $thisContentChannelKey . ".jaga.io/\" class=\"pull-right\">" . $channelTitle . "</a>";
							$html .= "\t\t\t\t\t\t</div>\n";

							$html .= "\t\t\t\t\t\t\t<a href=\"http://" . $thisContentChannelKey . ".jaga.io/k/" . $thisContentCategoryKey . "/" . $contentURL . "/\" class=\"list-group-item jagaListGroupItem\">";
								$html .= "<span class=\"jagaListGroup\">";
									// $html .= "<span class=\"jagaListGroupBadge\">" . $contentViews . "</span>";
									if (Image::objectHasImage('Content',$contentID)) {
										$imagePath = Image::getLegacyObjectMainImagePath('Content',$contentID);
										if ($imagePath == "") { $imagePath = Image::getObjectMainImagePath('Content',$contentID,600); }
										if ($imagePath != "") { $html .= "<img class=\"img-responsive\" src=\"" . $imagePath . "\"><br />"; }
									}			
									$html .=  "<div style=\"white-space:pre-line;overflow-y:hidden;\">" . $contentContent . "</div>";
								$html .= "</span>";
							$html .= "</a>\n";
							
						$html .= "\t\t\t\t\t</div>\n";
					$html .= "\t\t\t\t</div>\n";

					if ($i  == 10 || $i  == 30 || $i == ($numberOfItems - 10)) {
						$html .= "\t\t\t\t<div class=\"item col-xs-12 col-sm-6 col-md-4 col-lg-3\">\n";
							$html .= "\t\t\t\t\t<div class=\"panel panel-default\" style=\"padding:3px;\">\n";
								$html .= "
								<script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
								<!-- jaga.io -->
								<ins class=\"adsbygoogle\"
									 style=\"display:block\"
									 data-ad-client=\"" . Config::read('adsense.data-ad-client') . "\"
									 data-ad-slot=\"" . Config::read('adsense.data-ad-slot') . "\"
									 data-ad-format=\"auto\"></ins>
								<script>
								(adsbygoogle = window.adsbygoogle || []).push({});
								</script>
								";
							$html .= "\t\t\t\t\t</div>\n";
						$html .= "\t\t\t\t</div>\n";
					}
					
					$i++;
				}
				
			$html .= "\t\t\t</div> <!-- END ROW -->\n";
		$html .= "\t\t</div> <!-- END CONTAINER -->\n\n";
		
		return $html;
	
	}

	public function displayContentDeleteConfirmationForm($contentID) {
		
		$content = new Content($contentID);
		$contentTitle = $content->getTitle();
		$contentContent = $content->getContent();
		$authorUserID = $content->contentSubmittedByUserID;
		
		if ($authorUserID != $_SESSION['userID']) { die('You cannot delete posts that do not belong to you.'); }
		
		$html = "\n\n\t<!-- CONTENT DELTE CONFIRMATION FORM -->\n";
		$html .= "\t<div class=\"container\">\n\n";
			$html .= "\t\t<div class=\"panel panel-default\">\n";
				$html .= "\t\t<div class=\"panel-heading\">Permanently delete <u>" . $contentTitle . "</u>?</div>\n";
				$html .= "\t\t<div class=\"panel-body\">";
					$html .= $contentContent;
				$html .= "</div>\n";
				$html .= "\t\t<div class=\"panel-footer text-right\">\n";
					$html .= "\t\t\t<div>\n";
						$html .= "\t\t\t<form method=\"post\" action=\"/k/delete/" . $contentID . "/\">\n";
							$html .= "\t\t\t\t<input type=\"hidden\" name=\"contentID\" value =\"" . $contentID . "\">\n";
							$html .= "<button type=\"submit\" name=\"jagaDeleteContentConfirmation\" id=\"jagaDeleteContentConfirmation\" ";
							$html .= "class=\"btn btn-danger\" style=\"color:#fff;\">Yes. Delete this post. <span class=\"glyphicon glyphicon-remove\"></span></button>\n";
						$html .= "</form>\n";
					$html .= "\t\t\t</div>\n";
				$html .= "\t\t</div>\n";
			$html .= "\t\t<div>\n";
		$html .= "\t<div>\n\n";
		
		return $html;
	}

}

?>