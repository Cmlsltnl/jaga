<?php

class Controller {
	
	public function __construct() {
	
		if (!isset($_COOKIE['TheKutchannel'])) { $cookie = new Cookie(); }
	
		$channelID = Channel::getSelectedChannelID();
		Session::setSession('channelID', $channelID);
		$channelKey = Channel::getChannelKey($channelID);
		Session::setSession('channelKey', $channelKey);
		
		$userID = 0;
		if (isset($_COOKIE['TheKutchannel'])) {
			$sessionID = $_COOKIE['TheKutchannel'];
			$userID = Session::getAuthSessionUserID($sessionID);
		}
		
		Session::setSession('userID', $userID);
		
		// if (!empty($_POST)) { Session::setSession('post', $_POST); } else {  }

	}

	public function getResource($urlArray) {

		if ($_SESSION['channelID'] == 0) {
		
			// note: still need to reroute reserved strings
			
			$reservedDomains = array('dev', 'mail', 'property', 'realestate', 'news', 'www', 'blog', 'qa', 'faq', 'support');
			
			$domain = $_SERVER['HTTP_HOST'];
			$tmp = explode('.', $domain);
			$subdomain = current($tmp);
			
			if (in_array($subdomain,$reservedDomains)) {
				return '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"><head><title>The Kutchannel: This subdomain is reserved.</title><META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW"></head><body style="background-color:#FFFF99;padding-top:150px;"><div style="text-align:center;"><a href="http://the.kutchannel.net/"><img src="/jaga/images/banner.png" style="max-width:100%;border-style:none;"></a><br />This subdomain is reserved.</div></body></html>';
				die();			
			} else {
				return '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"><head><title>The Kutchannel: The "' . $subdomain . '" channel does not yet exist.</title><META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW"></head><body style="background-color:#FFFF99;padding-top:150px;"><div style="text-align:center;"><a href="http://the.kutchannel.net/"><img src="/jaga/images/banner.png" style="max-width:100%;border-style:none;"></a><br />This channel has not yet been created. <a href="http://the.kutchannel.net/create-a-channel/' . $subdomain . '/" style="text-decoration:none;">Create it</a>!</div></body></html>';
				die();
			}

		}
	
		$arrayOfSupportedLanguages = array('en','ja');
		$lang = Language::getBrowserDefaultLanguage();
		if ($_SESSION['userID'] != 0) { $lang = User::getUserSelectedLanguage($_SESSION['userID']); }
		if (!in_array($lang, $arrayOfSupportedLanguages)) { $lang = 'en'; }
		Session::setSession('lang', $lang);
		
		$i = 0; while ($i <= 3) { if (!isset($urlArray[$i])) { $urlArray[$i] = ''; } $i++; } // minimum 3 array pointers

		if ($urlArray[0] == 'login') {
			
			$inputArray = array();
			$errorArray = array();
			
			if (isset($_POST['jagaLoginSubmit'])) {
			
				$inputArray['username'] = $_POST['username'];
				
				$username = $_POST['username'];
				$password = $_POST['password'];
			
				$errorArray = Authentication::checkAuth($username, $password);
				
				if (empty($errorArray)) {
				
					// log user in
					$userID = User::getUserIDwithUserNameOrEmail($username);
					Session::setSession('userID', $userID);
					
					// set userLastVisitDateTime
					User::setUserLastVisitDateTime($userID);
					
					// save session to db
					$authSession = new Session();
					$authSession->createAuthSession();
					
					// terminate script; forward header
					if (isset($_SESSION['loginForwardURL'])) {
						$forwardURL = $_SESSION['loginForwardURL'];
						Session::unsetSession('loginForwardURL');
						header("Location: $forwardURL");
					} else {
						$forwardURL = '/';
					}
					header("Location: $forwardURL");
					
				}
				
			}
			
			$page = new PageView();
			$html = $page->buildPage($urlArray, $inputArray, $errorArray);
			return $html;
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		} elseif ($urlArray[0] == 'register') {
		
			$inputArray = array();
			$errorArray = array();
			
			if (isset($_POST['jagaRegisterSubmit'])) {
			
				$inputArray['username'] = $_POST['username'];
				$inputArray['userEmail'] = $_POST['userEmail'];
				
				$username = $_POST['username'];
				$userEmail = $_POST['userEmail'];
				$password = $_POST['password'];
				$confirmPassword = $_POST['confirmPassword'];
				$raptcha = $_POST['raptcha'];
			
				$errorArray = Authentication::register($username, $userEmail, $password, $confirmPassword, $raptcha);
				
				if (empty($errorArray)) {

					$user = new User(0);
				
					unset($user->userID);
					
					$user->username = $username;
					$user->userDisplayName = $username;
					$user->userEmail = $userEmail;
					$user->userPassword = md5($password);
					$user->userRegistrationDateTime = date('Y-m-d H:i:s');

					$userID = Content::insert($user);

					$forwardURL = '/thank-you-for-registering/';
					header("Location: $forwardURL");
					
				}
				
			}
			
			$page = new PageView();
			$html = $page->buildPage($urlArray, $inputArray, $errorArray);
			return $html;
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
		
		} elseif ($urlArray[0] == 'logout') {

			Authentication::logout();
			header("Location: /");
			
		} elseif ($urlArray[0] == 'rss') {

			$rss = new Rss();
			$feed = $rss->getFeed($urlArray);
			return $feed;
			
		} elseif ($urlArray[0] == 'sitemap.xml') {
		
			$sitemap = new Sitemap();
			$xml = $sitemap->getSitemap($urlArray);
			return $xml;
			
		} elseif ($urlArray[0] == 'channel.css') {
		
			$theme = new ThemeView();
			$css = $theme->getTheme();
			
			header("Content-type: text/css");
			return $css;
			
		} elseif ($urlArray[0] == 'manage-channels') {

			// LOGGED IN USERS ONLY
			if ($_SESSION['userID'] == 0) { die('you are not logged in'); }
		
			// CHANNEL OWNER ONLY FOR UPDATE
			if ($urlArray[1] == 'update') {
				if ($urlArray[2] == '') {
					die ('A channel must be selected.');
				} else {
					$channelID = Channel::getChannelID($urlArray[2]);
					$channel = new Channel($channelID);
					$channelCreatorID = $channel->siteManagerUserID;
					if ($_SESSION['userID'] != $channelCreatorID) { die ('You do not own this channel.'); }
				}
			}
			
			// INITIALIZE $inputArray and $errorArray
			$inputArray = array();
			$errorArray = array();
			
			// IF USER INPUT EXISTS
			if (!empty($_POST)) {
			
				$inputArray = $_POST;

				// VALIDATION
				if (!preg_match('/^[a-zA-Z0-9-]+$/', $inputArray['channelKey'])) {
					$errorArray[] = 'The Key can contain only letters, numbers, and hyphens.';
				}
				// check if channel key exists
				if ($inputArray['channelTitleEnglish'] == '') { $errorArray[] = 'A title is required field.'; }
				if ($inputArray['channelKeywordsEnglish'] == '') { $errorArray[] = 'Keywords are required.'; }
				if ($inputArray['channelDescriptionEnglish'] == '') { $errorArray[] = 'A description is required.'; }
				// is at least one contentCategorySelected?
				
				if (empty($errorArray)) {
				
					if ($urlArray[1] == 'create') {

						$channel = new Channel(0);
						
						// filter out auto_increment key
						unset($channel->channelID);
						
						// set object property values
						foreach ($inputArray AS $property => $value) { if (isset($channel->$property)) { $channel->$property = $value; } }
						
						$channelID = Channel::insert($channel);

						// START ChannelCategory //
						foreach ($inputArray['contentCategoryKey'] AS $contentCategoryKey) {
							$channelCategory = new ChannelCategory();
							$channelCategory->channelID = $channelID;
							$channelCategory->contentCategoryKey = $contentCategoryKey;
							ChannelCategory::insert($channelCategory);
						}
						// END ChannelCategory //
						
						header("Location: /channels/");
						
					} elseif ($urlArray[1] == 'update' && isset($urlArray[2])) {
					
						$channelID = Channel::getChannelID($urlArray[2]);
					
						// build object
						$channel = new Channel($channelID);
						foreach ($inputArray AS $property => $value) { if (isset($channel->$property)) { $channel->$property = $value; } }
						
						// build conditions
						$conditions = array();
						$conditions['channelID'] = $channelID;
						
						// unset attributes that you don't want to update
						unset($channel->channelID);
						
						// update object
						Channel::update($channel, $conditions);
						
							// START ChannelCategory //
							
							$oldCategoryArray = array_keys(ChannelCategory::getChannelCategoryArray($channelID));
							$newCategoryArray = $inputArray['contentCategoryKey'];
							
							// if the old ain't in the new, delete it
							foreach ($oldCategoryArray AS $oldContentCategoryKey) {
								if (!in_array($oldContentCategoryKey, $newCategoryArray)) {
									$channelCategory = new ChannelCategory();
									$channelCategory->channelID = $channelID;
									$channelCategory->contentCategoryKey = $oldContentCategoryKey;
									ChannelCategory::delete($channelCategory);
								}
							}
							
							// if the new ain't in the old, insert it
							foreach ($newCategoryArray AS $newContentCategoryKey) {
								if (!in_array($newContentCategoryKey, $oldCategoryArray)) {
									$channelCategory = new ChannelCategory();
									$channelCategory->channelID = $channelID;
									$channelCategory->contentCategoryKey = $newContentCategoryKey;
									ChannelCategory::insert($channelCategory);
								}
							}
							
							// END ChannelCategory //

						// die ();
						header("Location: /manage-channels/");
						
					}

				}

			}
			
			$page = new PageView();
			$html = $page->buildPage($urlArray, $inputArray, $errorArray);
			return $html;

		} elseif ($urlArray[0] == 'k' && ($urlArray[1] == 'update' || $urlArray[1] == 'create')) { // CONTENT

			// INITIALIZE $inputArray and $errorArray
			$inputArray = array();
			$errorArray = array();
			
			
			// LOGGED IN USERS ONLY
			if ($_SESSION['userID'] == 0) {
				Session::setSession('loginForwardURL', $_SERVER['REQUEST_URI']);
				header("Location: /login/");
			}
		
			// CONTENT OWNER ONLY FOR UPDATE
			if ($urlArray[1] == 'update') {
				if ($urlArray[2] == '' || $urlArray[2] == 0) {
					die ('Content must be selected.');
				} else {
					$contentID = $urlArray[2];
					$content = new Content($contentID);
					$contentSubmittedByUserID = $content->contentSubmittedByUserID;
					if ($_SESSION['userID'] != $contentSubmittedByUserID) { die ('You can only edit your own content.'); }
				}
			}
			
			// IF USER INPUT EXISTS
			if (!empty($_POST)) {

				$inputArray = $_POST;

				// VALIDATION
				if ($inputArray['contentTitleEnglish'] == '') { $errorArray['contentTitleEnglish'][] = 'Every post needs a title.'; }
				if ($inputArray['contentEnglish'] == '') { $errorArray['contentEnglish'][] = 'Your post is empty.'; }
				if ($inputArray['contentCategoryKey'] == '') { $errorArray['contentCategoryKey'][] = 'A category must be selected.'; }
				// is this category enabled for this channel? check it
				
				
				if (empty($errorArray)) {
					
					if ($urlArray[1] == 'create') {

						$content = new Content(0);
						
						// filter out auto_increment key
						unset($content->contentID);
						
						// set object property values
						foreach ($inputArray AS $property => $value) { if (isset($content->$property)) { $content->$property = $value; } }
						
						// modify values where required
						$content->contentURL = SEO::googlify($inputArray['contentTitleEnglish']);
						
						$contentID = Content::insert($content);

						if (!empty($_FILES)) {

							// print_r($_FILES);
							
							$numberOfImages = count($_FILES['contentImages']['name']);
							
							for ($i = 0; $i < $numberOfImages; $i++) {
							
								$imageArray = array();
								$imageArray['name'] = $_FILES['contentImages']['name'][$i];
								$imageArray['type'] = $_FILES['contentImages']['type'][$i];
								$imageArray['tmp_name'] = $_FILES['contentImages']['tmp_name'][$i];
								$imageArray['error'] = $_FILES['contentImages']['error'][$i];
								$imageArray['size'] = $_FILES['contentImages']['size'][$i];
								
								$imageObject = 'Content';
								$imageObjectID = $contentID;
								Image::uploadImageFile($imageArray,$imageObject,$imageObjectID);
								
							}

						}
	
						$postSubmitURL = "/k/" . $inputArray['contentCategoryKey'] . "/";
						header("Location: $postSubmitURL");

					} elseif ($urlArray[1] == 'update' && isset($urlArray[2])) {
					
						$contentID = $urlArray[2];
					
						// build object
						$content = new Content($contentID);
						foreach ($inputArray AS $property => $value) {
							if (isset($content->$property)) {
								$content->$property = $value;
							}
						}
						
						// build conditions
						$conditions = array();
						$conditions['contentID'] = $contentID;
						
						// unset attributes that you don't want to update
						unset($content->contentID);
						
						// update object
						// print_r($content);
						Content::update($content, $conditions);

						$postSubmitURL = "/k/" . $inputArray['contentCategoryKey'] . "/";
						
						header("Location: $postSubmitURL");
						
					}

				}
				
			}
		
			$page = new PageView();
			$html = $page->buildPage($urlArray, $inputArray, $errorArray);
			return $html;

		} elseif ($urlArray[0] == 'k' && $urlArray[1] == 'comment' && is_numeric($urlArray[2])) {
		
			$contentPath = Content::getContentURL($urlArray[2]);
		
			if (!empty($_POST)) { $inputArray = $_POST; } else { header("Location: $contentPath"); }
			
			$page = new PageView();
			$html = $page->buildPage($urlArray, $inputArray);
			return $html;
		
		} elseif ($urlArray[0] == 'settings') {
		
			// INITIALIZE $inputArray and $errorArray
			$inputArray = array();
			$errorArray = array();
			
			// REDIRECT USERS WITHOUT CREDS TO LOGIN ROUTE
			if ($_SESSION['userID'] == 0) {
				Session::setSession('loginForwardURL', $_SERVER['REQUEST_URI']);
				header("Location: /login/");
			}

			if ($urlArray[1] == 'profile') {
				
				// IF USER INPUT EXISTS
				if (!empty($_POST)) {
				
					$inputArray = $_POST;
					
					// print_r($inputArray);
					
					// VALIDATION
					if ($inputArray['userDisplayName'] == '') { $errorArray['userDisplayName'][] = 'A display name is required.'; }
					if ($inputArray['userEmail'] == '') { $errorArray['userEmail'][] = 'An email address is required.'; }
					if ($inputArray['userPassword'] != $inputArray['confirmPassword']) { $errorArray['confirmPassword'][] = 'Passwords do not match.'; }
					
					if (empty($errorArray)) {
					
						$userID = $_SESSION['userID'];
					
						// build object
						$user = new User($userID);
						foreach ($inputArray AS $property => $value) { if (isset($user->$property)) { $user->$property = $value; } }
						if ($inputArray['userPassword'] != '') { $user->userPassword = md5($inputArray['userPassword']); }
						
						// build conditions
						$conditions = array();
						$conditions['userID'] = $userID;
						
						// unset attributes that you don't want to update
						unset($user->userID);
						unset($user->username);
						unset($user->userEmailVerified);
						unset($user->userAcceptsEmail);
						unset($user->userRegistrationChannelID);
						unset($user->userRegistrationDateTime);
						unset($user->userLastVisitDateTime);
						unset($user->userTestMode);
						unset($user->userBlackList);
						unset($user->userSelectedLanguage);
						if ($inputArray['userPassword'] == '') { unset($user->userPassword); }

						// update user
						User::update($user, $conditions);
						
						// upload profile image
						if (!empty($_FILES)) {
							$image = $_FILES['profileImage'];
							$imageObject = 'User';
							$imageObjectID = $userID;
							Image::uploadImageFile($image,$imageObject,$imageObjectID);
						}

						$postSubmitURL = "/settings/profile/";
						header("Location: $postSubmitURL");
						
					}
					
				}

			} elseif ($urlArray[1] == 'channels') {
			
			} elseif ($urlArray[1] == 'subscriptions') {
			
			}

			$page = new PageView();
			$html = $page->buildPage($urlArray, $inputArray, $errorArray);
			return $html;
		
		} elseif ($urlArray[0] == 'subscribe') {
			
			$inputArray = array();
			$errorArray = array();
			
			if ($_SESSION['userID'] != 0) {
				Subscription::subscribeUser($_SESSION['userID'], $_SESSION['channelID']);
			} else {
				$errorArray['subscribe'][] = 'You must be logged in to subscribe.';
			}
			
			$page = new PageView();
			$html = $page->buildPage($urlArray, $inputArray, $errorArray);
			return $html;
				
		} elseif ($urlArray[0] == 'unsubscribe') {
			
			$inputArray = array();
			$errorArray = array();
			
			if ($_SESSION['userID'] != 0) {
				Subscription::unsubscribeUser($_SESSION['userID'], $_SESSION['channelID']);
			} else {
				$errorArray['unsubscribe'][] = 'You must be logged in to unsubscribe.';
			}
			
			$page = new PageView();
			$html = $page->buildPage($urlArray, $inputArray, $errorArray);
			return $html;

			
		} elseif ($urlArray[0] == 'account-recovery') {
			
			$inputArray = array();
			$errorArray = array();
			
			if (isset($_POST['jagaAccountRecoverySubmit'])) {
				$inputArray = $_POST;
				$userEmail = $inputArray['userEmail'];
				$raptcha = $inputArray['raptcha'];
				$errorArray = AccountRecovery::accountRecoveryRequestValidation($userEmail, $raptcha);
				if (empty($errorArray)) {
					$accountRecovery = new AccountRecovery(0);
					unset($accountRecovery->accountRecoveryID);
					$accountRecovery->accountRecoveryEmail = $userEmail;
					$accountRecovery->accountRecoveryRequestDateTime = date('Y-m-d H:i:s');
					$accountRecovery->accountRecoveryUserID = User::getUserID($userEmail);
					$accountRecoveryID = AccountRecovery::insert($accountRecovery);
				}
			}

			$page = new PageView();
			$html = $page->buildPage($urlArray, $inputArray, $errorArray);
			return $html;
			
		} else {
		
			$page = new PageView();
			$html = $page->buildPage($urlArray);
			return $html;
			
		}

		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	}
	
}

?>