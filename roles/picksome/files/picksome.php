<?php
# While this next line should be taken care of in extenions, and indeed
# it is, because this extension is loaded via wfLoadExtension, and because
# Collection is loaded the old way, the sidebar before output is called
# preferring Collection, placing it higher on the siderbar than PickSome.
#
# This was requested to be changed, and the most straightforward way is
# to add it to the hook directly when loaded by LocalSettings
$wgHooks['SidebarBeforeOutput'][] = 'PickSomeHooks::onSidebarBeforeOutput';
wfLoadExtension('PickSome');
$wgPickSomeNumberOfPicks = 15;

$wgLFCPickSomeEligiblePage = "TorqueConfig:ValidProposals";

# This may not be performant and require caching if number of users/page hits
# becomes large.
$wgPickSomePage = function($title) {
  global $wgLFCPickSomeEligiblePage;
  $eligibleWildCardsTitle = Title::newFromText($wgLFCPickSomeEligiblePage);
  if($eligibleWildCardsTitle->exists()) {
    $page = new WikiPage($eligibleWildCardsTitle);
    $valid_pages = [];
    preg_match_all("/\\[\\[([^\\]]*)\\]\\]/", $page->getContent()->getText(), $valid_pages);
    foreach($valid_pages[1] as $valid_page) {
      if($title->equals(Title::newFromText($valid_page))) {
        return true;
      }
    }
    return false;
  } else {
    return false;
  }
};

$wgPickSomeSortFunction = function($t1, $t2) {
  $text1 = $t1->getText();
  $text2 = $t2->getText();

  $text1 = preg_replace("/^\\W/", "", $text1);
  $text2 = preg_replace("/^\\W/", "", $text2);
  return $text1 > $text2;
};

# These are defaults for competitions, and probably will be overridden at some point
$picksomeOverrideMessage = [
  "picksome-all" => "Everyone's Finalist Candidates",
  "picksome-title" => "Finalist Candidates",
  "picksome-choices" => "Finalist Candidate Choices",
  "picksome-my-picks" => "My Finalist Candidates",
  "picksome-unpick" => "Deselect",
  "picksome-pick" => "Select this page",
  "picksome-no-picks" => "No Finalist Candidates",
  "picksome-current" => "Current Page",
  "picksome-view-all" => "View Everyone's Finalist Candidates",
  "picksome-global-list" => "Global Finalist Candidate List",
  "picksome-remove-below" => "To select the current page, remove one below",
  "picksome-stop" => "Stop Selecting",
  "picksome-close-window" => "Close Window",
  "picksome-start" => "Start Selecting",
];
$wgHooks['MessagesPreLoad'][] = function($title, &$message, $code) {
  global $picksomeOverrideMessage;
  if(array_key_exists(strtolower($title), $picksomeOverrideMessage)) {
    $message = $picksomeOverrideMessage[strtolower($title)];
  }
};
?>