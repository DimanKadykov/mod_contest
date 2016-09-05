<?php

class ContestPlayer extends ContestDb
{
    public static function isParticipant($userId)
    {
        $db = JFactory::getDBO();
        $query = 'SELECT * FROM frcst_player WHERE user_id = ' . $db->quote($userId);
        $db->setQuery($query);
        $result = $db->loadRow();
        return !empty($result);
    }

    public static function registerParticipant($userId, $contestId)
    {
        $db = JFactory::getDBO();
        $contestPlayer = new stdClass();
        $contestPlayer->contest_id = $contestId;
        $contestPlayer->user_id = $userId;
        $db->insertObject('frcst_player', $contestPlayer);
    }

    public static function getForecasts($participantId, $contest)
    {
        $db = JFactory::getDBO();
        $query = '
          SELECT * FROM frcst_forecast
          WHERE user_id = ' . $db->quote($participantId) . '
          AND contest_id = ' . $db->quote($contest->getId());
        $db->setQuery($query);
        $forecasts = $db->loadAssocList('game_id');
        return $forecasts;
    }
    public static function getForecastsByGameId($gameId)
    {
        $db = JFactory::getDBO();
        $query = '
          SELECT * FROM frcst_forecast
          WHERE game_id = ' . $db->quote($gameId);
        $db->setQuery($query);
        $forecastsByGameId = $db->loadAssocList('user_id');
        return $forecastsByGameId;
    }
    public static function getReport($gameId){
        $db = JFactory::getDBO();
        $query =  'SET @user_place = 0;';
        $db->setQuery($query);
        $db->query();
        $query1 = 'SELECT @user_place :=@user_place + 1 user_place, concat( \'Ливерпуль ФК - \', team.t_name, \' (\', cal.c_where, \')\' ) game_name, CASE WHEN cal.c_where = \'H\' THEN concat( CONVERT (frcst.lfc_goals, CHAR), \'-\', CONVERT (frcst.opp_team_goals, CHAR)) ELSE concat( CONVERT (frcst.opp_team_goals, CHAR), \'-\', CONVERT (frcst.lfc_goals, CHAR)) END forecast, users.username user_name, truncate(frcst.points_gained,2) frcst_points, truncate(frcst.potential_points,2) frcst_potential_points, truncate(summary.points,2) points, truncate(summary.potential_points,2) potential_points FROM frcst_forecast frcst JOIN jos_calendar cal ON frcst.game_id = cal.c_id JOIN jos_teams team ON team.t_id = cal.t_id JOIN jos_users users ON users.id = frcst.user_id JOIN ( SELECT user_id, sum(points_gained) points, sum(potential_points) potential_points FROM frcst_forecast GROUP BY 1 ) AS summary ON summary.user_id = frcst.user_id WHERE frcst.game_id ='.$db->quote($gameId).' ORDER BY points DESC;';
        $db->setQuery($query1);
        $report = $db->loadAssocList('user_place');
        return $report;
    }
    public static function getSeasonForecast($participantId, $contest){
        $db = JFactory::getDBO();
        $query = '
          SELECT * FROM frcst_season_bonus
          WHERE user_id = ' . $db->quote($participantId) . '
          AND contest_id = ' . $db->quote($contest->getId());
        $db->setQuery($query);
        $seasonForecasts = $db->loadAssoc();
        return $seasonForecasts;
    }
}
