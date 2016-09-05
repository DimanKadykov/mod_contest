<?php
class ContestForecast
{
    public static function getVariables($gameId, $method, $goalsH ,$goalsA , $gamePlace , $userId, $contestId){

        if ($method == 'insert') {
            echo 'Спасибо. Ваш прогноз принят! Счёт: '.$goalsH.' - '.$goalsA.'';
            if ($gamePlace == 'H') {
                ContestForecast::makeForecast ($userId, $contestId, $gameId, $goalsH, $goalsA);
            } else {
                ContestForecast::makeForecast($userId, $contestId, $gameId,$goalsA, $goalsH );
            }
        }
        if ($method == 'update') {
            echo 'Спасибо. Ваш прогноз принят! Счёт: '.$goalsH.' - '.$goalsA.'';
            if ($gamePlace == 'H') {
                ContestForecast::updateForecast($userId, $contestId, $gameId, $goalsH, $goalsA);
            }
            else {
                ContestForecast::updateForecast($userId, $contestId, $gameId, $goalsA,$goalsH);
            }
        }
    }

    public static function makeForecast($participantId, $contest, $gameId, $lfc_goals, $opp_team_goals)
    {
         if (ContestForecast::isForecastTimeExpired($gameId)) {
            return false;
         }

         $db = JFactory::getDBO();
         $forecast = new stdClass();
         $forecast->user_id = $participantId;
         $forecast->contest_id = $contest;
         $forecast->game_id = $gameId;
         $forecast->lfc_goals = $lfc_goals;
         $forecast->opp_team_goals = $opp_team_goals;
         $db->insertObject('frcst_forecast', $forecast);
     }

    public static function updateForecast($participantId, $contest, $gameId, $lfc_goals, $opp_team_goals)
    {
        if (ContestForecast::isForecastTimeExpired($gameId)) {
            return false;
        }

        $db = JFactory::getDBO();
        $query =  '
            UPDATE frcst_forecast
            SET lfc_goals = ' . $db->quote($lfc_goals) . ', opp_team_goals = ' . $db->quote($opp_team_goals) .
            'WHERE user_id =' .$db->quote($participantId).
            'AND game_id =' . $db->quote($gameId).
            'AND contest_id = ' . $db->quote($contest);
        $db->setQuery($query);
        $db->query();
    }

    public static function isForecastTimeExpired($gameId)
    {
        $db = JFactory::getDBO();
        $query = 'SELECT c_date, c_time
                  FROM jos_calendar
                  WHERE c_id = ' . $db->quote($gameId);
        $db->setQuery($query);
        $game = $db->loadAssoc();
        $gameTimestamp = strtotime($game['c_date'] . ' ' . $game['c_time']);
        $dateCurrent = new DateTime('now', new DateTimeZone('Europe/London'));

        return $dateCurrent->format('U') + 3600 > $gameTimestamp;
    }

    public static function makeSeasonForecast ($userId, $contestId, $lfcPlace, $lfcAllGoals, $lfcGamesAtNull,$lfcBestGoalScorer,$ligueCupStage,$faCupStage,$sturridgeGoals)
    {
        $db = JFactory::getDBO();
        $seasonForecast = new stdClass();
        $seasonForecast->user_id = $userId;
        $seasonForecast->contest_id = $contestId;
        $seasonForecast->lfc_place = $lfcPlace;
        $seasonForecast->lfc_all_goals = $lfcAllGoals;
        $seasonForecast->lfc_games_at_null = $lfcGamesAtNull;
        $seasonForecast->lfc_best_goalscorer = $lfcBestGoalScorer;
        $seasonForecast->ligue_cup_stage = $ligueCupStage;
        $seasonForecast->fa_cup_stage = $faCupStage;
        $seasonForecast->sturridge_goals = $sturridgeGoals;
        $db->insertObject('frcst_season_bonus', $seasonForecast);
        echo 'Спасибо. Ваш прогноз принят!';
    }

    public static function countPoints($gameId, $score, $homeOrAway, $forecastsByGameId){
        if($homeOrAway=='H'){
            $lfcGoalsForecast = substr($score,0,1);
            $oppTeamGoalsForecast = substr($score,2,1);
        }
        else{
            $lfcGoalsForecast = substr($score,2,1);
            $oppTeamGoalsForecast = substr($score,0,1);
        }
        $goalDiff = $lfcGoalsForecast - $oppTeamGoalsForecast;
        $result = ContestForecast::getGameResult($goalDiff);
        $users = ContestForecast::getPlayers();
        $playersCount = ContestForecast::getPlayersCount();
        for ($i=0; $i<$playersCount;$i++ ){
            $userId = $users[$i];
            if (isset($forecastsByGameId[$userId]['lfc_goals']) and isset($forecastsByGameId[$userId]['opp_team_goals'])) {
                $lfcGoals = $forecastsByGameId[$userId]['lfc_goals'];
                $oppTeamGoals = $forecastsByGameId[$userId]['opp_team_goals'];
                $divideOption = ContestForecast::countForecasts($gameId, $lfcGoals, $oppTeamGoals);
                $userPoints = 0;
                if ($lfcGoalsForecast == $forecastsByGameId[$userId]['lfc_goals'] and $oppTeamGoalsForecast == $forecastsByGameId[$userId]['opp_team_goals']){
                    $userPoints += 100/$divideOption[0];
                }
                $goalDiffUser = $lfcGoals - $oppTeamGoals;
                $resultUser = ContestForecast::getGameResult($goalDiffUser);
                if($goalDiff == $goalDiffUser){
                    $userPoints += 70/$divideOption[1];
                }
                if ($result == $resultUser){
                    $userPoints += 50/$divideOption[2];
                }
                ContestForecast::updatePoints($gameId, $userId, $userPoints);
            }
            if (isset($forecastsByGameId[$userId]['lfc_goals']) and isset($forecastsByGameId[$userId]['opp_team_goals'])) {
                $lfcGoals = $forecastsByGameId[$userId]['lfc_goals'];
                $oppTeamGoals = $forecastsByGameId[$userId]['opp_team_goals'];
                $divideOption = ContestForecast::countForecasts($gameId, $lfcGoals, $oppTeamGoals);
                $userPotentialPoints = 100/$divideOption[0] + 70/$divideOption[1] + 50/$divideOption[2];
                ContestForecast::updatePotentialPoints($gameId, $userId, $userPotentialPoints);
            }
        }
    }

    public function getGameResult($goalDiff){
        if ($goalDiff >0){
            $result = 'W';
        }
        elseif ($goalDiff<0){
            $result = 'L';
        }
        else {
            $result = 'D';
        }
        return $result;
    }

    public function updatePoints($gameId, $userId, $userPoints){
        $db = JFactory::getDBO();
        $query =  'Update frcst_forecast set points_gained = '.$db->quote($userPoints).
            'where user_id =' .$db->quote($userId).
            'and game_id =' . $db->quote($gameId);
        $db->setQuery($query);
        $db->query();
    }

    public function updatePotentialPoints($gameId, $userId, $userPotentialPoints){
        $db = JFactory::getDBO();
        $query =  'Update frcst_forecast set potential_points = '.$db->quote($userPotentialPoints).
            'where user_id =' .$db->quote($userId).
            'and game_id =' . $db->quote($gameId);
        $db->setQuery($query);
        $db->query();
    }

    public function countForecasts($gameId, $lfcGoals, $oppTeamGoals)
    {
        $db = JFactory::getDBO();
        $query1= 'SELECT COUNT(*)
                  FROM frcst_forecast
                  WHERE game_id ='.$db->quote($gameId).'
                  AND lfc_goals ='.$db->quote($lfcGoals).'
                  AND opp_team_goals ='.$db->quote($oppTeamGoals);
        $db->setQuery($query1);
        $pointsDivideOption[0] = $db->loadResult();

        $query2 = 'SELECT COUNT(*)
                   FROM frcst_forecast
                   WHERE game_id = '.$db->quote($gameId).'
                   AND lfc_goals-opp_team_goals = ' . $db->quote($lfcGoals-$oppTeamGoals);
        $db->setQuery($query2);
        $pointsDivideOption[1] = $db->loadResult();

        $query3 = 'SELECT COUNT(*)
                   FROM frcst_forecast
                   WHERE game_id = ' . $db->quote($gameId) . '
                   AND ((lfc_goals-opp_team_goals > 0 AND ' . $db->quote($lfcGoals-$oppTeamGoals). ' > 0)
                   OR (lfc_goals-opp_team_goals < 0 AND ' . $db->quote($lfcGoals-$oppTeamGoals) . ' < 0)
                   OR (lfc_goals-opp_team_goals = 0 AND ' . $db->quote($lfcGoals-$oppTeamGoals) . '=0))';
        $db->setQuery($query3);
        $pointsDivideOption[2] = $db->loadResult();

        return $pointsDivideOption;
    }

    public function getPlayers()
    {
        $db = JFactory::getDBO();
        $query='SELECT user_id FROM frcst_player';
        $db->setQuery($query);
        $players = $db->loadResultArray(0);
        return $players;
    }

    public function getPlayersCount(){
        $db = JFactory::getDBO();
        $query='select count(user_id) from frcst_player';
        $db->setQuery($query);
        $playersCount = $db->loadResult();
        return $playersCount;
    }

    public static function getLastGameId(){
        $db = JFactory::getDBO();
        $query='select * from jos_calendar where c_lasku is not null order by c_date desc limit 1;';
        $db->setQuery($query);
        $gameId = $db->loadAssoc();
        return $gameId;
    }
}

