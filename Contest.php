<?php

class Contest extends ContestDb
{
    private $id;
    private $name;
    private $seasonId;
    private $isActive;
    private $seasonStart;

    public static function findActive()
    {
        $db = JFactory::getDBO();
        $query = 'SELECT * FROM frcst_contest WHERE is_active=1 LIMIT 1';
        $db->setQuery($query);

        $contest = new self;
        return $contest->fromArray($db->loadAssoc());
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSeasonId()
    {
        return $this->seasonId;
    }

    public function getSeasonStart(){
        return $this->seasonStart;
    }

    public function fromArray($contestData)
    {
        $this->id = $contestData['id'];
        $this->name = $contestData['name'];
        $this->seasonId = $contestData['season_id'];
        $this->isActive = $contestData['is_active'];
        $this->seasonStart = $contestData['season_start'];
        return $this;
    }

    public function getGames()
    {
        $db = JFactory::getDBO();
        $query =
            'SELECT *
             FROM jos_calendar
             INNER JOIN jos_teams ON (jos_calendar.t_id = jos_teams.t_id)
             WHERE c_date >'.$db->quote($this->getSeasonStart()) . '
             ORDER BY c_date ASC';
        $db->setQuery($query);

        return $db->loadAssocList();
    }
}