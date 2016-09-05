<?php

// Защита от прямого обращения к скрипту
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ContestDb.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Contest.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ContestPlayer.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ContestForecast.php';


$user = JFactory::getUser();
$contest = Contest::findActive();

if (!$contest) {
    echo 'Конкурс закрыт';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ContestPlayer::registerParticipant($user->id, $contest->getId());
}

if (!$user->id) {
    $link = JRoute::_('index.php?option=com_user&view=login');
    header( 'Location: ' . $link );
} elseif (ContestPlayer::isParticipant($user->id)) {
    $games = $contest->getGames();
    $forecasts = ContestPlayer::getForecasts($user->id, $contest);
    $seasonForecasts = ContestPlayer::getSeasonForecast($user->id, $contest);
    $lastGameId = ContestForecast::getLastGameId();
    $forecastsByGameId = ContestPlayer::getForecastsByGameId($lastGameId['c_id']);
    ContestForecast::countPoints($lastGameId['c_id'],$lastGameId['c_lasku'],$lastGameId['c_where'], $forecastsByGameId);
    $report = ContestPlayer::getReport($lastGameId['c_id']);
    require_once JModuleHelper::getLayoutPath('mod_contest');
} else {
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'tmpl/contest_conditions.php';
}
